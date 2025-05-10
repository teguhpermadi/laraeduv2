<?php

namespace App\Console\Commands;

use App\Enums\CategoryLegerEnum;
use App\Models\Leger as ModelsLeger;
use App\Models\LegerNote;
use App\Models\LegerRecap;
use App\Models\TeacherSubject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessLegerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leger:process {teacher_subject_id?} {--time_signature=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memproses data leger untuk semester penuh dan tengah semester';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Tanyakan teacher_subject_id jika tidak disediakan
        $teacherSubjectId = $this->argument('teacher_subject_id');
        if (!$teacherSubjectId) {
            $teacherSubjectId = $this->ask('Masukkan teacher_subject_id yang akan diproses');
            if (!$teacherSubjectId) {
                $this->error('teacher_subject_id diperlukan untuk melanjutkan proses');
                return 1;
            }
        }

        // Tanyakan time_signature jika tidak disediakan
        $timeSignature = $this->option('time_signature');
        if (!$timeSignature) {
            $useCurrentTime = $this->confirm('Apakah Anda ingin menggunakan waktu saat ini untuk time_signature?', true);
            if ($useCurrentTime) {
                $timeSignature = now();
            } else {
                $timeSignature = $this->ask('Masukkan time_signature (format: YYYY-MM-DD HH:MM:SS)', now()->format('Y-m-d H:i:s'));
            }
        }

        $this->info('Memulai proses leger untuk teacher_subject_id: ' . $teacherSubjectId);
        $this->info('Menggunakan time_signature: ' . $timeSignature);

        try {
            // Ambil data teacher_subject
            $teacherSubject = TeacherSubject::with([
                'teacher',
                'subject',
                'academic',
                'competency',
                'studentGrade'
            ])->findOrFail($teacherSubjectId);

            $academicYearId = $teacherSubject->academic->id;
            $teacher_id = $teacherSubject->teacher_id;
            $subject_id = $teacherSubject->subject_id;
            $subjectOrder = $teacherSubject->subject->order;

            // Ambil data competency berdasarkan teacher_subject_id
            $competenciesFullSemester = $teacherSubject->competency;
            $competenciesHalfSemester = $teacherSubject->competency->where('half_semester', true);

            // Proses data untuk full semester
            $studentsFullSemester = $this->processStudentData($teacherSubject, $competenciesFullSemester, $subjectOrder, $teacher_id, $subject_id);
            
            // Proses data untuk half semester
            $studentsHalfSemester = $this->processStudentData($teacherSubject, $competenciesHalfSemester, $subjectOrder, $teacher_id, $subject_id);

            // Simpan data full semester
            $this->info('Menyimpan data leger untuk semester penuh...');
            foreach ($studentsFullSemester as $student) {
                $legerFullSemester = ModelsLeger::updateOrCreate([
                    'academic_year_id' => $academicYearId,
                    'student_id' => $student['student_id'],
                    'teacher_subject_id' => $teacherSubjectId,
                    'teacher_id' => $student['teacher_id'],
                    'subject_id' => $student['subject_id'],
                    'category' => CategoryLegerEnum::FULL_SEMESTER->value,
                ], [
                    'passing_grade' => $student['passing_grade'],
                    'score' => $student['avg_score'],
                    'score_skill' => $student['avg_skill'],
                    'sum' => $student['sum_score'],
                    'sum_skill' => $student['sum_skill'],
                    'rank' => $student['ranking'],
                    'description' => $student['description'],
                    'description_skill' => $student['description_skill'],
                    'metadata' => $student['competencies'],
                    'subject_order' => $student['subject_order'],
                ]);

                // Insert data ke table leger_note
                LegerNote::updateOrCreate([
                    'leger_id' => $legerFullSemester->id,
                ], [
                    'note' => '-',
                ]);
            }

            // Insert data ke table leger_recap untuk full semester
            LegerRecap::updateOrCreate([
                'academic_year_id' => $academicYearId,
                'teacher_subject_id' => $teacherSubjectId,
                'category' => CategoryLegerEnum::FULL_SEMESTER->value,
            ], [
                'updated_at' => $timeSignature,
            ]);

            // Simpan data half semester
            $this->info('Menyimpan data leger untuk tengah semester...');
            foreach ($studentsHalfSemester as $student) {
                $legerHalfSemester = ModelsLeger::updateOrCreate([
                    'academic_year_id' => $academicYearId,
                    'student_id' => $student['student_id'],
                    'teacher_subject_id' => $teacherSubjectId,
                    'teacher_id' => $student['teacher_id'],
                    'subject_id' => $student['subject_id'],
                    'category' => CategoryLegerEnum::HALF_SEMESTER->value,
                ], [
                    'passing_grade' => $student['passing_grade'],
                    'score' => $student['avg_score'],
                    'score_skill' => $student['avg_skill'],
                    'sum' => $student['sum_score'],
                    'sum_skill' => $student['sum_skill'],
                    'rank' => $student['ranking'],
                    'description' => $student['description'],
                    'description_skill' => $student['description_skill'],
                    'metadata' => $student['competencies'],
                    'subject_order' => $student['subject_order'],
                ]);

                // Insert data ke table leger_note
                LegerNote::updateOrCreate([
                    'leger_id' => $legerHalfSemester->id,
                ], [
                    'note' => '-',
                ]);
            }

            // Insert data ke table leger_recap untuk half semester
            LegerRecap::updateOrCreate([
                'academic_year_id' => $academicYearId,
                'teacher_subject_id' => $teacherSubjectId,
                'category' => CategoryLegerEnum::HALF_SEMESTER->value,
            ]);

            $this->info('Proses leger berhasil diselesaikan!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
            Log::error('Leger Process Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Proses data siswa untuk leger
     */
    private function processStudentData($teacherSubject, $competencies, $subjectOrder, $teacher_id, $subject_id)
    {
        // Import helper jika diperlukan
        if (!class_exists('App\Helpers\DescriptionHelper')) {
            $this->error('DescriptionHelper tidak ditemukan');
            throw new \Exception('DescriptionHelper tidak ditemukan');
        }

        $students = $teacherSubject->studentGrade->map(function ($student) use ($competencies, $subjectOrder, $teacher_id, $subject_id) {
            // Buat description dengan description helper
            $description = \App\Helpers\DescriptionHelper::getDescription($student->studentCompetency->whereIn('competency_id', $competencies->pluck('id')));
            $avg_score = $student->studentCompetency->whereIn('competency_id', $competencies->pluck('id'))->avg('score');
            $avg_skill = $student->studentCompetency->whereIn('competency_id', $competencies->pluck('id'))->avg('score_skill');
            $sum_score = $student->studentCompetency->whereIn('competency_id', $competencies->pluck('id'))->sum('score');
            $sum_skill = $student->studentCompetency->whereIn('competency_id', $competencies->pluck('id'))->sum('score_skill');
            // Passing grade adalah rata-rata dari passing grade competency
            $passing_grade = $competencies->avg('passing_grade');

            return [
                'student_id' => $student->student->id,
                'teacher_id' => $teacher_id,
                'subject_id' => $subject_id,
                'subject_order' => $subjectOrder,
                'nis' => $student->student->nis,
                'name' => $student->student->name,
                'avg_score' => round($avg_score, 0),
                'avg_skill' => round($avg_skill, 0),
                'sum_score' => $sum_score,
                'sum_skill' => $sum_skill,
                'description' => $description['description'],
                'description_skill' => $description['description_skill'],
                'competency_count' => $competencies->count(),
                'passing_grade' => round($passing_grade, 0),
                'competencies' => $student->studentCompetency
                    ->whereIn('competency_id', $competencies->pluck('id'))
                    ->sortBy('competency_id')
                    ->map(function ($competency) {
                        return [
                            'competency_id' => $competency->competency_id,
                            'code' => $competency->competency->code,
                            'score' => $competency->score,
                            'passing_grade' => $competency->competency->passing_grade,
                            'description' => $competency->competency->description,
                            'score_skill' => $competency->score_skill,
                            'description_skill' => $competency->competency->description_skill,
                        ];
                    }),
            ];
        });

        // Tambahkan ranking berdasarkan sum_score
        $students = $students->sortByDesc('sum_score')->values();
        $students = $students->map(function ($item, $index) {
            $item['ranking'] = $index + 1;
            return $item;
        });

        // Kembalikan data sort by student_id asc
        return $students->sortBy('student_id', SORT_ASC);
    }
}
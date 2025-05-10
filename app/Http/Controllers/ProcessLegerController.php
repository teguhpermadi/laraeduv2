<?php

namespace App\Http\Controllers;

use App\Enums\CategoryLegerEnum;
use App\Models\Leger as ModelsLeger;
use App\Models\LegerNote;
use App\Models\LegerRecap;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessLegerController extends Controller
{
    /**
     * Memproses data leger untuk semester penuh dan tengah semester
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {
        // Validasi input
        $request->validate([
            'teacher_subject_id' => 'required',
            'time_signature' => 'nullable', // Hapus validasi date_format
        ]);

        $teacherSubjectId = $request->input('teacher_subject_id');
        
        // Perbaikan pengolahan time_signature
        $timeSignature = $request->input('time_signature');
        if (empty($timeSignature)) {
            $timeSignature = now();
        } else {
            // Konversi format datetime-local (YYYY-MM-DDThh:mm) ke format Y-m-d H:i:s
            try {
                $timeSignature = \Carbon\Carbon::parse($timeSignature)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $timeSignature = now();
            }
        }
        
        try {
            // Ambil data teacher_subject
            $teacherSubject = TeacherSubject::with([
                'teacher',
                'subject',
                'academic',
                'competency',
                'studentGrade'
            ])->where('id', $teacherSubjectId)->firstOrFail();

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

            return response()->json([
                'status' => 'success',
                'message' => 'Proses leger berhasil diselesaikan!',
                'data' => [
                    'teacher_subject_id' => $teacherSubjectId,
                    'time_signature' => $timeSignature,
                    'full_semester_count' => count($studentsFullSemester),
                    'half_semester_count' => count($studentsHalfSemester),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Leger Process Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Proses data siswa untuk leger
     */
    private function processStudentData($teacherSubject, $competencies, $subjectOrder, $teacher_id, $subject_id)
    {
        // Import helper jika diperlukan
        if (!class_exists('App\Helpers\DescriptionHelper')) {
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

    /**
     * Tampilkan form untuk memproses leger
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Ambil daftar teacher subject untuk dropdown
        $teacherSubjects = TeacherSubject::with(['teacher', 'subject', 'academic'])
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->teacher->name . ' - ' . $item->subject->code . ' ' . $item->grade->name . ' (' . $item->academic->year . ' '. $item->academic->semester.')',
                ];
            });

        return view('leger.process', [
            'teacherSubjects' => $teacherSubjects,
        ]);
    }
}
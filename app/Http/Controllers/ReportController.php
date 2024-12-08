<?php

namespace App\Http\Controllers;

use App\Enums\CategoryLegerEnum;
use App\Enums\SemesterEnum;
use App\Models\AcademicYear;
use App\Models\LegerRecap;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Settings\SchoolSettings;
use Dompdf\Dompdf;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Element\TextRun;
use App\Enums\LinkertScaleEnum;
use App\Models\LegerQuran;

class ReportController extends Controller
{
    public function getDataCover($id)
    {
        $academicYear = AcademicYear::find(session('academic_year_id'));

        if($academicYear->teacher == null) {
            abort(403, 'Data Kepala Sekolah belum diisi. Hubungi Admin untuk mengisi data tersebut.');
        }

        $student = Student::find($id);

        $data = $this->cover($student);
        return $data;
    }

    public function cover($data)
    {
        $templateProcessor = new TemplateProcessor(storage_path('/app/public/templates/cover.docx'));
        $templateProcessor->setValue('nama', $data['name']);
        $templateProcessor->setValue('nisn', $data['nisn']);
        $templateProcessor->setValue('nis', $data['nis']);

        // generate filename
        $filename = 'Cover ' . $data['name'] . '.docx';
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $templateProcessor->saveAs($file_path);

        // download file
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }

    // get data cover student
    public function getDataCoverStudent($id)
    {
        $academicYear = AcademicYear::find(session('academic_year_id'));

        if($academicYear->teacher == null) {
            abort(403, 'Data Kepala Sekolah belum diisi. Hubungi Admin untuk mengisi data tersebut.');
        }

        $student = Student::with('dataStudent')->find($id);
        $data = $this->coverStudent($student);
        return $data;
    }


    // cover student identity
    public function coverStudent($data)
    {
        // dd($data->toArray());
        if (is_null($data['dataStudent'])) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        $academicYear = AcademicYear::find(session('academic_year_id'));

        $templateProcessor = new TemplateProcessor(storage_path('/app/public/templates/cover-student.docx'));
        $templateProcessor->setValue('nama', ($data['name']) ? $data['name'] : '-');
        $templateProcessor->setValue('nisn', ($data['nisn']) ? $data['nisn'] : '-');
        $templateProcessor->setValue('nis', ($data['nis']) ? $data['nis'] : '-');
        $templateProcessor->setValue('tempat_lahir', ($data['dataStudent']['city_born']) ? $data['dataStudent']['city_born'] : '-');

        $templateProcessor->setValue('tanggal_lahir', ($data['birthday']) ? Carbon::createFromFormat('Y-m-d', $data['birthday'])->locale('id')->translatedFormat('d F Y') : '-');
        $templateProcessor->setValue('jenis_kelamin', ($data['gender']) ? $data['gender'] : '-');
        $templateProcessor->setValue('agama', ($data['dataStudent']['religion']) ? $data['dataStudent']['religion'] : '-');
        $templateProcessor->setValue('pendidikan_sebelumnya', ($data['dataStudent']['previous_school']) ? $data['dataStudent']['previous_school'] : '-');
        $templateProcessor->setValue('alamat', ($data['dataStudent']['student_address']) ? $data['dataStudent']['student_address'] : '-');
        $templateProcessor->setValue('kelurahan', ($data['dataStudent']['student_village']) ? $data['dataStudent']['student_village'] : '-');
        $templateProcessor->setValue('kecamatan', ($data['dataStudent']['student_district']) ? $data['dataStudent']['student_district'] : '-');
        $templateProcessor->setValue('kota', ($data['dataStudent']['student_city']) ? $data['dataStudent']['student_city'] : '-');
        $templateProcessor->setValue('provinsi', ($data['dataStudent']['student_province']) ? $data['dataStudent']['student_province'] : '-');

        // ayah
        $templateProcessor->setValue('nama_ayah', ($data['dataStudent']['father_name']) ? $data['dataStudent']['father_name'] : '-');
        $templateProcessor->setValue('pendidikan_ayah', ($data['dataStudent']['father_education']) ? $data['dataStudent']['father_education'] : '-');
        $templateProcessor->setValue('pekerjaan_ayah', ($data['dataStudent']['father_occupation']) ? $data['dataStudent']['father_occupation'] : '-');
        
        // ibu
        $templateProcessor->setValue('nama_ibu', ($data['dataStudent']['mother_name']) ? $data['dataStudent']['mother_name'] : '-');
        $templateProcessor->setValue('pendidikan_ibu', ($data['dataStudent']['mother_education']) ? $data['dataStudent']['mother_education'] : '-');
        $templateProcessor->setValue('pekerjaan_ibu', ($data['dataStudent']['mother_occupation']) ? $data['dataStudent']['mother_occupation'] : '-');

        // alamat
        $templateProcessor->setValue('alamat_orangtua', ($data['dataStudent']['parent_address']) ? $data['dataStudent']['parent_address'] : '-');
        $templateProcessor->setValue('kelurahan_orangtua', ($data['dataStudent']['parent_village']) ? $data['dataStudent']['parent_village'] : '-');
        $templateProcessor->setValue('kecamatan_orangtua', ($data['dataStudent']['parent_district']) ? $data['dataStudent']['parent_district'] : '-');
        $templateProcessor->setValue('kota_orangtua', ($data['dataStudent']['parent_city']) ? $data['dataStudent']['parent_city'] : '-');
        $templateProcessor->setValue('provinsi_orangtua', ($data['dataStudent']['parent_province']) ? $data['dataStudent']['parent_province'] : '-');

        // wali
        $templateProcessor->setValue('nama_wali', ($data['dataStudent']['guardian_name']) ? $data['dataStudent']['guardian_name'] : '-');
        $templateProcessor->setValue('pendidikan_wali', ($data['dataStudent']['guardian_education']) ? $data['dataStudent']['guardian_education'] : '-');
        $templateProcessor->setValue('pekerjaan_wali', ($data['dataStudent']['guardian_occupation']) ? $data['dataStudent']['guardian_occupation'] : '-');
        $templateProcessor->setValue('alamat_wali', ($data['dataStudent']['guardian_address']) ? $data['dataStudent']['guardian_address'] : '-');

        // tanda tangan
        $templateProcessor->setValue('date_received', ($data['dataStudent']['date_received']) ? Carbon::createFromFormat('Y-m-d', $data['dataStudent']['date_received'])->locale('id')->translatedFormat('d F Y') : '-');
        $templateProcessor->setValue('headmaster', ($academicYear->teacher->name) ? $academicYear->teacher->name : '-');



        $filename = 'Identitas ' . $data['name'] . ' - ' . $academicYear->semester . '.docx';
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $templateProcessor->saveAs($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }

    public function halfSemester($id)
    {
        $academic = session('academic_year_id');

        // get academic year
        $academicYear = AcademicYear::with('teacher')->find($academic);

        if($academicYear->teacher == null) {
            abort(403, 'Data Kepala Sekolah belum diisi. Hubungi Admin untuk mengisi data tersebut.');
        }

        // get category
        $category = CategoryLegerEnum::HALF_SEMESTER->value;

        $student = Student::with([
            'leger' => function ($query) use ($academic, $category) {
                $query->where('academic_year_id', $academic);
                $query->where('category', $category);
            },
            'studentGradeFirst.grade.teacherGradeFirst',
            'leger.teacherSubject.subject',
            'legerQuran',
            'attitudeFirst',
            'attendanceFirst',
            'extracurricular',
        ])
            ->find($id);

        // dd($student->leger->toArray());

        $report = $this->getHalfReport($academicYear, $student);

        return $report;
    }

    public function getHalfReport($academic, $student)
    {
        // add competencies
        $leger = $student->leger;

        if ($leger->isEmpty()) {
            abort(403, 'Data leger tidak ditemukan');
        }

        if($student->attendanceFirst == null){
            abort(403, 'Data kehadiran belum diisi.');
        }

        $schoolSettings = app(SchoolSettings::class);

        $templateProcessor = new TemplateProcessor(storage_path('/app/public/templates/reportHalf.docx'));

        $templateProcessor->setValue('school_name', $schoolSettings->school_name);
        $templateProcessor->setValue('school_address', $schoolSettings->school_address);
        $templateProcessor->setValue('headmaster', $academic->teacher->name);
        $templateProcessor->setValue('date_report_half', Carbon::createFromFormat('Y-m-d', $academic->date_report_half)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('year', $academic->year);
        $templateProcessor->setValue('semester', $academic->semester);

        $templateProcessor->setValue('student_name', $student->name);
        $templateProcessor->setValue('nisn', $student->nisn);
        $templateProcessor->setValue('nis', $student->nis);

        $templateProcessor->setValue('grade_name', $student->studentGradeFirst->grade->name);
        $templateProcessor->setValue('grade_level', $student->studentGradeFirst->grade->phase);

        $templateProcessor->setValue('sick', $student->attendanceFirst->sick . "\u{200B}");
        $templateProcessor->setValue('permission', $student->attendanceFirst->permission . "\u{200B}");
        $templateProcessor->setValue('absent', $student->attendanceFirst->absent . "\u{200B}");

        $templateProcessor->setValue('teacher_name', $student->studentGradeFirst->grade->teacherGradeFirst->teacher->name);

        // setting mata pelajaran
        $data = [];
        $numRow = 1;
        $subjects = $student->leger;

        // dd($subjects->toArray());

        foreach ($subjects as $subject) {
            $data[] = [
                'order' => $numRow++,
                'subject' => $subject->teacherSubject->subject->name,
                'score' => $subject->score,
                'passing_grade' => $subject->teacherSubject->passing_grade,
                'description' => $subject->description,
                'criteria' => $subject->teacherSubject->getScoreCriteria($subject->score),
            ];
        }

        // dd($data);
        // tabel nilai mata pelajaran
        $templateProcessor->cloneRowAndSetValues('order', $data);

        // tambahkan data detail
        $dataDetail = [];
        foreach ($subjects as $key => $subject) {
            $i = 1;
            $note = $subject->note;
            // Tambahkan grup data (judul blok)
            $dataDetail[] = [
                'leger_subject' => $subject->teacherSubject->subject->name,
                'passing_grade' => $subject->teacherSubject->passing_grade,
                'score' => $subject->score,
                'data_tabel' => $subject->metadata,
                'criteria' => $subject->teacherSubject->getScoreCriteria($subject->score),
                'subject_note' => ($note) ? $note->note : '-',
            ];
        }

        // dd($dataDetail);

        # Block cloning
        $replacements = [];
        $i = 1;
        foreach ($dataDetail as $index => $detail) {
            $replacements[] = [
                'leger_subject' => $i++ . '. ' . $detail['leger_subject'],
                'order_subject' => '${order_subject_' . $index . '}',
                'competency' => '${competency_' . $index . '}',
                'score' => '${score_' . $index . '}',
                'passing_grade' => '${passing_grade_' . $index . '}',
                'avg_score' => $detail['score'],
                'avg_passing_grade' => $detail['passing_grade'],
                'criteria' => $detail['criteria'],
                'subject_note' => $detail['subject_note'],
            ];
        }

        $templateProcessor->cloneBlock('block_name', count($replacements), true, false, $replacements);

        # Table row cloning
        foreach ($dataDetail as $index => $detail) {
            $values = [];
            $order = 1;

            $score = $detail['score'];
            $passing_grade = $detail['passing_grade'];

            foreach ($detail['data_tabel'] as $row) {

                // dd($row);
                $values[] = [
                    "order_subject_{$index}" => $order++,
                    "competency_{$index}" => $row['description'],
                    "score_{$index}" => $row['score'],
                    "passing_grade_{$index}" => $row['passing_grade'] ?? '-', // Default nilai jika tidak ada passing grade
                ];
            }

            $templateProcessor->cloneRowAndSetValues("order_subject_{$index}", $values);
        }


        // dd($dataDetail, $data);
        $filename = 'Rapor Tengah Semester ' . $student->name . ' - ' . str_replace('/', ' ', $academic->year) . ' ' . $academic->semester . '.docx';
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $templateProcessor->saveAs($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true);; // <<< HERE
    }

    public function fullSemester($id)
    {
        $academic = session('academic_year_id');

        // get academic year
        $academicYear = AcademicYear::find($academic);

        if($academicYear->teacher == null) {
            abort(403, 'Data Kepala Sekolah belum diisi. Hubungi Admin untuk mengisi data tersebut.');
        }

        // get category
        $category = CategoryLegerEnum::FULL_SEMESTER->value;

        $student = Student::with([
            'leger' => function ($query) use ($academic, $category) {
                $query->where('academic_year_id', $academic);
                $query->where('category', $category);
            },
            'studentGradeFirst.grade.teacherGradeFirst',
            'leger.teacherSubject.subject',
            'legerQuran',
            'attitudeFirst',
            'attendanceFirst',
            'extracurricular' => function ($query) use ($academic) {
                $query->orderBy('extracurricular_id', 'asc');
            },
        ])
            ->find($id);

        $report = $this->getFullReport($academicYear, $student);

        return $report;
    }

    public function getFullReport($academic, $student)
    {
        // add competencies
        $leger = $student->leger;

        if ($leger->isEmpty()) {
            abort(403, 'Data leger tidak ditemukan');
        }

        if($student->attendanceFirst == null){
            abort(403, 'Data kehadiran belum diisi.');
        }

        $schoolSettings = app(SchoolSettings::class);

        $templateProcessor = new TemplateProcessor(storage_path('/app/public/templates/reportFull.docx'));

        $templateProcessor->setValue('school_name', $schoolSettings->school_name);
        $templateProcessor->setValue('school_address', $schoolSettings->school_address);
        $templateProcessor->setValue('headmaster', $academic->teacher->name);
        $templateProcessor->setValue('date_report', Carbon::createFromFormat('Y-m-d', $academic->date_report)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('year', $academic->year);
        $templateProcessor->setValue('semester', $academic->semester);

        $templateProcessor->setValue('student_name', $student->name);
        $templateProcessor->setValue('nisn', $student->nisn);
        $templateProcessor->setValue('nis', $student->nis);

        $templateProcessor->setValue('grade_name', $student->studentGradeFirst->grade->name);
        $templateProcessor->setValue('grade_level', $student->studentGradeFirst->grade->phase);

        $templateProcessor->setValue('sick', ($student->attendanceFirst->sick == 0) ? '-' : $student->attendanceFirst->sick);
        $templateProcessor->setValue('permission', ($student->attendanceFirst->permission == 0) ? '-' : $student->attendanceFirst->permission);
        $templateProcessor->setValue('absent', ($student->attendanceFirst->absent == 0) ? '-' : $student->attendanceFirst->absent);
        $templateProcessor->setValue('note', $student->attendanceFirst->note ?? '-');
        $templateProcessor->setValue('achievement', $student->attendanceFirst->achievement ?? '-');

        $templateProcessor->setValue('teacher_name', $student->studentGradeFirst->grade->teacherGradeFirst->teacher->name);

        // jika semester ganjil
        if ($academic->semester === SemesterEnum::GANJIL->value) {
            $templateProcessor->cloneBlock('block_status', 0, true, false, null);
        } else {
            $templateProcessor->cloneBlock('block_status', 1, true, false, null);
            
            $description = '';
            // next grade dari grade saat ini
            $nowGrade = $student->studentGradeFirst->grade->grade;
            $nextGrade = $nowGrade + 1;

            switch ($student->attendanceFirst->status) {
                case 1:
                    $description = 'Berdasarkan pencapaian seluruh kompetensi, ananda ' . $student->name . ' dinyatakan NAIK KELAS dan dapat melanjutkan ke jenjang berikutnya.';
                    break;

                case 0:
                    $description = 'Berdasarkan pencapaian seluruh kompetensi, ananda ' . $student->name . ' dinyatakan TIDAK NAIK KELAS dan tetap berada jenjang sekarang.';
                    break;
                    
                default:
                    $description = '-';
                    break;
            } 

            $templateProcessor->setValue('status', $description ?? '-');
        }

        // setting mata pelajaran
        $data = [];
        $numRow = 1;
        $subjects = $student->leger;

        foreach ($subjects as $subject) {
            $data[] = [
                'order' => $numRow++,
                'subject' => $subject->teacherSubject->subject->name,
                'score' => $subject->score,
                'passing_grade' => $subject->teacherSubject->passing_grade,
                'description' => $subject->description,
                'criteria' => $subject->teacherSubject->getScoreCriteria($subject->score),
            ];
        }

        // dd($data);
        // tabel nilai mata pelajaran
        $templateProcessor->cloneRowAndSetValues('order', $data);


        // setting nilai extrakurricular
        $extracurriculars = $student->extracurricular;
        $numRowExtra = 1;
        $dataExtra = [];

        foreach ($extracurriculars as $extracurricular) {
            $extraScoreEnum = LinkertScaleEnum::tryFrom($extracurricular->score);
            $extraScoreLabel = $extraScoreEnum ? $extraScoreEnum->getLabel() : 'Tidak Diketahui';

            $dataExtra[] = [
                'orderExtra' => $numRowExtra++,
                'extra_name' => $extracurricular->extracurricular->name,
                'extra_score' => $extraScoreLabel, // Menggunakan label dari LinkertScaleEnum
                'optional' => ($extracurricular->extracurricular->is_required == 1) ? 'Wajib' : 'Pilihan',
            ];
        }

        // table extrakurricular
        $templateProcessor->cloneRowAndSetValues('orderExtra', $dataExtra);

        // tambahkan data detail
        $dataDetail = [];
        foreach ($subjects as $key => $subject) {
            $i = 1;
            $note = $subject->note;
            // Tambahkan grup data (judul blok)
            $dataDetail[] = [
                'leger_subject' => $subject->teacherSubject->subject->name,
                'passing_grade' => $subject->teacherSubject->passing_grade,
                'score' => $subject->score,
                'data_tabel' => $subject->metadata,
                'criteria' => $subject->teacherSubject->getScoreCriteria($subject->score),
                'subject_note' => ($note) ? $note->note : '-',
            ];
        }

        // dd($dataDetail);

        # Block cloning
        $replacements = [];
        $i = 1;
        foreach ($dataDetail as $index => $detail) {
            $replacements[] = [
                'leger_subject' => $i++ . '. ' . $detail['leger_subject'],
                'order_subject' => '${order_subject_' . $index . '}',
                'competency' => '${competency_' . $index . '}',
                'score' => '${score_' . $index . '}',
                'passing_grade' => '${passing_grade_' . $index . '}',
                'avg_score' => $detail['score'],
                'avg_passing_grade' => $detail['passing_grade'],
                'subject_note' => $detail['subject_note'],
            ];
        }

        $templateProcessor->cloneBlock('block_name', count($replacements), true, false, $replacements);

        # Table row cloning
        foreach ($dataDetail as $index => $detail) {
            $values = [];
            $order = 1;

            $score = $detail['score'];
            $passing_grade = $detail['passing_grade'];

            foreach ($detail['data_tabel'] as $row) {

                // dd($row);
                $values[] = [
                    "order_subject_{$index}" => $order++,
                    "competency_{$index}" => $row['description'],
                    "score_{$index}" => $row['score'],
                    "passing_grade_{$index}" => $row['passing_grade'] ?? '-', // Default nilai jika tidak ada passing grade
                ];
            }

            $templateProcessor->cloneRowAndSetValues("order_subject_{$index}", $values);
        }


        // dd($dataDetail, $data);
        $filename = 'Rapor Akhir Semester ' . $student->name . ' - ' . str_replace('/', ' ', $academic->year) . ' ' . $academic->semester . '.docx';
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $templateProcessor->saveAs($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true);; // <<< HERE
    }

    public function project($id)
    {
        $academic = session('academic_year_id');

        $academicYear = AcademicYear::find($academic);

        if($academicYear->teacher == null) {
            abort(403, 'Data Kepala Sekolah belum diisi. Hubungi Admin untuk mengisi data tersebut.');
        }

        $student = Student::with([
            'studentGradeFirst.grade.teacherGradeFirst',
            'studentGradeFirst.project.projectTarget.studentProject' => function ($query) use ($id) {
                $query->where('student_id', $id);
            },
            'studentGradeFirst.project.note' => function ($query) use ($id) {
                $query->where('student_id', $id);
            },
        ])
            ->find($id);

        // dd($student->toArray());

        $report = $this->getProjectReport($academicYear, $student);

        return $report;
    }

    public function getProjectReport($academic, $student)
    {
        // dd($student->toArray());
        if ($student->studentGradeFirst->project->isEmpty()) {
            abort(403, 'Data project tidak ditemukan');
        }

        // dd($student->studentGradeFirst->project->toArray());

        // dd($academic, $student);
        $schoolSettings = app(SchoolSettings::class);

        $templateProcessor = new TemplateProcessor(storage_path('/app/public/templates/project.docx'));

        $templateProcessor->setValue('school_name', $schoolSettings->school_name);
        $templateProcessor->setValue('school_address', $schoolSettings->school_address);
        $templateProcessor->setValue('headmaster', $academic->teacher->name);
        $templateProcessor->setValue('date_report', Carbon::createFromFormat('Y-m-d', $academic->date_report)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('year', $academic->year);
        $templateProcessor->setValue('semester', $academic->semester);

        $templateProcessor->setValue('student_name', $student->name);
        $templateProcessor->setValue('nisn', $student->nisn);
        $templateProcessor->setValue('nis', $student->nis);

        $templateProcessor->setValue('grade_name', $student->studentGradeFirst->grade->name);
        $templateProcessor->setValue('grade_level', $student->studentGradeFirst->grade->phase);
        $templateProcessor->setValue('teacher_name', $student->studentGradeFirst->grade->teacherGradeFirst->teacher->name);

        // get all project
        $projects = $student->studentGradeFirst->project;
        // dd(count($projects));

        // block cloning
        $replacements = [];
        $i = 1;
        foreach ($projects as $index => $project) {
            $replacements[] = [
                'project_number' => '${project_number_' . $i . '}',
                'title' => '${title_' . $i . '}',
                'description' => '${description_' . $i . '}',
                'number_target' => '${number_target_' . $i . '}',
                'dimention_description' => '${dimention_description_' . $i . '}',
                'element_description' => '${element_description_' . $i . '}',
                'value_description' => '${value_description_' . $i . '}',
                'sub_value_description' => '${sub_value_description_' . $i . '}',
                'target_description' => '${target_description_' . $i . '}',
                'bsb' => '${bsb_' . $i . '}',
                'bsh' => '${bsh_' . $i . '}',
                'mb' => '${mb_' . $i . '}',
                'bb' => '${bb_' . $i . '}',
                'project_note' => '${project_note_' . $i . '}',
            ];
            $i++;
        }

        $templateProcessor->cloneBlock('project_block', count($projects), true, false, $replacements);

        // dd($projects->toArray());

        // setiap project
        
        foreach ($projects as $index => $project) {
            $j = $index + 1;
            // dd($project->toArray());
            $values = [];
            // set table project
            $projectTargets = $project->projectTarget;
            $k = 1;
            $note = '';
            foreach ($projectTargets as $target) {
                $score = $target->studentProject->first()->score;
                // dd($target->toArray());

                $values[] = [
                    "number_target_{$j}" => $k++,
                    "dimention_description_{$j}" => $target->dimention->description,
                    "element_description_{$j}" => $target->element->description,
                    "value_description_{$j}" => $target->value->description,
                    "sub_value_description_{$j}" => $target->subValue->description,
                    "target_description_{$j}" => $target->target->description,
                    "bsb_{$j}" => ($score == 1) ? 'V' : '-',
                    "bsh_{$j}" => ($score == 2) ? 'V' : '-',
                    "mb_{$j}" => ($score == 3) ? 'V' : '-',
                    "bb_{$j}" => ($score == 4) ? 'V' : '-',
                    // "project_note_{$j}" => $target->studentProject->first()->projectNote->note,
                ];                    
            }
            
            $note = $project->note->first();
            // dd($note->toArray());
            
            $templateProcessor->setValue("project_number_{$j}", $j);
            $templateProcessor->setValue("title_{$j}", $project->name);
            $templateProcessor->setValue("description_{$j}", $project->description);

            $templateProcessor->cloneRowAndSetValues("number_target_{$j}", $values);
            $templateProcessor->setValue("project_note_{$j}", ($note) ? $note->note : '-');
            
        }

        // generate filename
        $filename = 'Rapor Proyek ' . $student->name . ' - ' . str_replace('/', ' ', $academic->year) . ' ' . $academic->semester . '.docx';
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $templateProcessor->saveAs($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true);; // <<< HERE
    }

    public function quran($id)
    {
        $academic = session('academic_year_id');

        $academicYear = AcademicYear::find($academic);

        if($academicYear->teacher == null) {
            abort(403, 'Data Kepala Sekolah belum diisi. Hubungi Admin untuk mengisi data tersebut.');
        }

        $student = LegerQuran::where('student_id', $id)
            ->where('academic_year_id', $academic)
            ->with('quranGrade.teacherQuranGrade')
            ->first();

        $report = $this->getQuranReport($academicYear, $student);

        return $report;
    }

    public function getQuranReport($academicYear, $student)
    {
        if (!$student) {
            abort(403, 'Data quran tidak ditemukan');
        }

        // dd($student->toArray());

        $schoolSettings = app(SchoolSettings::class);

        // template processor
        $templateProcessor = new TemplateProcessor(storage_path('/app/public/templates/reportQuran.docx'));

        $templateProcessor->setValue('school_name', $schoolSettings->school_name);
        $templateProcessor->setValue('school_address', $schoolSettings->school_address);
        $templateProcessor->setValue('headmaster', $academicYear->teacher->name);
        $templateProcessor->setValue('date_report', Carbon::createFromFormat('Y-m-d', $academicYear->date_report)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('year', $academicYear->year);
        $templateProcessor->setValue('semester', $academicYear->semester);

        $templateProcessor->setValue('student_name', $student->student->name);
        $templateProcessor->setValue('nisn', $student->student->nisn);
        $templateProcessor->setValue('nis', $student->student->nis);

        $templateProcessor->setValue('grade_name', $student->student->studentGradeFirst->grade->name);
        $templateProcessor->setValue('grade_level', $student->student->studentGradeFirst->grade->phase);

        $templateProcessor->setValue('quran_grade', $student->quranGrade->name);
        $templateProcessor->setValue('teacher_quran_grade', $student->quranGrade->teacherQuranGrade->first()->teacher->name);

        $templateProcessor->setValue('note', $student->quranNote->note);

        // setting competency quran
        $competencyQuran = $student->metadata;

        // dd($competencyQuran);
        $i = 1;
        $data = [];
        foreach ($competencyQuran as $key => $value) {
            $data[] = [
                'quran_order' => $i++,
                'quran_competency' => $value['description'],
                'score' => $value['score'],
                'criteria' => $student->quranGrade->teacherQuranGrade->first()->getScoreCriteria($value['score']),
            ];
        }

        $templateProcessor->cloneRowAndSetValues('quran_order', $data);

        // save
        $filename = 'Rapor Quran ' . $student->student->name . ' - ' . str_replace('/', ' ', $academicYear->year) . ' ' . $academicYear->semester . '.docx';
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $templateProcessor->saveAs($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true);; // <<< HERE
    }
}

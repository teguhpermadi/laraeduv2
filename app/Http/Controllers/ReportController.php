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

class ReportController extends Controller
{
    public function getDataCover($id)
    {
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
        $student = Student::with('dataStudent')->find($id);
        $data = $this->coverStudent($student);
        return $data;
    }


    // cover student identity
    public function coverStudent($data)
    {
        // dd($data);
        if (is_null($data['dataStudent'])) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        $templateProcessor = new TemplateProcessor(storage_path('/app/public/templates/cover-student.docx'));
        $templateProcessor->setValue('nama', $data['name']);
        $templateProcessor->setValue('nisn', $data['nisn']);
        $templateProcessor->setValue('nis', $data['nis']);
        $templateProcessor->setValue('tempat_lahir', $data['city_born']);

        $templateProcessor->setValue('tanggal_lahir', Carbon::createFromFormat('Y-m-d', $data['birthday'])->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('jenis_kelamin', $data['gender']);
        $templateProcessor->setValue('agama', $data['dataStudent']['religion']);
        $templateProcessor->setValue('pendidikan_sebelumnya', $data['previous_school']);
        $templateProcessor->setValue('alamat', $data['student_address']);
        $templateProcessor->setValue('kelurahan', $data['student_village']);
        $templateProcessor->setValue('kecamatan', $data['student_district']);
        $templateProcessor->setValue('kota', $data['student_city']);
        $templateProcessor->setValue('provinsi', $data['student_province']);

        // ayah
        $templateProcessor->setValue('nama_ayah', $data['dataStudent']['father_name']);
        $templateProcessor->setValue('pendidikan_ayah', $data['dataStudent']['father_education']);
        $templateProcessor->setValue('pekerjaan_ayah', $data['dataStudent']['father_occupation']);
        // ibu
        $templateProcessor->setValue('nama_ibu', $data['dataStudent']['mother_name']);
        $templateProcessor->setValue('pendidikan_ibu', $data['dataStudent']['mother_education']);
        $templateProcessor->setValue('pekerjaan_ibu', $data['dataStudent']['mother_occupation']);

        // alamat
        $templateProcessor->setValue('alamat_orangtua', $data['dataStudent']['parent_address']);
        $templateProcessor->setValue('kelurahan_orangtua', $data['dataStudent']['parent_village']);
        $templateProcessor->setValue('kecamatan_orangtua', $data['dataStudent']['parent_district']);
        $templateProcessor->setValue('kota_orangtua', $data['dataStudent']['parent_city']);
        $templateProcessor->setValue('provinsi_orangtua', $data['dataStudent']['parent_province']);

        // tanda tangan
        $templateProcessor->setValue('date_received', Carbon::createFromFormat('Y-m-d', $data['dataStudent']['date_received'])->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('headmaster', $data['academic']['teacher']['name']);

        $filename = 'Identitas ' . $data['student']['name'] . ' - ' . $data['academic']['semester'] . '.docx';
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $templateProcessor->saveAs($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }

    public function halfSemester($id)
    {
        $academic = session('academic_year_id');

        // get academic year
        $academicYear = AcademicYear::with('teacher')->find($academic);

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
        $templateProcessor->setValue('grade_level', $student->studentGradeFirst->grade->grade);

        $templateProcessor->setValue('sick', $student->attendanceFirst->sick . "\u{200B}");
        $templateProcessor->setValue('permission', $student->attendanceFirst->permission . "\u{200B}");
        $templateProcessor->setValue('absent', $student->attendanceFirst->absent . "\u{200B}");

        $templateProcessor->setValue('teacher_name', $student->studentGradeFirst->grade->teacherGradeFirst->teacher->name);

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

        // tambahkan data detail
        $dataDetail = [];
        foreach ($subjects as $key => $subject) {
            $i = 1;

            // Tambahkan grup data (judul blok)
            $dataDetail[] = [
                'leger_subject' => $subject->teacherSubject->subject->name,
                'passing_grade' => $subject->teacherSubject->passing_grade,
                'score' => $subject->score,
                'data_tabel' => $subject->metadata,
                'criteria' => $subject->teacherSubject->getScoreCriteria($subject->score),
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
                    "competency_{$index}" => $row['competency']['description'],
                    "score_{$index}" => $row['score'],
                    "passing_grade_{$index}" => $row['competency']['passing_grade'] ?? '-', // Default nilai jika tidak ada passing grade
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
        $templateProcessor->setValue('grade_level', $student->studentGradeFirst->grade->grade);

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
            $templateProcessor->setValue('status', $student->attendanceFirst->status ?? '-');
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
        // dd($extracurriculars->toArray());
        $numRowExtra = 1;
        $dataExtra = [];

        foreach ($extracurriculars as $extracurricular) {
            $dataExtra[] = [
                'orderExtra' => $numRowExtra++,
                'extra_name' => $extracurricular->extracurricular->name,
                'extra_score' => $extracurricular->score,
                'optional' => ($extracurricular->extracurricular->is_required == 1) ? 'Wajib' : 'Pilihan',
            ];
        }

        // table extrakurricular
        $templateProcessor->cloneRowAndSetValues('orderExtra', $dataExtra);

        // tambahkan data detail
        $dataDetail = [];
        foreach ($subjects as $key => $subject) {
            $i = 1;

            // Tambahkan grup data (judul blok)
            $dataDetail[] = [
                'leger_subject' => $subject->teacherSubject->subject->name,
                'passing_grade' => $subject->teacherSubject->passing_grade,
                'score' => $subject->score,
                'data_tabel' => $subject->metadata,
                'criteria' => $subject->teacherSubject->getScoreCriteria($subject->score),
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
                    "competency_{$index}" => $row['competency']['description'],
                    "score_{$index}" => $row['score'],
                    "passing_grade_{$index}" => $row['competency']['passing_grade'] ?? '-', // Default nilai jika tidak ada passing grade
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

        $student = Student::with([
            'studentGradeFirst.grade.teacherGradeFirst',
            'studentGradeFirst.project.projectTarget.studentProject' => function ($query) use ($id) {
                $query->where('student_id', $id)->first();
            },
        ])
            ->find($id);

        // dd($student->toArray());

        $report = $this->getProjectReport($academicYear, $student);

        return $report;
    }

    public function getProjectReport($academic, $student)
    {
        if ($student->project->isEmpty()) {
            abort(403, 'Data project tidak ditemukan');
        }

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
        $templateProcessor->setValue('grade_level', $student->studentGradeFirst->grade->grade);
        $templateProcessor->setValue('teacher_name', $student->studentGradeFirst->grade->teacherGradeFirst->teacher->name);

        // get all project
        $projects = $student->project;

        // generate filename
        $filename = 'Rapor Proyek ' . $student->name . ' - ' . str_replace('/', ' ', $academic->year) . ' ' . $academic->semester . '.docx';
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $templateProcessor->saveAs($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true);; // <<< HERE
    }
}

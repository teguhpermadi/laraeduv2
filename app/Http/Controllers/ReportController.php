<?php

namespace App\Http\Controllers;

use App\Enums\CategoryLegerEnum;
use App\Models\AcademicYear;
use App\Models\LegerRecap;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentGrade;
use Dompdf\Dompdf;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

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
        if(is_null($data['dataStudent'])){
            abort(404, 'Data siswa tidak ditemukan');
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
        $academicYear = AcademicYear::find($academic);

        // get category
        $category = CategoryLegerEnum::HALF_SEMESTER->value;

        $student = Student::with(['leger' => function ($query) use ($academic, $category) {
                                $query->where('academic_year_id', $academic);
                                $query->where('category', $category);
                            }, 
                            'leger.teacherSubject.subject',
                            'legerQuran',
                            'attitude',
                            'attendance',
                            'extracurricular'
                            ])
                            ->find($id);

        $data = [
            'student' => $student,
            'academic' => $academicYear,
        ];

        // $report = $this->getHalfReport($data);
        
        return $data;
    }

    public function getHalfReport($data)
    {
        $templateProcessor = new TemplateProcessor( storage_path('/app/public/templates/reportHalf.docx'));
        $templateProcessor->setValue('school_name',$data['school']['name']);
        $templateProcessor->setValue('school_address',$data['school']['address']);
        $templateProcessor->setValue('headmaster',$data['headmaster']);
        $templateProcessor->setValue('date_report_half',$data['academic']['date_report_half']);
        $templateProcessor->setValue('year',$data['academic']['year']);
        $templateProcessor->setValue('semester',$data['academic']['semester']);
        $templateProcessor->setValue('student_name',$data['student']['name']);
        $templateProcessor->setValue('nisn',$data['student']['nisn']);
        $templateProcessor->setValue('nis',$data['student']['nis']);
        $templateProcessor->setValue('grade_name',$data['grade']['name']);
        $templateProcessor->setValue('grade_level',$data['grade']['grade']);
        $templateProcessor->setValue('sick',$data['attendance']['sick']);
        $templateProcessor->setValue('permission',$data['attendance']['permission']);
        $templateProcessor->setValue('absent',$data['attendance']['absent']);
        $templateProcessor->setValue('total_attendance',$data['attendance']['total_attendance']);
        $templateProcessor->setValue('teacher_name',$data['teacher']['name']);
    }

    public function fullSemester($id)
    {
        $academic = session('academic_year_id');

        // get academic year
        $academicYear = AcademicYear::find($academic);

        // get category
        $category = CategoryLegerEnum::FULL_SEMESTER->value;

        $student = Student::with(['leger' => function ($query) use ($academic, $category) {
                                $query->where('academic_year_id', $academic);
                                $query->where('category', $category);
                            }, 
                            'leger.teacherSubject.subject',
                            'legerQuran',
                            'attitude',
                            'attendance',
                            'extracurricular'
                            ])
                            ->find($id);

        return [$student, $academicYear];
    }
}

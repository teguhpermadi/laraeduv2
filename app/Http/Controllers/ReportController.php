<?php

namespace App\Http\Controllers;

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
        $templateProcessor = new TemplateProcessor( storage_path('/app/public/templates/cover.docx'));
        $templateProcessor->setValue('nama',$data['name']);
        $templateProcessor->setValue('nisn',$data['nisn']);
        $templateProcessor->setValue('nis',$data['nis']);

        // generate filename
        $filename = 'Cover '.$data['name'] .'.docx';
        $file_path = storage_path('/app/public/downloads/'.$filename);
        $templateProcessor->saveAs($file_path);

        // download file
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }

    // get data cover student
    public function getDataCoverStudent($id)
    {
        $student = Student::find($id);
        $data = $this->coverStudent($student);
        return $data;
    }   


    // cover student identity
    public function coverStudent($data)
    {
        $templateProcessor = new TemplateProcessor( storage_path('/app/public/templates/cover-student.docx'));
        $templateProcessor->setValue('nama',$data['student']['name']);
        $templateProcessor->setValue('nisn',$data['student']['nisn']);
        $templateProcessor->setValue('nis',$data['student']['nis']);
        $templateProcessor->setValue('tempat_lahir',$data['student']['city_born']);
        
        $templateProcessor->setValue('tanggal_lahir', Carbon::createFromFormat('Y-m-d', $data['student']['birthday'])->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('jenis_kelamin',$data['student']['gender']);
        $templateProcessor->setValue('agama',$data['student']['dataStudent']['religion']);
        $templateProcessor->setValue('pendidikan_sebelumnya',$data['student']['dataStudent']['previous_school']);
        $templateProcessor->setValue('alamat',$data['student']['dataStudent']['student_address']);
        $templateProcessor->setValue('kelurahan',$data['student']['dataStudent']['student_village']);
        $templateProcessor->setValue('kecamatan',$data['student']['dataStudent']['student_district']);
        $templateProcessor->setValue('kota',$data['student']['dataStudent']['student_city']);
        $templateProcessor->setValue('provinsi',$data['student']['dataStudent']['student_province']);
        
        // ayah
        $templateProcessor->setValue('nama_ayah',$data['student']['dataStudent']['father_name']);
        $templateProcessor->setValue('pendidikan_ayah',$data['student']['dataStudent']['father_education']);
        $templateProcessor->setValue('pekerjaan_ayah',$data['student']['dataStudent']['father_occupation']);
        // ibu
        $templateProcessor->setValue('nama_ibu',$data['student']['dataStudent']['mother_name']);
        $templateProcessor->setValue('pendidikan_ibu',$data['student']['dataStudent']['mother_education']);
        $templateProcessor->setValue('pekerjaan_ibu',$data['student']['dataStudent']['mother_occupation']);
        
        // alamat
        $templateProcessor->setValue('alamat_orangtua',$data['student']['dataStudent']['parent_address']);
        $templateProcessor->setValue('kelurahan_orangtua',$data['student']['dataStudent']['parent_village']);
        $templateProcessor->setValue('kecamatan_orangtua',$data['student']['dataStudent']['parent_district']);
        $templateProcessor->setValue('kota_orangtua',$data['student']['dataStudent']['parent_city']);
        $templateProcessor->setValue('provinsi_orangtua',$data['student']['dataStudent']['parent_province']);
        
        // tanda tangan
        $templateProcessor->setValue('date_received', Carbon::createFromFormat('Y-m-d', $data['student']['dataStudent']['date_received'])->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('headmaster',$data['academic']['teacher']['name']);

        $filename = 'Identitas '.$data['student']['name'].' - '. $data['academic']['semester'] .'.docx';
        $file_path = storage_path('/app/public/downloads/'.$filename);
        $templateProcessor->saveAs($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }

    public function calculateHalfSemester($id)
    {
        // get data academic
        $academic = session('academic_year_id');
        
        // get data student
        $studentGrade = StudentGrade::where('student_id', $id)->first();

        // get data score from legerrecap
        $score = LegerRecap::where('student_id', $id)->where('academic_year_id', $academic)->first();
        
        // $data = $this->halfSemester($student, $academic);
        // return $data;
    }

    public function halfSemester($data)
    {
        
    }
}

<?php

namespace App\Imports;

use App\Models\DataStudent;
use App\Models\Student;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentImport implements ToCollection, WithHeadingRow
{
    use Importable, SkipsErrors;
    /**,
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            try {
                $student = Student::updateOrCreate([
                    'nisn' => $row['nisn'],
                    'nis' => $row['nis'],
                ],[
                    'nisn' => $row['nisn'],
                    'nis' => $row['nis'],
                    'name' => $row['nama_lengkap'],
                    'gender' => Str::lower($row['jenis_kelamin']),
                    'city_born' => $row['tempat_lahir'],
                    'birthday' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d'),
                    'nick_name' => $row['nama_panggilan'],
                ]);
    
                DataStudent::updateOrCreate([
                    'student_id' => $student->id,
                ],[
                    'student_id' => $student->id,
                    'student_address' => $row['alamat_siswa'],
                    'student_province'=> $row['provinsi_siswa'],
                    'student_city'=> $row['kota_siswa'],
                    'student_district'=> $row['kecamatan_siswa'],
                    'student_village'=> $row['kelurahan_siswa'],
                    'religion' => $row['agama'],
                    'previous_school' => $row['asal_sekolah'],
                    'date_received' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_diterima'])->format('Y-m-d'),
                    'grade_received' => $row['diterima_kelas'],
                    'father_name' => $row['nama_ayah'],
                    'father_education' => $row['pendidikan_ayah'],
                    'father_occupation' => $row['pekerjaan_ayah'],
                    'father_phone' => $row['telp_ayah'],
                    'mother_name' => $row['nama_ibu'],
                    'mother_education' => $row['pendidikan_ibu'],
                    'mother_occupation' => $row['pekerjaan_ibu'],
                    'mother_phone' => $row['telp_ibu'],
                    'guardian_name' => $row['nama_wali'],
                    'guardian_education' => $row['pendidikan_wali'],
                    'guardian_occupation' => $row['pekerjaan_wali'],
                    'guardian_phone' => $row['telp_wali'],
                    'guardian_address' => $row['alamat_wali'],
                    // 'guardian_village' => $row['kelurahan_wali'],
                    'parent_address' => $row['alamat_orangtua'],
                    'parent_village' => $row['kelurahan_orangtua'],
                    'parent_address' => $row['alamat_orangtua'],
                    'parent_province'=> $row['provinsi_orangtua'],
                    'parent_city'=> $row['kota_orangtua'],
                    'parent_district'=> $row['kecamatan_orangtua'],
                    'parent_village'=> $row['kelurahan_orangtua'],
                ]);
            } catch (\Exception  $e) {
                //throw $th;
                session()->push('import_errors', [
                    'row' => $row,
                    'error' => $e->getMessage(),
                ]);
            }
            
        }
    }
}

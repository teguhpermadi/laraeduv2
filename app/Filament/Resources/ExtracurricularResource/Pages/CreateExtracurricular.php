<?php

namespace App\Filament\Resources\ExtracurricularResource\Pages;

use App\Filament\Resources\ExtracurricularResource;
use App\Models\Student;
use App\Models\StudentExtracurricular;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateExtracurricular extends CreateRecord
{
    protected static string $resource = ExtracurricularResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // buat dulu extracurricular
        $extracurricular = static::getModel()::create($data);

        // jika is_required true maka tambahkan semua data siswa ke table student_extracurricular
        if ($data['is_required']) {
            $students = Student::all();
            foreach ($students as $student) {
                StudentExtracurricular::create(['student_id' => $student->id, 'extracurricular_id' => $extracurricular->id]);
            }
        }
        
        return $extracurricular;
    }
}

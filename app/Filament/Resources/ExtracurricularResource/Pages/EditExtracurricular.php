<?php

namespace App\Filament\Resources\ExtracurricularResource\Pages;

use App\Filament\Resources\ExtracurricularResource;
use App\Models\Student;
use App\Models\StudentExtracurricular;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditExtracurricular extends EditRecord
{
    protected static string $resource = ExtracurricularResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // jika is_required true maka tambahkan semua data siswa ke table student_extracurricular
        if ($data['is_required']) {
            $students = Student::all();
            foreach ($students as $student) {
                StudentExtracurricular::create([
                    'academic_year_id' => session('academic_year_id'),
                    'student_id' => $student->id, 
                    'extracurricular_id' => $record->id
                ]);
            }
        } else {
            // jika is_required false maka hapus semua data siswa dari table student_extracurricular
            StudentExtracurricular::where('extracurricular_id', $record->id)->delete();
        }

        $record->update($data);
 
        return $record;
    }

    // redirec ke halaman edit extracurricular
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}

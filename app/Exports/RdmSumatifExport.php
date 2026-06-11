<?php

namespace App\Exports;

use App\Enums\CategoryLegerEnum;
use App\Models\Competency;
use App\Models\TeacherSubject;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RdmSumatifExport implements WithMultipleSheets
{
    use Exportable;

    protected $teacher_subject_id;
    protected TeacherSubject $teacherSubject;
    protected $competencies;

    public function __construct($teacher_subject_id)
    {
        $this->teacher_subject_id = $teacher_subject_id;
        $this->teacherSubject     = TeacherSubject::with(['grade', 'subject'])->find($teacher_subject_id);

        // Hanya ambil kompetensi dengan kode full_semester (Akhir Semester)
        $this->competencies = Competency::where('teacher_subject_id', $teacher_subject_id)
            ->where('code', CategoryLegerEnum::FULL_SEMESTER->value)
            ->get();
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->competencies as $index => $competency) {
            $sheets[] = new RdmSheetExport(
                $this->teacherSubject,
                $competency,
                $index + 1
            );
        }

        return $sheets;
    }
}

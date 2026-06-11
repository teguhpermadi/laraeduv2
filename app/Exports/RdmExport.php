<?php

namespace App\Exports;

use App\Enums\CategoryLegerEnum;
use App\Models\Competency;
use App\Models\StudentCompetency;
use App\Models\TeacherSubject;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RdmExport implements WithMultipleSheets
{
    use Exportable;

    protected $teacher_subject_id;
    protected $teacherSubject;
    protected $competencies;

    public function __construct($teacher_subject_id)
    {
        $this->teacher_subject_id = $teacher_subject_id;
        $this->teacherSubject = TeacherSubject::with(['grade', 'subject'])->find($teacher_subject_id);
        $excludedCodes = array_column(CategoryLegerEnum::cases(), 'value');

        $this->competencies = Competency::where('teacher_subject_id', $teacher_subject_id)
            ->whereNotIn('code', $excludedCodes)
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

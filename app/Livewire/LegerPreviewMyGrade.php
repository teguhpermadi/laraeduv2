<?php

namespace App\Livewire;

use App\Models\Leger;
use App\Models\Student;
use App\Models\TeacherGrade;
use App\Models\TeacherSubject;
use Livewire\Component;

class LegerPreviewMyGrade extends Component
{
    public $myGrade, $subjects;
    public $data;

    public function mount()
    {
        $myGrade = TeacherGrade::myGrade()->mySubject()->get();
        
        $this->myGrade = $myGrade;

        $data = [];

        foreach ($myGrade as $grade) { // setiap grade


            $no = 1;
            foreach ($grade->studentGrade as $student) { // setiap student

                $data[$grade->id][$student->student_id] = [
                    'no' => $no++,
                    'student' => $student->student->toArray(),
                ];

                foreach ($grade->grade->teacherSubject as $subject) { // setiap subject
                    $leger = $student->student->leger()
                        ->where('category', 'full_semester')
                        ->where('subject_id', $subject->subject_id)
                        ->first();

                    if ($leger) {
                        $data[$grade->id][$student->student_id]['leger'][] = $leger->toArray();
                    } else {
                        $data[$grade->id][$student->student_id]['leger'][] = null;
                    }
                }

                $legers = $student->student->leger()
                    ->where('category', 'full_semester')
                    ->get();

                // buatkan jumlah dari setiap nilai
                $data[$grade->id][$student->student_id]['total'] = $legers->sum('score');
                // buatkan rata-rata dari setiap nilai
                $data[$grade->id][$student->student_id]['average'] = $legers->avg('score');
                // buatkan ranking dari setiap nilai
            }

            // pada setiap grade, urutkan berdasarkan total score
            $data[$grade->id] = collect($data[$grade->id])->sortByDesc('total')->values();

            // pada setiap grade, buatkan ranking berdasarkan total score
            $data[$grade->id] = $data[$grade->id]->map(function ($item, $index) {
                $item['ranking'] = $index + 1;
                return $item;
            });

            // kembalikan data berdasarkan student_id asc
            $data[$grade->id] = collect($data[$grade->id])->sortBy('student.id')->values();

        }

        // dd($data);
        $this->data = $data;
    }

    public function render()
    {
        return view('livewire.leger-preview-my-grade');
    }
}

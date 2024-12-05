<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Userable;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $teacher = Teacher::create($data);

        // userable
        $name = $data['name'];
        $userable_id = $teacher->id;
        $userable_type = Teacher::class;

        $user = User::firstOrCreate(
            [
                'email' => Str::replace(' ', '', $name) . '@laraedu.com',
            ],
            [
                'name' => $name,
                'username' => fake()->numerify(Str::replace(' ', '', $name) . '_##'),
                'email' => Str::replace(' ', '', $name) . '@laraedu.com',
                'password' => Hash::make('password'),
            ]
        );

        $userable = Userable::firstOrCreate(
            [
                'userable_id' => $userable_id,
                'userable_type' => $userable_type,
            ],
            [
                'user_id' => $user->id,
                'userable_id' => $userable_id,
                'userable_type' => $userable_type
            ]
        );

        return $teacher;
    }
}

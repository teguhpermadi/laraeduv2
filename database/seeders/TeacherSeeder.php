<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Userable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Teacher::factory(2)->create();

        // get all teachers
        $teachers = Teacher::all();

        foreach ($teachers as $teacher) {
            $name = $teacher->name;
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
        }
    }
}

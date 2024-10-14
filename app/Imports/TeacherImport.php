<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Userable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeacherImport implements ToCollection, WithHeadingRow
{
    use Importable, SkipsErrors;
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // cek password
                if (!is_null($row['password'])) {
                    $pass = Hash::make($row['password']);
                } else {
                    $pass = Hash::make('password');
                }

                // create user
                $user = User::updateOrCreate([
                    'email' => Str::slug($row['nama_lengkap']) . '@teacher.com',
                ], [
                    'name' => $row['nama_lengkap'],
                    'username' => $row['username'],
                    'email' => Str::slug($row['nama_lengkap']) . '@teacher.com',
                    'password' => $pass,
                ]);

                // create teacher
                $teacher = Teacher::updateOrCreate([
                    'name' => $row['nama_lengkap'],
                ], [
                    'name' => $row['nama_lengkap'],
                    'gender' => Str::lower($row['jenis_kelamin']),
                ]);

                // create userable
                Userable::updateOrCreate([
                    'user_id' => $user->id,
                ], [
                    'user_id' => $user->id,
                    'userable_id' => $teacher->id,
                    'userable_type' => Teacher::class,
                ]);

                $user->assignRole('teacher');
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

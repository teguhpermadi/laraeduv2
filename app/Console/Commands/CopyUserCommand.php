<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Userable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy user from laraedu to laraeduv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = DB::connection('laraedu')->table('users')->get();

        foreach ($users as $user) {
            $array = json_decode(json_encode($user), true);

            $data = [
                'id' => $array['ulid'],
                'name' => $array['name'],
                'username' => $array['username'],
                'email' => $array['email'],
                'password' => $array['password'],
                // 'avatar_url',
                // 'last_activity',
            ];

            try {
                User::create($data);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }

        // userable
        $userables = DB::connection('laraedu')->table('userables')->get();

        foreach ($userables as $userable) {
            $arrayUserble = json_decode(json_encode($userable), true);

            $dataUserable = [
                'id' => $arrayUserble['ulid'],
                'user_id' => $arrayUserble['user_ulid'],
                'userable_id' => $arrayUserble['userable_ulid'],
                'userable_type' => $arrayUserble['userable_type'],
            ];

            // Tambahkan log untuk memeriksa data
            // Log::info('Data Userable:', $dataUserable);

            try {
                Userable::create($dataUserable);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
    }
}

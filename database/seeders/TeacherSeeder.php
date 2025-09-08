<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class TeacherSeeder extends Seeder
{
    public function run()
    {
        // List of teachers to seed
        $teachers = [
            [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'john@school.com',
                'password'   => bcrypt('password'),
                'role'       => 'teacher',
                'status'     => 'active'
            ],
            [
                'first_name' => 'Jane',
                'last_name'  => 'Smith',
                'email'      => 'jane@school.com',
                'password'   => bcrypt('password'),
                'role'       => 'teacher',
                'status'     => 'active'
            ],
        ];

        foreach ($teachers as $teacher) {
            // firstOrCreate avoids duplicate email issues
            Account::firstOrCreate(
                ['email' => $teacher['email']],
                $teacher
            );
        }
    }
}

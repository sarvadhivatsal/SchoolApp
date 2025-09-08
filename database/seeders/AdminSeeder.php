<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Account::create([
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'admin@school.com',
            'password'   => Hash::make('password123'),
            'role'       => 'admin',
            'status'     => 'active'
        ]);
    }
}

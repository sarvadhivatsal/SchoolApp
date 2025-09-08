<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // \Log::info('Row imported:', $row);
        // Validate each row before inserting
        $validator = Validator::make($row, [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:accounts,email',
            'password'   => 'required|string|min:6',
            'status'     => 'required|in:active,inactive',
            'dob'        => 'nullable|date',
            'phone'      => 'nullable|string|max:15',
            'gender'     => 'nullable|in:male,female,other',
            'address'    => 'nullable|string|max:255',
            'city'       => 'nullable|string|max:255',
            'state'      => 'nullable|string|max:255',
            'zipcode'    => 'nullable|string|max:20',
        ]);

        // if ($validator->fails()) {
        //     // Instead of failing silently, log error row
        //     \Log::error("Teacher Import Error", [
        //         'row' => $row,
        //         'errors' => $validator->errors()->all(),
        //     ]);
        //     return null; // skip bad rows
        // }

        // ✅ Create account
        $account = Account::create([
            'first_name' => $row['first_name'],
            'last_name'  => $row['last_name'],
            'email'      => $row['email'],
            'password'   => Hash::make($row['password']),
            'role'       => 'Teacher',
            'status'     => $row['status'],
        ]);

        // ✅ Create teacher linked to account
        return new Teacher([
            'account_id' => $account->id,
            'dob'        => $row['dob'] ?? null,
            'phone'      => $row['phone'] ?? null,
            'gender'     => $row['gender'] ?? null,
            'address'    => $row['address'] ?? null,
            'city'       => $row['city'] ?? null,
            'state'      => $row['state'] ?? null,
            'zipcode'    => $row['zipcode'] ?? null,
        ]);
    }
}

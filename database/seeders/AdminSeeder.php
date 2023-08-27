<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin=Employee::create([
            'firstName' => 'admin',
            'lastName' => '',
            'middleName' => '',
            'role' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12341234'),
            'phoneNum' => '0945224753',
            'accountStatus'=>1
        ]);
        $admin->assignRole('Admin');
    }
}

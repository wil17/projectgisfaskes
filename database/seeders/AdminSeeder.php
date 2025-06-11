<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'username' => 'admin',
            'email' => 'adminsigfaskes@gmail.com',
            'password' => Hash::make('cobalagi'),
        ]);

        // Optional: Create additional admin users
        Admin::create([
            'username' => 'admin2',
            'email' => 'admin2faskes@gmail.com',
            'password' => Hash::make('tryagain'),
        ]);
    }
}
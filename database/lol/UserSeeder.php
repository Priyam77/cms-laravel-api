<?php

namespace Database\Seeders;
// database/seeders/UserSeeder.php
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
{
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin'
    ]);

    User::create([
        'name' => 'Author User',
        'email' => 'author@example.com',
        'password' => Hash::make('password'),
        'role' => 'author'
    ]);
}
}

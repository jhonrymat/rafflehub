<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Elizabeth',
            'email' => 'elizabeth@rifa.com',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Yasmin',
            'email' => 'yasmin@rifa.com',
            'password' => Hash::make('123456789'),
        ]);
    }
}

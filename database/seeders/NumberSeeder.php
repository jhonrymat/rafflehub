<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Number;

class NumberSeeder extends Seeder
{
    public function run(): void
    {
        // Crear números del 00 al 99
        for ($i = 0; $i <= 99; $i++) {
            Number::create([
                'number' => str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'disponible',
            ]);
        }
    }
}

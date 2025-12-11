<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RaffleConfig;

class RaffleConfigSeeder extends Seeder
{
    public function run(): void
    {
        RaffleConfig::create([
            'prize_name' => 'Novillo ó su valor en efectivo',
            'prize_description' => 'Un novillo de alta calidad o su equivalente en dinero en efectivo.',
            'ticket_price' => 50000.00,
            // raffle_date: fecha del sorteo = 11 de febrero de 2026
            'raffle_date' => '2026-02-11',
            'sale_start_date' => now(),
            'sale_end_date' => now()->addMonths(2)->subDays(11),
            'lottery_method' => 'Últimas 2 cifras de la Lotería del Meta',
            'status' => 'active',
            'contact_info' => 'Elizabeth Perez: +573222572825, Yasmin Vargas: +573223718874',
        ]);
    }
}

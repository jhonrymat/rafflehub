<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaffleConfig extends Model
{
    protected $fillable = [
        'prize_name',
        'prize_description',
        'prize_image',
        'ticket_price',
        'raffle_date',
        'sale_start_date',
        'sale_end_date',
        'lottery_method',
        'winning_number',
        'status',
        'terms_and_conditions',
        'contact_info',
    ];

    protected $casts = [
        'raffle_date' => 'date',
        'sale_start_date' => 'date',
        'sale_end_date' => 'date',
        'ticket_price' => 'decimal:2',
    ];

    // Obtener la configuración actual (solo debe haber una)
    public static function current()
    {
        return self::where('status', 'active')->first();
    }
}

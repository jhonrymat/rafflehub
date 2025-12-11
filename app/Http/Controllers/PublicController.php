<?php

namespace App\Http\Controllers;

use App\Models\Number;
use Illuminate\View\View;
use App\Models\RaffleConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PublicController extends Controller
{
    public function index(): View
    {
        $config = RaffleConfig::current();
        $numbers = Number::orderBy('number')->get();

        // Estadísticas
        $totalNumbers = $numbers->count();
        $soldNumbers = $numbers->where('status', 'vendido')->count();
        $availableNumbers = $numbers->where('status', 'disponible')->count();
        $percentageSold = $totalNumbers > 0 ? round(($soldNumbers / $totalNumbers) * 100, 2) : 0;

        return view('public.home', compact('config', 'numbers', 'totalNumbers', 'soldNumbers', 'availableNumbers', 'percentageSold'));
    }

    // API para actualización automática
    public function numbersStatus(): JsonResponse
    {
        // Cache de 10 segundos para evitar sobrecarga
        $numbers = Cache::remember('raffle_numbers', 10, function () {
            return Number::orderBy('number')->get(['id', 'number', 'status']);
        });

        $stats = Cache::remember('raffle_stats', 10, function () {
            $sold = Number::where('status', 'vendido')->count();
            $total = Number::count();

            return [
                'sold' => $sold,
                'available' => $total - $sold,
                'percentage' => $total > 0 ? round(($sold / $total) * 100, 2) : 0,
            ];
        });

        return response()->json([
            'numbers' => $numbers,
            'stats' => $stats,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}

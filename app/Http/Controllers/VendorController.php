<?php

namespace App\Http\Controllers;

use App\Models\Number;
use App\Models\Cliente;
use App\Models\Payment;
use App\Models\RaffleConfig;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * Mostrar el dashboard principal del vendedor
     */
    public function dashboard(): View
    {
        $config = RaffleConfig::current();
        $user = auth()->user();

        // Estadísticas generales de la rifa
        $totalNumbers = Number::count();
        $soldNumbers = Number::where('status', 'vendido')->count();
        $availableNumbers = Number::where('status', 'disponible')->count();
        $percentageSold = $totalNumbers > 0 ? round(($soldNumbers / $totalNumbers) * 100, 2) : 0;

        // Estadísticas del vendedor actual
        $mySales = Number::where('sold_by', $user->id)->count();
        $myClientes = Cliente::where('user_id', $user->id)->count();

        // Ingresos totales y del vendedor
        $totalRevenue = Cliente::sum('total_paid');
        $myRevenue = Cliente::where('user_id', $user->id)->sum('total_paid');

        // Ingresos esperados
        $ticketPrice = $config ? $config->ticket_price : 50000;
        $expectedTotalRevenue = $soldNumbers * $ticketPrice;
        $expectedMyRevenue = $mySales * $ticketPrice;

        // Clientes del vendedor con pagos pendientes o abonos
        $pendingClientes = Cliente::where('user_id', $user->id)
            ->whereIn('payment_status', ['pendiente', 'abono'])
            ->with('number')
            ->orderBy('payment_status', 'asc') // Pendientes primero
            ->orderBy('created_at', 'desc')
            ->get();

        // Ventas recientes del usuario (últimas 10)
        $recentSales = Number::where('sold_by', $user->id)
            ->with('cliente')
            ->orderBy('sold_at', 'desc')
            ->limit(10)
            ->get();

        // Top vendedores (para motivación)
        $topVendors = DB::table('numbers')
            ->join('users', 'numbers.sold_by', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(*) as total_sales'))
            ->where('numbers.status', 'vendido')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();

        // Resumen de pagos del vendedor
        $myPaymentStats = [
            'fully_paid' => Cliente::where('user_id', $user->id)
                ->where('payment_status', 'pagado')
                ->count(),
            'partial_paid' => Cliente::where('user_id', $user->id)
                ->where('payment_status', 'abono')
                ->count(),
            'pending' => Cliente::where('user_id', $user->id)
                ->where('payment_status', 'pendiente')
                ->count(),
        ];

        return view('vendor.dashboard', compact(
            'config',
            'totalNumbers',
            'soldNumbers',
            'availableNumbers',
            'percentageSold',
            'mySales',
            'myClientes',
            'totalRevenue',
            'myRevenue',
            'expectedTotalRevenue',
            'expectedMyRevenue',
            'pendingClientes',
            'recentSales',
            'topVendors',
            'myPaymentStats'
        ));
    }

    /**
     * Mostrar reportes generales (todos los vendedores pueden ver)
     */
    public function reports(): View
    {
        $config = RaffleConfig::current();
        $user = auth()->user();

        // Todos los números vendidos con información completa
        $soldNumbers = Number::where('status', 'vendido')
            ->with(['cliente.payments', 'soldBy'])
            ->orderBy('number')
            ->get();

        // Resumen de ventas
        $totalSales = Number::where('status', 'vendido')->count();
        $totalRevenue = Cliente::sum('total_paid');

        $ticketPrice = $config ? $config->ticket_price : 50000;
        $expectedRevenue = $totalSales * $ticketPrice;
        $pendingAmount = $expectedRevenue - $totalRevenue;

        // Pagos por estado
        $fullyPaid = Cliente::where('payment_status', 'pagado')->count();
        $partialPaid = Cliente::where('payment_status', 'abono')->count();
        $pending = Cliente::where('payment_status', 'pendiente')->count();

        // Estadísticas por vendedor
        $vendorStats = DB::table('numbers')
            ->join('users', 'numbers.sold_by', '=', 'users.id')
            ->leftJoin('clientes', 'numbers.id', '=', 'clientes.number_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(DISTINCT numbers.id) as total_sales'),
                DB::raw('COALESCE(SUM(clientes.total_paid), 0) as total_revenue'),
                DB::raw('SUM(CASE WHEN clientes.payment_status = "pagado" THEN 1 ELSE 0 END) as paid_count'),
                DB::raw('SUM(CASE WHEN clientes.payment_status = "abono" THEN 1 ELSE 0 END) as partial_count'),
                DB::raw('SUM(CASE WHEN clientes.payment_status = "pendiente" THEN 1 ELSE 0 END) as pending_count')
            )
            ->where('numbers.status', 'vendido')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_sales', 'desc')
            ->get();

        // Pagos por método
        $paymentsByMethod = Payment::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Ventas por día (últimos 30 días)
        $salesByDay = Number::where('status', 'vendido')
            ->where('sold_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(sold_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Clientes con saldo pendiente mayor
        $highDebtClientes = Cliente::whereIn('payment_status', ['pendiente', 'abono'])
            ->with(['number', 'user'])
            ->get()
            ->map(function ($cliente) {
                $cliente->pending_balance = $cliente->pendingBalance();
                return $cliente;
            })
            ->sortByDesc('pending_balance')
            ->take(10);

        return view('vendor.reports', compact(
            'config',
            'soldNumbers',
            'totalSales',
            'totalRevenue',
            'expectedRevenue',
            'pendingAmount',
            'fullyPaid',
            'partialPaid',
            'pending',
            'vendorStats',
            'paymentsByMethod',
            'salesByDay',
            'highDebtClientes'
        ));
    }

    /**
     * Exportar reportes a CSV (opcional)
     */
    public function exportReport(Request $request)
    {
        $type = $request->get('type', 'general');

        $filename = 'reporte_rifa_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8 (para que Excel lo lea correctamente)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($type === 'general') {
                // Encabezados
                fputcsv($file, ['Número', 'Cliente', 'Teléfono', 'Email', 'Vendedor', 'Pagado', 'Pendiente', 'Estado', 'Fecha Venta']);

                // Datos
                $soldNumbers = Number::where('status', 'vendido')
                    ->with(['cliente', 'soldBy'])
                    ->orderBy('number')
                    ->get();

                foreach ($soldNumbers as $number) {
                    fputcsv($file, [
                        $number->number,
                        $number->cliente->name ?? 'N/A',
                        $number->cliente->phone ?? 'N/A',
                        $number->cliente->email ?? 'N/A',
                        $number->soldBy->name,
                        number_format($number->cliente->total_paid ?? 0, 2),
                        number_format($number->cliente ? $number->cliente->pendingBalance() : 0, 2),
                        $number->cliente->payment_status ?? 'N/A',
                        $number->sold_at->format('Y-m-d H:i:s'),
                    ]);
                }
            } elseif ($type === 'payments') {
                // Reporte de pagos
                fputcsv($file, ['Número', 'Cliente', 'Monto', 'Método', 'Fecha', 'Registrado por', 'Notas']);

                $payments = Payment::with(['cliente.number', 'user'])
                    ->orderBy('payment_date', 'desc')
                    ->get();

                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->cliente->number->number ?? 'N/A',
                        $payment->cliente->name,
                        number_format($payment->amount, 2),
                        $payment->payment_method,
                        $payment->payment_date->format('Y-m-d'),
                        $payment->user->name,
                        $payment->notes ?? '',
                    ]);
                }
            } elseif ($type === 'vendors') {
                // Reporte por vendedor
                fputcsv($file, ['Vendedor', 'Total Ventas', 'Ingresos', 'Pagados', 'Abonos', 'Pendientes']);

                $vendorStats = DB::table('numbers')
                    ->join('users', 'numbers.sold_by', '=', 'users.id')
                    ->leftJoin('clientes', 'numbers.id', '=', 'clientes.number_id')
                    ->select(
                        'users.name',
                        DB::raw('COUNT(DISTINCT numbers.id) as total_sales'),
                        DB::raw('COALESCE(SUM(clientes.total_paid), 0) as total_revenue'),
                        DB::raw('SUM(CASE WHEN clientes.payment_status = "pagado" THEN 1 ELSE 0 END) as paid_count'),
                        DB::raw('SUM(CASE WHEN clientes.payment_status = "abono" THEN 1 ELSE 0 END) as partial_count'),
                        DB::raw('SUM(CASE WHEN clientes.payment_status = "pendiente" THEN 1 ELSE 0 END) as pending_count')
                    )
                    ->where('numbers.status', 'vendido')
                    ->groupBy('users.id', 'users.name')
                    ->orderBy('total_sales', 'desc')
                    ->get();

                foreach ($vendorStats as $stat) {
                    fputcsv($file, [
                        $stat->name,
                        $stat->total_sales,
                        number_format($stat->total_revenue, 2),
                        $stat->paid_count,
                        $stat->partial_count,
                        $stat->pending_count,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Mis ventas del día (para revisión rápida)
     */
    public function todaySales(): View
    {
        $user = auth()->user();

        $todaySales = Number::where('sold_by', $user->id)
            ->whereDate('sold_at', today())
            ->with('cliente')
            ->orderBy('sold_at', 'desc')
            ->get();

        $todayRevenue = Cliente::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('total_paid');

        return view('vendor.today-sales', compact('todaySales', 'todayRevenue'));
    }
}

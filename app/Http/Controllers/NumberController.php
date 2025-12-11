<?php

namespace App\Http\Controllers;

use App\Models\Number;
use App\Models\Cliente;
use App\Models\Payment;
use Illuminate\View\View;
use App\Models\RaffleConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class NumberController extends Controller
{
    public function index(): View
    {
        $config = RaffleConfig::current();
        $numbers = Number::with(['cliente', 'soldBy'])
            ->orderBy('number')
            ->get();

        return view('vendor.numbers.index', compact('config', 'numbers'));
    }

    public function show(Number $number): View
    {
        $number->load(['cliente.payments', 'soldBy', 'payments']);
        $config = RaffleConfig::current();

        return view('vendor.numbers.show', compact('number', 'config'));
    }

    public function sell(Request $request, Number $number): RedirectResponse
    {
        // Validar que el número esté disponible
        if ($number->status !== 'disponible') {
            return back()->with('error', 'Este número ya está vendido.');
        }

        $config = RaffleConfig::current();
        $ticketPrice = $config ? $config->ticket_price : 50000;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'payment_amount' => 'required|numeric|min:0|max:' . $ticketPrice,
            'payment_method' => 'required|in:efectivo,transferencia,nequi,daviplata,otro',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Actualizar el número
            $number->update([
                'status' => 'vendido',
                'sold_by' => auth()->id(),
                'sold_at' => now(),
            ]);

            // Determinar estado de pago
            $paymentStatus = 'pendiente';
            if ($validated['payment_amount'] >= $ticketPrice) {
                $paymentStatus = 'pagado';
            } elseif ($validated['payment_amount'] > 0) {
                $paymentStatus = 'abono';
            }

            // Crear el cliente
            $cliente = Cliente::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'number_id' => $number->id,
                'user_id' => auth()->id(),
                'payment_status' => $paymentStatus,
                'total_paid' => $validated['payment_amount'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Registrar el primer pago si hay monto
            if ($validated['payment_amount'] > 0) {
                Payment::create([
                    'cliente_id' => $cliente->id,
                    'number_id' => $number->id,
                    'user_id' => auth()->id(),
                    'amount' => $validated['payment_amount'],
                    'payment_date' => $validated['payment_date'],
                    'payment_method' => $validated['payment_method'],
                    'notes' => 'Pago inicial',
                ]);
            }

            DB::commit();

            return redirect()->route('numbers.show', $number)
                ->with('success', 'Número vendido exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al vender el número: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Number $number): RedirectResponse
    {
        // Solo el vendedor que vendió el número puede actualizarlo
        if ($number->sold_by !== auth()->id()) {
            return back()->with('error', 'No tienes permiso para editar este número.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $number->update($validated);

        return back()->with('success', 'Número actualizado exitosamente.');
    }
}

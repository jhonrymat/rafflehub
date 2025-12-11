<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Cliente;
use App\Models\RaffleConfig;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Registrar un nuevo pago para un cliente
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:efectivo,transferencia,nequi,daviplata,otro',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cliente = Cliente::findOrFail($validated['cliente_id']);

        // Verificar que el vendedor tenga permiso para registrar pagos
        if ($cliente->user_id !== auth()->id()) {
            return back()->with('error', 'No tienes permiso para registrar pagos de este cliente.');
        }

        $config = RaffleConfig::current();
        $ticketPrice = $config ? $config->ticket_price : 50000;

        // Verificar que no exceda el precio del boleto
        $newTotal = $cliente->total_paid + $validated['amount'];
        if ($newTotal > $ticketPrice) {
            return back()->with('error', 'El monto excede el precio del boleto. Saldo pendiente: $' . number_format($cliente->pendingBalance(), 0, ',', '.'));
        }

        DB::beginTransaction();

        try {
            // Crear el pago
            Payment::create([
                'cliente_id' => $cliente->id,
                'number_id' => $cliente->number_id,
                'user_id' => auth()->id(),
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Actualizar total pagado del cliente
            $cliente->total_paid = $newTotal;

            // Actualizar estado del pago
            if ($newTotal >= $ticketPrice) {
                $cliente->payment_status = 'pagado';
            } elseif ($newTotal > 0) {
                $cliente->payment_status = 'abono';
            }

            $cliente->save();

            DB::commit();

            return back()->with('success', 'Pago registrado exitosamente. Total pagado: $' . number_format($newTotal, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar el historial de pagos de un cliente
     */
    public function index(Cliente $cliente): View
    {
        // Verificar permiso - todos los vendedores pueden ver
        // pero si quieres restringir solo al vendedor que vendió:
        // if ($cliente->user_id !== auth()->id()) {
        //     abort(403, 'No tienes permiso para ver los pagos de este cliente.');
        // }

        $payments = $cliente->payments()
            ->with('user')
            ->orderBy('payment_date', 'desc')
            ->get();

        $config = RaffleConfig::current();

        return view('vendor.payments.index', compact('cliente', 'payments', 'config'));
    }

    /**
     * Eliminar un pago (opcional - solo el vendedor que lo registró)
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        // Verificar que el usuario que registró el pago sea quien lo elimina
        if ($payment->user_id !== auth()->id()) {
            return back()->with('error', 'No tienes permiso para eliminar este pago.');
        }

        DB::beginTransaction();

        try {
            $cliente = $payment->cliente;
            $amount = $payment->amount;

            // Eliminar el pago
            $payment->delete();

            // Actualizar el total pagado del cliente
            $cliente->total_paid -= $amount;

            // Actualizar estado de pago
            $config = RaffleConfig::current();
            $ticketPrice = $config ? $config->ticket_price : 50000;

            if ($cliente->total_paid >= $ticketPrice) {
                $cliente->payment_status = 'pagado';
            } elseif ($cliente->total_paid > 0) {
                $cliente->payment_status = 'abono';
            } else {
                $cliente->payment_status = 'pendiente';
            }

            $cliente->save();

            DB::commit();

            return back()->with('success', 'Pago eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Obtener resumen de pagos (para API o estadísticas)
     */
    public function summary(): array
    {
        $config = RaffleConfig::current();
        $ticketPrice = $config ? $config->ticket_price : 50000;

        $totalPayments = Payment::sum('amount');
        $totalClientes = Cliente::count();
        $expectedRevenue = $totalClientes * $ticketPrice;
        $pendingRevenue = $expectedRevenue - $totalPayments;

        $paymentsByMethod = Payment::select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->payment_method => $item->total];
            });

        return [
            'total_payments' => $totalPayments,
            'total_clientes' => $totalClientes,
            'expected_revenue' => $expectedRevenue,
            'pending_revenue' => $pendingRevenue,
            'payments_by_method' => $paymentsByMethod,
        ];
    }
}

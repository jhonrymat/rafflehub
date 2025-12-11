<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\RaffleConfig;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::with(['number', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.clientes.index', compact('clientes'));
    }

    public function show(Cliente $cliente): View
    {
        $cliente->load(['number', 'user', 'payments']);
        $config = RaffleConfig::current();

        return view('vendor.clientes.show', compact('cliente', 'config'));
    }

    public function edit(Cliente $cliente): View
    {
        // Solo el vendedor que vendió puede editar
        if ($cliente->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para editar este cliente.');
        }

        $cliente->load('number');

        return view('vendor.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        // Solo el vendedor que vendió puede editar
        if ($cliente->user_id !== auth()->id()) {
            return back()->with('error', 'No tienes permiso para editar este cliente.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado exitosamente.');
    }
}

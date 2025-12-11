<?php

namespace App\Http\Controllers;

use App\Models\RaffleConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RaffleConfigController extends Controller
{
    /**
     * Mostrar el formulario de edición
     */
    public function edit()
    {
        // Obtener o crear la configuración
        $config = RaffleConfig::first();

        if (!$config) {
            // Crear una configuración por defecto si no existe
            $config = RaffleConfig::create([
                'prize_name' => 'Nueva Rifa',
                'prize_description' => '',
                'ticket_price' => 50000,
                'raffle_date' => now()->addMonth(),
                'status' => 'active',
            ]);
        }

        return view('admin.raffle-config.edit', compact('config'));
    }

    /**
     * Actualizar la configuración
     */
    public function update(Request $request)
    {
        $config = RaffleConfig::first();

        $validated = $request->validate([
            'prize_name' => 'required|string|max:255',
            'prize_description' => 'nullable|string',
            'prize_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_price' => 'required|numeric|min:0',
            'raffle_date' => 'required|date',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
            'lottery_method' => 'nullable|string|max:255',
            'winning_number' => 'nullable|string|max:2',
            'status' => 'required|in:active,finished,cancelled',
            'terms_and_conditions' => 'nullable|string',
            'contact_info' => 'nullable|string',
        ], [
            'prize_name.required' => 'El nombre del premio es obligatorio',
            'ticket_price.required' => 'El precio del boleto es obligatorio',
            'ticket_price.min' => 'El precio debe ser mayor a 0',
            'raffle_date.required' => 'La fecha del sorteo es obligatoria',
            'raffle_date.date' => 'La fecha del sorteo no es válida',
            'sale_end_date.after_or_equal' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'status.required' => 'El estado es obligatorio',
            'prize_image.image' => 'El archivo debe ser una imagen',
            'prize_image.max' => 'La imagen no debe pesar más de 2MB',
        ]);

        // Manejar la imagen
        if ($request->hasFile('prize_image')) {
            // Eliminar imagen anterior si existe
            if ($config->prize_image && Storage::disk('public')->exists($config->prize_image)) {
                Storage::disk('public')->delete($config->prize_image);
            }

            // Guardar nueva imagen
            $path = $request->file('prize_image')->store('raffle-images', 'public');
            $validated['prize_image'] = $path;
        }

        // Actualizar configuración
        $config->update($validated);

        return redirect()
            ->route('raffle-config.edit')
            ->with('success', '¡Configuración actualizada exitosamente!');
    }
}

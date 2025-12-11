<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ⚙️ Configuración de la Rifa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensaje de éxito -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('raffle-config.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Información del Premio -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🎁 Información del Premio</h3>

                            <div class="grid grid-cols-1 gap-6">
                                <!-- Nombre del Premio -->
                                <div>
                                    <label for="prize_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre del Premio <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="prize_name" id="prize_name"
                                        value="{{ old('prize_name', $config->prize_name) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('prize_name') border-red-500 @enderror"
                                        required>
                                    @error('prize_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Descripción -->
                                <div>
                                    <label for="prize_description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Descripción del Premio
                                    </label>
                                    <textarea name="prize_description" id="prize_description" rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('prize_description') border-red-500 @enderror"
                                    >{{ old('prize_description', $config->prize_description) }}</textarea>
                                    @error('prize_description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Imagen del Premio -->
                                <div>
                                    <label for="prize_image" class="block text-sm font-medium text-gray-700 mb-2">
                                        Imagen del Premio
                                    </label>

                                    @if ($config->prize_image)
                                        <div class="mb-3">
                                            <img src="{{ Storage::url($config->prize_image) }}"
                                                alt="Premio"
                                                class="h-32 w-auto rounded-lg shadow-sm">
                                            <p class="text-xs text-gray-500 mt-1">Imagen actual</p>
                                        </div>
                                    @endif

                                    <input type="file" name="prize_image" id="prize_image"
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('prize_image') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF hasta 2MB</p>
                                    @error('prize_image')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de Ventas -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">💰 Configuración de Ventas</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Precio del Boleto -->
                                <div>
                                    <label for="ticket_price" class="block text-sm font-medium text-gray-700 mb-2">
                                        Precio del Boleto (COP) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                        <input type="number" name="ticket_price" id="ticket_price"
                                            value="{{ old('ticket_price', $config->ticket_price) }}"
                                            step="0.01" min="0"
                                            class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('ticket_price') border-red-500 @enderror"
                                            required>
                                    </div>
                                    @error('ticket_price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estado -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Estado <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-500 @enderror"
                                        required>
                                        <option value="active" {{ old('status', $config->status) == 'active' ? 'selected' : '' }}>
                                            ✅ Activa
                                        </option>
                                        <option value="finished" {{ old('status', $config->status) == 'finished' ? 'selected' : '' }}>
                                            🏁 Finalizada
                                        </option>
                                        <option value="cancelled" {{ old('status', $config->status) == 'cancelled' ? 'selected' : '' }}>
                                            ❌ Cancelada
                                        </option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">📅 Fechas</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Fecha del Sorteo -->
                                <div>
                                    <label for="raffle_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha del Sorteo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="raffle_date" id="raffle_date"
                                        value="{{ old('raffle_date', $config->raffle_date?->format('Y-m-d')) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('raffle_date') border-red-500 @enderror"
                                        required>
                                    @error('raffle_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Inicio de Ventas -->
                                <div>
                                    <label for="sale_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Inicio de Ventas
                                    </label>
                                    <input type="date" name="sale_start_date" id="sale_start_date"
                                        value="{{ old('sale_start_date', $config->sale_start_date?->format('Y-m-d')) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('sale_start_date') border-red-500 @enderror">
                                    @error('sale_start_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Fin de Ventas -->
                                <div>
                                    <label for="sale_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fin de Ventas
                                    </label>
                                    <input type="date" name="sale_end_date" id="sale_end_date"
                                        value="{{ old('sale_end_date', $config->sale_end_date?->format('Y-m-d')) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('sale_end_date') border-red-500 @enderror">
                                    @error('sale_end_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información del Sorteo -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🎲 Información del Sorteo</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Método de Lotería -->
                                <div>
                                    <label for="lottery_method" class="block text-sm font-medium text-gray-700 mb-2">
                                        Método de Lotería
                                    </label>
                                    <input type="text" name="lottery_method" id="lottery_method"
                                        value="{{ old('lottery_method', $config->lottery_method) }}"
                                        placeholder="Ej: Lotería de Bogotá"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('lottery_method') border-red-500 @enderror">
                                    @error('lottery_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Número Ganador -->
                                <div>
                                    <label for="winning_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Número Ganador (2 últimos dígitos)
                                    </label>
                                    <input type="text" name="winning_number" id="winning_number"
                                        value="{{ old('winning_number', $config->winning_number) }}"
                                        maxlength="2" pattern="[0-9]{2}"
                                        placeholder="00-99"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('winning_number') border-red-500 @enderror">
                                    @error('winning_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Información Adicional</h3>

                            <div class="grid grid-cols-1 gap-6">
                                <!-- Términos y Condiciones -->
                                <div>
                                    <label for="terms_and_conditions" class="block text-sm font-medium text-gray-700 mb-2">
                                        Términos y Condiciones
                                    </label>
                                    <textarea name="terms_and_conditions" id="terms_and_conditions" rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('terms_and_conditions') border-red-500 @enderror"
                                    >{{ old('terms_and_conditions', $config->terms_and_conditions) }}</textarea>
                                    @error('terms_and_conditions')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Información de Contacto -->
                                <div>
                                    <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-2">
                                        Información de Contacto
                                    </label>
                                    <input type="text" name="contact_info" id="contact_info"
                                        value="{{ old('contact_info', $config->contact_info) }}"
                                        placeholder="Ej: Juan: +573001234567, María: +573009876543"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_info') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Formato: Nombre: Teléfono, Nombre: Teléfono</p>
                                    @error('contact_info')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-between pt-4">
                            <a href="{{ route('dashboard') }}"
                                class="text-gray-600 hover:text-gray-800 font-medium">
                                ← Volver al Dashboard
                            </a>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                                💾 Guardar Cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

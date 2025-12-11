<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Número {{ $number->number }}
            </h2>
            <a href="{{ route('numbers.index') }}" class="text-blue-600 hover:text-blue-900">
                ← Volver a números
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
            @endif

            <!-- Información del Número -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <div class="text-center mb-6">
                    <div class="text-6xl font-bold text-gray-800 mb-2">{{ $number->number }}</div>
                    <div class="inline-block px-4 py-2 rounded-full text-white font-bold
                        {{ $number->status === 'disponible' ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ strtoupper($number->status) }}
                    </div>
                </div>

                @if($number->status === 'vendido')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Vendido por:</span>
                        <span class="font-bold">{{ $number->soldBy->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Fecha de venta:</span>
                        <span class="font-bold">{{ $number->sold_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                @endif
            </div>

            @if($number->status === 'disponible')
            <!-- Formulario para Vender -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">Vender este número</h3>

                <form action="{{ route('numbers.sell', $number) }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre completo *
                            </label>
                            <input type="text" name="name" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('name') }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono *
                            </label>
                            <input type="text" name="phone" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('phone') }}">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email (opcional)
                            </label>
                            <input type="email" name="email"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('email') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Dirección (opcional)
                            </label>
                            <input type="text" name="address"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('address') }}">
                        </div>
                    </div>

                    <div class="border-t pt-4 mb-4">
                        <h4 class="font-bold mb-3">Información de Pago</h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Monto a pagar *
                                </label>
                                <input type="number" name="payment_amount" required step="0.01" min="0"
                                       max="{{ $config ? $config->ticket_price : 50000 }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       value="{{ old('payment_amount', $config ? $config->ticket_price : 50000) }}">
                                <p class="mt-1 text-xs text-gray-500">
                                    Precio total: ${{ number_format($config ? $config->ticket_price : 50000, 0, ',', '.') }}
                                </p>
                                @error('payment_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Método de pago *
                                </label>
                                <select name="payment_method" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="nequi">Nequi</option>
                                    <option value="daviplata">Daviplata</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de pago *
                                </label>
                                <input type="date" name="payment_date" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       value="{{ old('payment_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notas (opcional)
                        </label>
                        <textarea name="notes" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Vender Número
                        </button>
                    </div>
                </form>
            </div>
            @else
            <!-- Información del Cliente y Pagos -->
            @if($number->cliente)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">Información del Cliente</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-gray-600">Nombre:</span>
                        <span class="font-bold">{{ $number->cliente->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Teléfono:</span>
                        <span class="font-bold">{{ $number->cliente->phone }}</span>
                    </div>
                    @if($number->cliente->email)
                    <div>
                        <span class="text-gray-600">Email:</span>
                        <span class="font-bold">{{ $number->cliente->email }}</span>
                    </div>
                    @endif
                    @if($number->cliente->address)
                    <div>
                        <span class="text-gray-600">Dirección:</span>
                        <span class="font-bold">{{ $number->cliente->address }}</span>
                    </div>
                    @endif
                </div>

                <div class="border-t pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <span class="text-gray-600">Estado de Pago:</span>
                            <div class="mt-1">
                                @if($number->cliente->payment_status === 'pagado')
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        PAGADO
                                    </span>
                                @elseif($number->cliente->payment_status === 'abono')
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ABONO
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                        PENDIENTE
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-600">Total Pagado:</span>
                            <div class="text-2xl font-bold text-green-600">
                                ${{ number_format($number->cliente->total_paid, 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-600">Saldo Pendiente:</span>
                            <div class="text-2xl font-bold text-red-600">
                                ${{ number_format($number->cliente->pendingBalance(), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($number->cliente->notes)
                <div class="border-t pt-4">
                    <span class="text-gray-600">Notas:</span>
                    <p class="mt-1">{{ $number->cliente->notes }}</p>
                </div>
                @endif

                @if($number->sold_by === auth()->id())
                <div class="border-t pt-4 mt-4">
                    <a href="{{ route('clientes.edit', $number->cliente) }}"
                       class="text-blue-600 hover:text-blue-900">
                        ✏️ Editar información del cliente
                    </a>
                </div>
                @endif
            </div>

            <!-- Historial de Pagos -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Historial de Pagos</h3>
                    @if($number->sold_by === auth()->id() && $number->cliente->payment_status !== 'pagado')
                    <button onclick="document.getElementById('payment-form').classList.toggle('hidden')"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                        + Registrar Pago
                    </button>
                    @endif
                </div>

                <!-- Formulario para Nuevo Pago (oculto por defecto) -->
                @if($number->sold_by === auth()->id() && $number->cliente->payment_status !== 'pagado')
                <div id="payment-form" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-bold mb-3">Nuevo Pago</h4>
                    <form action="{{ route('payments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cliente_id" value="{{ $number->cliente->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Monto *
                                </label>
                                <input type="number" name="amount" required step="0.01" min="0.01"
                                       max="{{ $number->cliente->pendingBalance() }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    Pendiente: ${{ number_format($number->cliente->pendingBalance(), 0, ',', '.') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Método *
                                </label>
                                <select name="payment_method" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="nequi">Nequi</option>
                                    <option value="daviplata">Daviplata</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha *
                                </label>
                                <input type="date" name="payment_date" required
                                       value="{{ date('Y-m-d') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notas (opcional)
                            </label>
                            <textarea name="notes" rows="2"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('payment-form').classList.add('hidden')"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                                Registrar Pago
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Lista de Pagos -->
                @if($number->cliente->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registrado por</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($number->cliente->payments->sortByDesc('payment_date') as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">
                                    ${{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($payment->payment_method) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->user->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500 text-center py-4">No hay pagos registrados</p>
                @endif
            </div>
            @endif
            @endif

        </div>
    </div>
</x-app-layout>

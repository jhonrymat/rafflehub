<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <!-- Botón de Compartir -->
    <div class="bg-gradient-to-r from-green-500 to-blue-500 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold mb-2">📢 Comparte la rifa y vende más</h3>
                <p class="text-sm opacity-90">Usa estos botones para compartir con tus clientes</p>
            </div>
            <div class="flex gap-2">
                <a href="https://wa.me/?text=¡Participa y gana! {{ $config->prize_name ?? 'Gran Rifa' }} - Valor: ${{ number_format($config->ticket_price ?? 50000, 0, ',', '.') }} - Ver números disponibles: {{ url('/') }}"
                    target="_blank"
                    class="bg-white hover:bg-gray-100 text-green-600 font-bold py-2 px-4 rounded-lg transition">
                    💬 Compartir WhatsApp
                </a>
                <button onclick="copyDashboardLink()"
                    class="bg-white hover:bg-gray-100 text-blue-600 font-bold py-2 px-4 rounded-lg transition">
                    📋 Copiar Link
                </button>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Información de la Rifa -->
            @if ($config)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <h3 class="text-2xl font-bold mb-4">🎉 {{ $config->prize_name }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-gray-600">Valor Boleta:</span>
                            <span class="font-bold">${{ number_format($config->ticket_price, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Fecha Sorteo:</span>
                            <span class="font-bold">{{ $config->raffle_date->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Estado:</span>
                            <span class="font-bold">{{ ucfirst($config->status) }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Estadísticas Generales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-gray-600 text-sm">Total Números</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $totalNumbers }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-gray-600 text-sm">Vendidos</div>
                    <div class="text-3xl font-bold text-red-600">{{ $soldNumbers }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $totalNumbers > 0 ? round(($soldNumbers / $totalNumbers) * 100, 1) : 0 }}% del total
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-gray-600 text-sm">Disponibles</div>
                    <div class="text-3xl font-bold text-green-600">{{ $availableNumbers }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-gray-600 text-sm">Ingresos Totales</div>
                    <div class="text-2xl font-bold text-purple-600">
                        ${{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <!-- Estadísticas del Vendedor -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">📊 Mis Estadísticas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-gray-600 text-sm">Mis Ventas</div>
                        <div class="text-3xl font-bold text-blue-600">{{ $mySales }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm">Mis Clientes</div>
                        <div class="text-3xl font-bold text-green-600">{{ $myClientes }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 text-sm">Mis Ingresos</div>
                        <div class="text-2xl font-bold text-purple-600">
                            ${{ number_format($myRevenue, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clientes con Pagos Pendientes -->
            @if ($pendingClientes->count() > 0)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-bold mb-4 text-red-600">⚠️ Pagos Pendientes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($pendingClientes as $cliente)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-bold">{{ $cliente->number->number }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $cliente->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $cliente->phone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            ${{ number_format($cliente->total_paid, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-red-600 font-semibold">
                                            ${{ number_format($cliente->pendingBalance(), 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('clientes.show', $cliente) }}"
                                                class="text-blue-600 hover:text-blue-900">
                                                Ver detalles
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Ventas Recientes -->
            @if ($recentSales->count() > 0)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4">📋 Mis Ventas Recientes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado
                                        Pago</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($recentSales as $number)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-bold text-lg">{{ $number->number }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $number->cliente->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $number->sold_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($number->cliente)
                                                @if ($number->cliente->payment_status === 'pagado')
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        Pagado
                                                    </span>
                                                @elseif($number->cliente->payment_status === 'abono')
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Abono
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        Pendiente
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('numbers.show', $number) }}"
                                                class="text-blue-600 hover:text-blue-900">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
    <script>
        function copyDashboardLink() {
            const url = '{{ url('/') }}';
            navigator.clipboard.writeText(url).then(() => {
                alert('✅ Link copiado! Compártelo con tus clientes');
            });
        }
    </script>
</x-app-layout>

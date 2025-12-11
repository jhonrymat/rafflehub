<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reportes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Resumen General -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">📊 Resumen General</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $totalSales }}</div>
                        <div class="text-gray-600 text-sm">Números Vendidos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">
                            ${{ number_format($totalRevenue, 0, ',', '.') }}
                        </div>
                        <div class="text-gray-600 text-sm">Total Recaudado</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-red-600">
                            ${{ number_format($pendingAmount, 0, ',', '.') }}
                        </div>
                        <div class="text-gray-600 text-sm">Total Pendiente</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">
                            {{ $totalSales > 0 ? round(($totalRevenue / (($config ? $config->ticket_price : 50000) * $totalSales)) * 100, 1) : 0 }}%
                        </div>
                        <div class="text-gray-600 text-sm">% Recaudado</div>
                    </div>
                </div>
            </div>

            <!-- Estados de Pago -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">💳 Estados de Pago</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-4xl font-bold text-green-600">{{ $fullyPaid }}</div>
                        <div class="text-gray-600">Completamente Pagados</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-4xl font-bold text-yellow-600">{{ $partialPaid }}</div>
                        <div class="text-gray-600">Con Abono</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-4xl font-bold text-red-600">{{ $pending }}</div>
                        <div class="text-gray-600">Pendientes</div>
                    </div>
                </div>
            </div>

            <!-- Detalle de Números Vendidos -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">📋 Detalle de Números Vendidos</h3>
                    <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                        🖨️ Imprimir
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendedor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendiente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($soldNumbers as $number)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap font-bold">{{ $number->number }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $number->cliente->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $number->cliente->phone ?? 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $number->soldBy->name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-green-600 font-semibold">
                                    ${{ number_format($number->cliente->total_paid ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-red-600 font-semibold">
                                    ${{ number_format($number->cliente ? $number->cliente->pendingBalance() : 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($number->cliente)
                                        @if($number->cliente->payment_status === 'pagado')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Pagado
                                            </span>
                                        @elseif($number->cliente->payment_status === 'abono')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Abono
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Pendiente
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $number->sold_at->format('d/m/Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-center text-gray-500">
                                    No hay números vendidos aún
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

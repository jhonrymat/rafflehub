<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Números de la Rifa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            <!-- Leyenda -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-green-500 rounded border-2 border-green-600"></div>
                        <span>Disponible (click para vender)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-blue-500 rounded border-2 border-blue-600"></div>
                        <span>Vendido por mí (click para ver)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gray-400 rounded border-2 border-gray-500"></div>
                        <span>Vendido por otro (solo lectura)</span>
                    </div>
                </div>
            </div>

            <!-- Grid de Números -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="grid grid-cols-5 sm:grid-cols-10 gap-3">
                    @foreach($numbers as $number)
                    <a href="{{ $number->status === 'disponible' || $number->sold_by === auth()->id() ? route('numbers.show', $number) : '#' }}"
                       class="aspect-square flex items-center justify-center text-xl font-bold rounded-lg border-2 transition-all duration-300
                       @if($number->status === 'disponible')
                           bg-green-500 border-green-600 text-white hover:bg-green-600 hover:scale-105
                       @elseif($number->sold_by === auth()->id())
                           bg-blue-500 border-blue-600 text-white hover:bg-blue-600 hover:scale-105
                       @else
                           bg-gray-400 border-gray-500 text-gray-700 cursor-not-allowed
                       @endif">
                        {{ $number->number }}
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

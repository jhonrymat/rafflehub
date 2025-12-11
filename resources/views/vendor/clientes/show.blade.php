<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Cliente - Número {{ $cliente->number->number }}
            </h2>
            <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-900">
                ← Volver a clientes
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

            <!-- Ver el número completo -->
            <div class="mb-4">
                <a href="{{ route('numbers.show', $cliente->number) }}"
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Ver Número {{ $cliente->number->number }}
                </a>
            </div>

        </div>
    </div>
</x-app-layout>

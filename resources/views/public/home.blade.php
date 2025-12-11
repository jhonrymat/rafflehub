<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $config->prize_name ?? 'Rifa' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-2">
                🎉 {{ $config->prize_name ?? 'Rifa' }} 🎉
            </h1>
            <p class="text-xl text-gray-600">{{ $config->prize_description ?? '' }}</p>

            @if ($config)
                <div class="mt-4 flex flex-wrap justify-center gap-4 text-sm md:text-base">
                    <div class="bg-white px-4 py-2 rounded-lg shadow">
                        <span class="font-semibold">💰 Valor:</span>
                        ${{ number_format($config->ticket_price, 0, ',', '.') }} COP
                    </div>
                    <div class="bg-white px-4 py-2 rounded-lg shadow">
                        <span class="font-semibold">📅 Sorteo:</span>
                        {{ $config->raffle_date->format('d/m/Y') }}
                    </div>
                    <div class="bg-white px-4 py-2 rounded-lg shadow">
                        <span class="font-semibold">🎲 Método:</span>
                        {{ $config->lottery_method ?? 'Por definir' }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Imagen del Premio -->
        @if ($config && $config->prize_image)
            <div class="mb-8 flex justify-center">
                <div class="bg-white rounded-2xl shadow-xl p-4 max-w-2xl w-full">
                    <div class="relative overflow-hidden rounded-xl">
                        <img src="{{ Storage::url($config->prize_image) }}"
                             alt="{{ $config->prize_name }}"
                             class="w-full h-auto object-cover max-h-96 rounded-xl transition-transform duration-300 hover:scale-105">
                        <div class="absolute top-4 right-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-4 py-2 rounded-full font-bold shadow-lg">
                            🏆 Premio
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Estadísticas -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-3xl font-bold text-blue-600" id="total-numbers">{{ $totalNumbers }}</div>
                    <div class="text-gray-600 text-sm">Total</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-red-600" id="sold-numbers">{{ $soldNumbers }}</div>
                    <div class="text-gray-600 text-sm">Vendidos</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-green-600" id="available-numbers">{{ $availableNumbers }}</div>
                    <div class="text-gray-600 text-sm">Disponibles</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-purple-600" id="percentage-sold">{{ $percentageSold }}%</div>
                    <div class="text-gray-600 text-sm">Vendido</div>
                </div>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="flex justify-center gap-6 mb-6 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-500 rounded border-2 border-green-600"></div>
                <span>Disponible</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-400 rounded border-2 border-gray-500"></div>
                <span>Vendido</span>
            </div>
        </div>

        <!-- Grid de Números -->
        <div id="numbers-grid" class="grid grid-cols-5 sm:grid-cols-10 gap-2 md:gap-3 max-w-5xl mx-auto">
            @foreach ($numbers as $number)
                <div class="number-card aspect-square flex items-center justify-center text-xl font-bold rounded-lg border-2 transition-all duration-300
                {{ $number->status === 'disponible'
                    ? 'bg-green-500 border-green-600 text-white hover:bg-green-600 cursor-pointer'
                    : 'bg-gray-400 border-gray-500 text-gray-700 cursor-not-allowed' }}"
                    data-number="{{ $number->number }}" data-status="{{ $number->status }}">
                    {{ $number->number }}
                </div>
            @endforeach
        </div>

        <!-- Información de Contacto -->
        @if ($config && $config->contact_info)
            <div class="mt-8 text-center bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-700 mb-2">
                    <span class="font-semibold">📞 Contacto:</span>
                    @php
                        // Dividir el string por comas para obtener cada contacto
                        $contacts = array_map('trim', explode(',', $config->contact_info));
                    @endphp

                    @foreach ($contacts as $index => $contact)
                        @php
                            // Extraer nombre y teléfono usando regex
                            preg_match('/^(.+?):\s*(\+?\d+)$/', $contact, $matches);
                            $name = $matches[1] ?? $contact;
                            $phone = $matches[2] ?? '';
                            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                        @endphp

                        @if ($phone)
                            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank"
                                class="text-green-600 hover:text-green-700 font-semibold">
                                {{ $name }}: {{ $phone }}
                            </a>
                            @if ($index < count($contacts) - 1)
                                <span class="text-gray-400">, </span>
                            @endif
                        @endif
                    @endforeach
                </p>
                <p class="text-sm text-gray-500">Haz clic en un número verde para más información</p>
            </div>
        @endif

        <!-- Botones para Compartir -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold mb-4 text-center">📢 Comparte esta rifa</h3>
            <div class="flex flex-wrap justify-center gap-3">

                <!-- WhatsApp -->
                <a href="https://wa.me/?text=¡Participa en esta rifa! {{ $config->prize_name }} - {{ url('/') }}"
                    target="_blank"
                    class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                    WhatsApp
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url('/') }}" target="_blank"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                    Facebook
                </a>

                <!-- Twitter -->
                <a href="https://twitter.com/intent/tweet?text=¡Participa en esta rifa! {{ $config->prize_name }}&url={{ url('/') }}"
                    target="_blank"
                    class="flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                    </svg>
                    Twitter
                </a>

                <!-- Copiar Link -->
                <button onclick="copyLink()"
                    class="flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Copiar Link
                </button>

            </div>
            <p class="text-center text-sm text-gray-500 mt-4" id="copy-message"></p>
        </div>

        <!-- Última actualización -->
        <div class="mt-4 text-center text-sm text-gray-500">
            Última actualización: <span id="last-update">{{ now()->format('H:i:s') }}</span>
        </div>
    </div>

    <!-- Modal para número disponible -->
    <div id="number-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
            <h3 class="text-2xl font-bold mb-4 text-center">Número <span id="modal-number"></span></h3>
            <div class="text-center mb-6">
                <div class="inline-block bg-green-500 text-white px-6 py-3 rounded-lg text-xl font-bold">
                    ✅ DISPONIBLE
                </div>
            </div>
            <p class="text-gray-700 mb-4 text-center">
                Este número está disponible. Para comprarlo, contacta al:
            </p>
            @if ($config && $config->contact_info)
                @php
                    // Dividir el string por comas para obtener cada contacto
                    $contacts = array_map('trim', explode(',', $config->contact_info));
                @endphp

                @foreach ($contacts as $contact)
                    @php
                        // Extraer nombre y teléfono usando regex
                        preg_match('/^(.+?):\s*(\+?\d+)$/', $contact, $matches);
                        $name = $matches[1] ?? $contact;
                        $phone = $matches[2] ?? '';
                        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                    @endphp

                    @if ($phone)
                        <a href="https://wa.me/{{ $cleanPhone }}?text=Hola, estoy interesado en el número"
                            target="_blank"
                            class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg text-center mb-3">
                            💬 WhatsApp: {{ $name }} ({{ $phone }})
                        </a>
                    @endif
                @endforeach
            @endif
            <button onclick="closeModal()"
                class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                Cerrar
            </button>
        </div>
    </div>

    <script>
        // ⚙️ CONFIGURACIÓN DE ACTUALIZACIÓN (modifica estos valores)
        const UPDATE_CONFIG = {
            firstUpdate: 5, // Segundos para primera actualización (5 segundos)
            interval: 30, // Segundos entre actualizaciones (30 segundos = medio minuto)
            maxAttempts: 3 // Intentos antes de detener auto-refresh
        };

        // Auto-refresh configurable
        let refreshInterval;
        let updateAttempts = 0;

        function startAutoRefresh() {
            console.log(`🔄 Auto-refresh iniciado (cada ${UPDATE_CONFIG.interval}s)`);
            // Primera actualización
            setTimeout(updateNumbers, UPDATE_CONFIG.firstUpdate * 1000);
            // Luego cada intervalo configurado
            refreshInterval = setInterval(updateNumbers, UPDATE_CONFIG.interval * 1000);
        }

        async function updateNumbers() {
            try {
                console.log('🔍 Intentando actualizar números...');

                // Usar la URL absoluta en lugar de route helper
                const url = window.location.origin + '/api/numbers/status';
                console.log('📡 URL:', url);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-cache'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('✅ Datos recibidos:', data);

                // Resetear intentos fallidos
                updateAttempts = 0;

                // Actualizar estadísticas
                updateStats(data.stats);

                // Actualizar números
                updateNumberCards(data.numbers);

                // Actualizar hora
                updateLastUpdateTime();

                // Mostrar notificación sutil
                showUpdateNotification('✓ Actualizado');

            } catch (error) {
                console.error('❌ Error al actualizar números:', error);
                updateAttempts++;

                if (updateAttempts >= UPDATE_CONFIG.maxAttempts) {
                    console.error('🛑 Máximo de intentos alcanzado. Deteniendo auto-refresh.');
                    clearInterval(refreshInterval);
                    showUpdateNotification('⚠ Error de conexión', true);
                }
            }
        }

        function updateStats(stats) {
            const soldElement = document.getElementById('sold-numbers');
            const availableElement = document.getElementById('available-numbers');
            const percentageElement = document.getElementById('percentage-sold');

            if (soldElement && stats.sold !== undefined) {
                animateNumber(soldElement, parseInt(soldElement.textContent), stats.sold);
            }

            if (availableElement && stats.available !== undefined) {
                animateNumber(availableElement, parseInt(availableElement.textContent), stats.available);
            }

            if (percentageElement && stats.percentage !== undefined) {
                animateNumber(percentageElement, parseFloat(percentageElement.textContent), stats.percentage, '%');
            }
        }

        function updateNumberCards(numbers) {
            let changesCount = 0;

            numbers.forEach(number => {
                const card = document.querySelector(`[data-number="${number.number}"]`);
                if (card && card.dataset.status !== number.status) {
                    changesCount++;
                    card.dataset.status = number.status;

                    if (number.status === 'vendido') {
                        card.className =
                            'number-card aspect-square flex items-center justify-center text-xl font-bold rounded-lg border-2 transition-all duration-300 bg-gray-400 border-gray-500 text-gray-700 cursor-not-allowed';
                        // Efecto de "vendido"
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            card.style.transform = 'scale(1)';
                        }, 300);
                    } else {
                        card.className =
                            'number-card aspect-square flex items-center justify-center text-xl font-bold rounded-lg border-2 transition-all duration-300 bg-green-500 border-green-600 text-white hover:bg-green-600 cursor-pointer';
                    }
                }
            });

            if (changesCount > 0) {
                console.log(`📊 ${changesCount} número(s) actualizado(s)`);
            }
        }

        function updateLastUpdateTime() {
            const lastUpdateElement = document.getElementById('last-update');
            if (lastUpdateElement) {
                lastUpdateElement.textContent = new Date().toLocaleTimeString('es-CO', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
        }

        function animateNumber(element, from, to, suffix = '') {
            const duration = 500;
            const steps = 20;
            const increment = (to - from) / steps;
            let current = from;
            let step = 0;

            const timer = setInterval(() => {
                step++;
                current += increment;

                if (step >= steps) {
                    element.textContent = to + suffix;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.round(current) + suffix;
                }
            }, duration / steps);
        }

        function showUpdateNotification(message, isError = false) {
            // Crear notificación temporal
            const notification = document.createElement('div');
            notification.textContent = message;
            notification.className =
                `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white text-sm z-50 transition-opacity duration-300 ${isError ? 'bg-red-500' : 'bg-green-500'}`;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 2000);
        }

        // Click en números
        document.querySelectorAll('.number-card').forEach(card => {
            card.addEventListener('click', function() {
                if (this.dataset.status === 'disponible') {
                    document.getElementById('modal-number').textContent = this.dataset.number;

                    // Actualizar link de WhatsApp con el número
                    const waLinks = document.querySelectorAll('#number-modal a[href*="wa.me"]');
                    waLinks.forEach(waLink => {
                        const currentHref = waLink.href.split('?text=')[0];
                        waLink.href = currentHref + '?text=Hola, estoy interesado en el número ' +
                            this.dataset.number;
                    });

                    document.getElementById('number-modal').classList.remove('hidden');
                }
            });
        });

        function closeModal() {
            document.getElementById('number-modal').classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('number-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Función para copiar link
        function copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                showUpdateNotification('✅ Link copiado!');
            }).catch(err => {
                console.error('Error al copiar:', err);
                showUpdateNotification('❌ Error al copiar', true);
            });
        }

        // Iniciar auto-refresh cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startAutoRefresh);
        } else {
            startAutoRefresh();
        }

        // Reanudar actualizaciones cuando la página vuelve a estar visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                console.log('👁️ Página visible de nuevo, actualizando...');
                updateNumbers();
            }
        });
    </script>
</body>

</html>

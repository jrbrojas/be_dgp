<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    {{-- Tailwind “standalone” para render fuera del build de Vite --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Ajustes para que el screenshot quede compacto y sin scroll */
        html,
        body {
            margin: 0;
            background: #ffffff;
        }

        .page {
            width: 1280px;
            /* base de captura */
            padding: 20px;
        }
    </style>
</head>

<body>
    @php
        $nivelColorClasses = [
            'MUY ALTO' => 'text-red-500 bg-red-500',
            'ALTO' => 'text-orange-400 bg-orange-400',
            'MEDIO' => 'text-yellow-500 bg-yellow-500',
            'BAJO' => 'text-green-400 bg.green-400',
            'MUY BAJO' => 'text-green-700 bg-green-700',
            '' => 'text-gray-500 bg-gray-500',
        ];

        \Carbon\Carbon::setLocale('es');
        $mesInicio = \Carbon\Carbon::parse($escenario->fecha_inicio)->translatedFormat('F');
        $mesFin = \Carbon\Carbon::parse($escenario->fecha_fin)->translatedFormat('F');
        $year = \Carbon\Carbon::parse($escenario->fecha_inicio)->year;
    @endphp

    <div id="capture" class="page p-2">
        <div class='grid grid-cols-1 lg:grid-cols-4 gap-4'>

            <div class='flex flex-col gap-4'>
                <div class='flex justify-center items-center overflow-hidden rounded-lg'>
                    <x-image :src="$escenario->mapas->firstWhere('tipo', $tipo === 'imagen_izquierdo_bt')->ruta ?? ''" />
                </div>
            </div>

            <div class='col-span-2 flex-col items-center justify-center'>
                <div class='flex-1 flex-col items-center text-center mb-2'>
                    <h6 class="text-center font-semibold text-teal-600">{{ $escenario->nombre }}</h6>
                    <h6 class="text-center font-semibold text-green-600/60">{{ $escenario->subtitulo }}</h6>
                </div>

                <div class='w-full mb-4'>
                    <x-image :src="$escenario->mapas->firstWhere('tipo', $tipo === 'imagen_centro_bt')->ruta ?? ''" />
                </div>
            </div>

            <div class='flex flex-col gap-3 items-center'>
                @foreach (array_slice($data, 0, 1) as $index => $item)
                    <div class="flex flex-col gap-3 justify-start items-center w-full">

                        <div class="p-2 bg-teal-600 rounded-full">
                            <h4 class='text-sm font-medium text-white mr-4 ml-4'>HELADAS</h4>
                        </div>

                        <div class="bg-gray-200/50 rounded-4xl p-5 grid grid-cols-2 items-center w-full">

                            <div className='flex flex-col gap-5 items-center'>
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-cyan-600" height="50"
                                    width="50" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                    <path
                                        d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z">
                                    </path>
                                </svg>
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 640 512"
                                    class="text-cyan-600" height="50" width="50"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z">
                                    </path>
                                </svg>
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512"
                                    class="text-cyan-600" height="50" width="50"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z">
                                    </path>
                                </svg>
                            </div>
                            <div className='flex flex-col gap-5 items-center'>
                                {{-- Distritos --}}
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ $item['total_distritos'] }}</p>
                                    <p class="text-md">Distritos</p>
                                </div>
                                {{-- Población --}}
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ $item['total_poblacion'] }}</p>
                                    <p class="text-md">Población</p>
                                </div>
                                {{-- Viviendas --}}
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ $item['total_vivienda'] }}</p>
                                    <p class="text-md">Viviendas</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col p-2 w-full">
                            {{-- Nivel de riesgo --}}
                            <div
                                class="{{ $nivelColorClasses[strtoupper($item['nivel'])] }} text-white text-center
                                font-semibold py-1 rounded">
                                {{ $item['nivel'] }}
                            </div>
                            <div class="mt-5 text-xs text-teal-600 font-semibold">
                                Departamentos con población expuesta:
                                @foreach ($item['departamentos_poblacion'] as $depa)
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs">{{ $depa['departamento'] }}</span>
                                        <span class='text-xs'>{{ numero_formateado($depa['total_poblacion']) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class='w-full flex items-center gap-2 mt-5'>
                    <span class="text-xs flex-shrink-0">Fuente: CENEPRED (2025)</span>
                    <a href={{ $escenario->url_base }} target="_blank" rel="noreferrer"
                        class="block bg-teal-600 p-2 text-white rounded-md overflow-hidden whitespace-nowrap text-ellipsis">
                        Ver Informe Escenario de Riesgo
                    </a>
                </div>

            </div>

        </div>
    </div>
</body>

</html>

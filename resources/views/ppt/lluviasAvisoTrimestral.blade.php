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
        $indexMapa = [
            'inundaciones' => 0,
            'movimiento_masa' => 1,
        ];

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

        $mapaIzquierdoSuperior = $escenario->mapas->where('tipo', 'mapa_izquierdo_superior')->values();
        $mapaIzquierdoInferior = $escenario->mapas->where('tipo', 'mapa_izquierdo_inferior')->values();
        $mapaCentro = $escenario->mapas->where('tipo', 'mapa_centro')->values();
    @endphp

    <div id="capture" class='page'>

        <div class='flex justify-between gap-4 items-center mb-3'>
            <div class='flex-1 flex flex-col items-center text-center'>
                <h2 class="text-center font-semibold text-teal-600">
                    Escenario de riesgos por exposición
                </h2>
                <p class='text-blue-400 text-lg'>{{ $escenario->nombre }}</p>
                <p class='text-teal-600 text-lg'>{{ $escenario->titulo_base }}</p>
            </div>
            <div class="p-2 text-lg font-medium text-white bg-teal-600 rounded-full">
                <p>AVISO TRIMESTRAL</p>
            </div>
        </div>

        <div class='grid grid-cols-1 lg:grid-cols-5 gap-6'>

            <div class='flex flex-col justify-start items-center gap-2'>
                <div class='w-full flex justify-center'>
                    @if ($escenario->mapas)
                        <x-image
                            src="{{ $mapaIzquierdoSuperior[$indexMapa[$tipo]] ? $mapaIzquierdoSuperior[$indexMapa[$tipo]]->ruta : null }}" />
                    @endif

                </div>
                <div class='w-full flex justify-center'>
                    @if ($escenario->mapas)
                        <x-image
                            src="{{ $mapaIzquierdoInferior[$indexMapa[$tipo]] ? $mapaIzquierdoInferior[$indexMapa[$tipo]]->ruta : null }}" />
                    @endif
                </div>
            </div>

            <div class='col-span-2 flex items-start justify-center'>
                @if ($escenario->mapas)
                    <x-image src="{{ $mapaCentro[$indexMapa[$tipo]] ? $mapaCentro[$indexMapa[$tipo]]->ruta : null }}" />
                @endif
            </div>

            <div class='col-span-2'>
                <h3 class='font-semibold text-teal-600 text-center'>
                    {{ $mesInicio }} - {{ $mesFin }} {{ $year }}
                </h3>
                <h3 class='font-semibold text-green-600/70 text-center'>
                    {{ $tipo == 'inundaciones' ? 'Inundación' : 'Movimiento en masa' }}
                </h3>

                @foreach (array_slice($data, 0, 1) as $index => $item)
                    <div class="grid grid-cols-2 border rounded-xl border-teal-600 shadow-md bg-white">
                        <div class="p-2 space-y-4">
                            {{-- Distritos --}}
                            <div class="flex items-center gap-3">
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-cyan-600" height="50"
                                    width="50" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                    <path
                                        d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z">
                                    </path>
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ $item['total_centro_poblado'] }}</p>
                                    <p class="text-md">Centros Poblados</p>
                                </div>
                            </div>
                            {{-- Población --}}
                            <div class="flex items-center gap-3">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 640 512"
                                    class="text-cyan-600" height="50" width="50"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z">
                                    </path>
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ $item['total_poblacion'] }}</p>
                                    <p class="text-md">Población</p>
                                </div>
                            </div>
                            {{-- Viviendas --}}
                            <div class="flex items-center gap-3">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512"
                                    class="text-cyan-600" height="50" width="50"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z">
                                    </path>
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ $item['total_vivienda'] }}</p>
                                    <p class="text-md">Viviendas</p>
                                </div>
                            </div>
                            {{-- Instituciones Educativas --}}
                            <div class="flex items-center gap-3">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                    class="text-cyan-600" height="50" width="50"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21 10h-2V4h1V2H4v2h1v6H3a1 1 0 0 0-1 1v9h20v-9a1 1 0 0 0-1-1zm-7 8v-4h-4v4H7V4h10v14h-3z">
                                    </path>
                                    <path d="M9 6h2v2H9zm4 0h2v2h-2zm-4 4h2v2H9zm4 0h2v2h-2z"></path>
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ $item['total_inst_educativa'] }}</p>
                                    <p class="text-md">Inst. Educativas</p>
                                </div>
                            </div>
                            {{-- Establecimientos de Salud --}}
                            <div class="flex items-center gap-3">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16"
                                    class="text-cyan-600" height="50" width="50"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8.5 5.034v1.1l.953-.55.5.867L9 7l.953.55-.5.866-.953-.55v1.1h-1v-1.1l-.953.55-.5-.866L7 7l-.953-.55.5-.866.953.55v-1.1zM13.25 9a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25zM13 11.25a.25.25 0 0 1 .25-.25h.5a.25.25 0 0 1 .25.25v.5a.25.25 0 0 1-.25.25h-.5a.25.25 0 0 1-.25-.25zm.25 1.75a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25zm-11-4a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5A.25.25 0 0 0 3 9.75v-.5A.25.25 0 0 0 2.75 9zm0 2a.25.25 0 0 0-.25.25v.5c0 .138.112.25.25.25h.5a.25.25 0 0 0 .25-.25v-.5a.25.25 0 0 0-.25-.25zM2 13.25a.25.25 0 0 1 .25-.25h.5a.25.25 0 0 1 .25.25v.5a.25.25 0 0 1-.25.25h-.5a.25.25 0 0 1-.25-.25z">
                                    </path>
                                    <path
                                        d="M5 1a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1a1 1 0 0 1 1 1v4h3a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h3V3a1 1 0 0 1 1-1zm2 14h2v-3H7zm3 0h1V3H5v12h1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1zm0-14H6v1h4zm2 7v7h3V8zm-8 7V8H1v7z">
                                    </path>
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ $item['total_est_salud'] }}</p>
                                    <p class="text-md">Est. de Salud</p>
                                </div>
                            </div>
                        </div>
                        <div class="border-l border-teal-600 p-2 flex flex-col justify-between">
                            <div
                                class="{{ $nivelColorClasses[strtoupper($item['nivel'])] }} text-white text-center font-semibold py-1 rounded">
                                {{ $item['nivel'] }}
                            </div>
                            <p class="text-sm text-teal-600 mt-2">
                                Departamentos:
                                <span
                                    class="{{ $nivelColorClasses[strtoupper($item['nivel'])] }} bg-white font-semibold">
                                    @if ($item['departamentos'])
                                        <x-format-nombre-array pgArray="{{ $item['departamentos'] }}" />
                                    @endif
                                </span>
                            </p>

                            <div class="mt-3 text-sm text-teal-600 font-semibold">
                                Departamentos con población expuesta:
                                @foreach ($item['departamentos_poblacion'] as $depa)
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs">{{ $depa['departamento'] }}</span>
                                        <span class='text-xs'>{{ $depa['total_poblacion'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class='flex items-center gap-2 mt-5'>
                    <span class="text-xs flex-shrink-0">Fuente: CENEPRED (2025)</span>
                    <a href={{ $escenario->url_base }} target="_blank" rel="noreferrer"
                        class="block bg-teal-600 p-2 text-white rounded-md overflow-hidden whitespace-nowrap text-ellipsis">
                        Ver detalle
                    </a>
                </div>
            </div>
        </div>

    </div>

</body>

</html>

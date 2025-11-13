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

        $mapaIzquierdo = $escenario->mapas->where('tipo', 'mapa_izquierdo')->values();
        $mapaCentro = $escenario->mapas->where('tipo', 'mapa_centro')->values();

    @endphp

    <div id="capture" class="page p-2">
        <div class='grid grid-cols-1 lg:grid-cols-4 gap-4'>

            <div class="w-full flex justify-start">
                <div class="flex flex-col gap-4 w-full">
                    @if ($escenario->mapas)
                        <x-image src="{{ $mapaIzquierdo[0] ? $mapaIzquierdo[0]->ruta : null }}" />
                    @endif

                    @foreach (array_slice($data, 0, 1) as $index => $item)
                        <div class="text-sm text-teal-600 font-semibold p-4 bg-blue-100/80">
                            Departamentos con población expuesta:
                            @foreach ($item['departamentos_poblacion'] as $depa)
                                <div class="flex justify-between items-center">
                                    <span class="text-xs">{{ $depa['departamento'] }}</span>
                                    <span class='text-xs'>{{ numero_formateado($depa['total_poblacion']) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class='col-span-2'>
                <div class='w-full flex flex-col gap-4 justify-center items-center'>
                    <div class='flex-1 flex-col items-center text-center'>
                        <h4 class="text-center font-semibold text-teal-600">{{ $escenario->nombre }}</h4>
                        <h4 class="text-center font-semibold text-green-600/60">{{ $escenario->subtitulo }}</h4>
                    </div>

                    @if ($escenario->mapas)
                        <x-image src="{{ $mapaCentro[0] ? $mapaCentro[0]->ruta : null }}" />
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-3 items-center">
                @foreach (array_slice($data, 0, 1) as $index => $item)
                    <div class="flex flex-col gap-3 justify-start items-center">

                        <div class="p-2 bg-teal-600 rounded-full w-full text-center">
                            <h4 class='text-sm font-medium text-white mr-4 ml-4'>INCENDIO FORESTALES</h4>
                        </div>

                        {{-- Nivel de riesgo --}}
                        <div
                            class="{{ $nivelColorClasses[strtoupper($item['nivel'])] }} text-white text-center font-semibold
                            p-2 w-full rounded">
                            {{ $item['nivel'] }}
                        </div>

                        <div class="bg-gray-200/50 rounded-4xl p-5 space-y-5 w-full">
                            {{-- Centros poblados --}}
                            <div class="flex items-center gap-8">
                                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-cyan-600" height="50"
                                    width="50" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                    <path
                                        d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z">
                                    </path>
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ numero_formateado($item['total_centro_poblado']) }}</p>
                                    <p class="text-md">Centros poblados</p>
                                </div>
                            </div>
                            {{-- Población --}}
                            <div class="flex items-center gap-8">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 640 512"
                                    class="text-cyan-600" height="50" width="50"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z">
                                    </path>
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ numero_formateado($item['total_poblacion']) }}</p>
                                    <p class="text-md">Población</p>
                                </div>
                            </div>
                            {{-- Viviendas --}}
                            <div class="flex items-center gap-8">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 576 512"
                                    class="text-cyan-600" height="50" width="50"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z">
                                    </path>
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ numero_formateado($item['total_vivienda']) }}</p>
                                    <p class="text-md">Viviendas</p>
                                </div>
                            </div>
                            {{-- Inst. Educativas --}}
                            <div class="flex items-center gap-8">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                    class="text-cyan-600" height="50" width="50"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21 10h-2V4h1V2H4v2h1v6H3a1 1 0 0 0-1 1v9h20v-9a1 1 0 0 0-1-1zm-7 8v-4h-4v4H7V4h10v14h-3z">
                                    </path>
                                    <path d="M9 6h2v2H9zm4 0h2v2h-2zm-4 4h2v2H9zm4 0h2v2h-2z"></path>
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ numero_formateado($item['total_inst_educativa']) }}</p>
                                    <p class="text-md">Inst. Educativas</p>
                                </div>
                            </div>
                            {{-- Est. Salud --}}
                            <div class="flex items-center gap-8">
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
                                    <p class="text-xl font-bold">{{ numero_formateado($item['total_est_salud']) }}</p>
                                    <p class="text-md">Est. de Salud</p>
                                </div>
                            </div>
                            {{-- Superficie agricola --}}
                            <div class="flex items-center gap-8">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" class="bi bi-tree-fill text-cyan-600" height="50" width="50" viewBox="0 0 16 16">
                                    <path
                                        d="M8.416.223a.5.5 0 0 0-.832 0l-3 4.5A.5.5 0 0 0 5 5.5h.098L3.076 8.735A.5.5 0 0 0 3.5 9.5h.191l-1.638 3.276a.5.5 0 0 0 .447.724H7V16h2v-2.5h4.5a.5.5 0 0 0 .447-.724L12.31 9.5h.191a.5.5 0 0 0 .424-.765L10.902 5.5H11a.5.5 0 0 0 .416-.777z" />
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ numero_formateado($item['total_superficie_agricola']) }}</p>
                                    <p class="text-md">Sup. Agricola (Ha)</p>
                                </div>
                            </div>
                            {{-- Mon. Arqueologicos --}}
                            <div class="flex items-center gap-8">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" fill="currentColor" class="text-cyan-600" height="50" width="50">
                                    <path
                                        d="M80 88C80 74.7 90.7 64 104 64L536 64C549.3 64 560 74.7 560 88C560 101.3 549.3 112 536 112L528 112L528 528L536 528C549.3 528 560 538.7 560 552C560 565.3 549.3 576 536 576L104 576C90.7 576 80 565.3 80 552C80 538.7 90.7 528 104 528L112 528L112 112L104 112C90.7 112 80 101.3 80 88zM288 176L288 208C288 216.8 295.2 224 304 224L336 224C344.8 224 352 216.8 352 208L352 176C352 167.2 344.8 160 336 160L304 160C295.2 160 288 167.2 288 176zM192 160C183.2 160 176 167.2 176 176L176 208C176 216.8 183.2 224 192 224L224 224C232.8 224 240 216.8 240 208L240 176C240 167.2 232.8 160 224 160L192 160zM288 272L288 304C288 312.8 295.2 320 304 320L336 320C344.8 320 352 312.8 352 304L352 272C352 263.2 344.8 256 336 256L304 256C295.2 256 288 263.2 288 272zM416 160C407.2 160 400 167.2 400 176L400 208C400 216.8 407.2 224 416 224L448 224C456.8 224 464 216.8 464 208L464 176C464 167.2 456.8 160 448 160L416 160zM176 272L176 304C176 312.8 183.2 320 192 320L224 320C232.8 320 240 312.8 240 304L240 272C240 263.2 232.8 256 224 256L192 256C183.2 256 176 263.2 176 272zM416 256C407.2 256 400 263.2 400 272L400 304C400 312.8 407.2 320 416 320L448 320C456.8 320 464 312.8 464 304L464 272C464 263.2 456.8 256 448 256L416 256zM352 448L395.8 448C405.7 448 413.3 439 409.8 429.8C396 393.7 361 368 320.1 368C279.2 368 244.2 393.7 230.4 429.8C226.9 439 234.5 448 244.4 448L288.2 448L288.2 528L352.2 528L352.2 448z" />
                                </svg>
                                <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                    <p class="text-xl font-bold">{{ numero_formateado($item['total_mon_arqueologico']) }}</p>
                                    <p class="text-md">Mon. Arqueológicos</p>
                                </div>
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

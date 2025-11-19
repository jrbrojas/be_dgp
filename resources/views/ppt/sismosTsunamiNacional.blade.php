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
        $mapaIzquierdo = [
            'sismos' => 'imagen_izquierdo_sismo',
            'tsunamis' => 'imagen_izquierdo_tsunami',
            'glaciares' => 'imagen_izquierdo_glaciar',
            'movimiento_masa' => 'imagen_izquierdo_mm',
        ];

        $mapaCentro = [
            'sismos' => 'imagen_centro_sismo',
            'tsunamis' => 'imagen_centro_tsunami',
            'glaciares' => 'imagen_centro_glaciar',
            'movimiento_masa' => 'imagen_centro_mm',
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
    @endphp

    <div id="capture" class="p-2">
        <div class='grid grid-cols-1 lg:grid-cols-4 gap-4'>

            <div class="flex flex-col gap-4 justify-start items-center w-full">
                <div class='flex justify-center'>
                    <div class="bg-teal-600 text-white text-center font-semibold p-2 rounded">
                        @foreach (array_slice($data, 0, 1) as $index => $item)
                            RIESGO POR {{ strtoupper($tipo) }}
                        @endforeach
                    </div>
                </div>

                <x-image :src="$escenario->mapas->firstWhere('tipo', $mapaIzquierdo[$tipo])->ruta ?? ''" />

                <div class='w-full flex items-center gap-2'>
                    <span class="text-xs flex-shrink-0">Fuente: CENEPRED (2025)</span>
                    <a href={{ $escenario->url_base }} target="_blank" rel="noreferrer"
                        class="block bg-teal-600 p-2 text-white rounded-md overflow-hidden whitespace-nowrap text-ellipsis">
                        Ver Informe Escenario de Riesgo
                    </a>
                </div>
            </div>

            <div class='col-span-3 flex flex-col gap-2 justify-center items-center'>
                <div class='flex-1 flex flex-col items-center text-center'>
                    <h4 class="text-center font-semibold text-teal-600">
                        {{ $escenario->nombre }}
                    </h4>
                    <h4 class="text-center font-semibold text-green-600/70">
                        {{ $escenario->subtitulo }}
                    </h4>
                </div>

                <div class='flex justify-center items-center gap-4'>
                    <span class='text-sm font-bold'>ELEMENTOS EXPUESTOS A NIVEL NACIONAL</span>
                    @foreach (array_slice($data, 0, 1) as $index => $item)
                        <div
                            class="{{ $nivelColorClasses[strtoupper($item['nivel'])] }} text-white text-center text-sm
                        font-semibold p-2 rounded">
                            {{ strtoupper($item['nivel']) }}
                        </div>
                    @endforeach
                </div>

                <div class='w-full flex justify-center items-center'>
                    <x-image :src="$escenario->mapas->firstWhere('tipo', $mapaCentro[$tipo])->ruta ?? ''" />
                </div>

                @foreach (array_slice($data, 0, 1) as $index => $item)
                    <div class='flex flex-col gap-5 items-center'>
                        <div class="flex flex-col gap-3 justify-center items-center">

                            <div
                                class="flex flex-wrap justify-center items-center gap-6 bg-gray-200/50 rounded-xl p-4 max-w-full overflow-hidden">
                                {{-- Población --}}
                                <div class="flex items-center gap-5">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                        viewBox="0 0 640 512" class="text-cyan-600" height="50" width="50"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z">
                                        </path>
                                    </svg>
                                    <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                        <p class="text-lg font-bold">{{ numero_formateado($item['total_poblacion']) }}</p>
                                        <p class="text-md">Población</p>
                                    </div>
                                </div>
                                {{-- Distritos --}}
                                <div class="flex items-center gap-5">
                                    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                        stroke-linecap="round" stroke-linejoin="round" class="text-cyan-600"
                                        height="50" width="50" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                        <path
                                            d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z">
                                        </path>
                                    </svg>
                                    <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                        <p class="text-lg font-bold">{{ numero_formateado($item['total_distritos']) }}</p>
                                        <p class="text-md">Distritos</p>
                                    </div>
                                </div>
                                {{-- Viviendas --}}
                                <div class="flex items-center gap-5">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                        viewBox="0 0 576 512" class="text-cyan-600" height="50" width="50"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z">
                                        </path>
                                    </svg>
                                    <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                        <p class="text-lg font-bold">{{ numero_formateado($item['total_vivienda']) }}</p>
                                        <p class="text-md">Viviendas</p>
                                    </div>
                                </div>
                                {{-- Inst. Educativas --}}
                                <div class="flex items-center gap-5">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                        class="text-cyan-600" height="50" width="50"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21 10h-2V4h1V2H4v2h1v6H3a1 1 0 0 0-1 1v9h20v-9a1 1 0 0 0-1-1zm-7 8v-4h-4v4H7V4h10v14h-3z">
                                        </path>
                                        <path d="M9 6h2v2H9zm4 0h2v2h-2zm-4 4h2v2H9zm4 0h2v2h-2z"></path>
                                    </svg>
                                    <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                        <p class="text-lg font-bold">{{ numero_formateado($item['total_inst_educativa']) }}
                                        </p>
                                        <p class="text-md">Inst. Educativas</p>
                                    </div>
                                </div>
                                {{-- Est. Salud --}}
                                <div class="flex items-center gap-5">
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
                                        <p class="text-lg font-bold">{{ numero_formateado($item['total_est_salud']) }}</p>
                                        <p class="text-md">Est. de Salud</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="flex flex-col gap-3 justify-center items-center">

                            <div
                                class="flex flex-wrap justify-center items-center gap-6 bg-gray-200/50 rounded-xl p-4 max-w-full overflow-hidden">
                                {{-- Red viales --}}
                                <div class="flex items-center gap-5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-cyan-600" viewBox="0 0 640 640" fill="currentColor" height="50" width="50">
                                        <path
                                            d="M147 170.7L117.2 256L240.1 256L240.1 160L162.2 160C155.4 160 149.3 164.3 147.1 170.7zM48.6 257.9L86.5 149.6C97.8 117.5 128.1 96 162.1 96L360 96C385.2 96 408.9 107.9 424 128L520.2 256.3C587.1 260.5 640 316.1 640 384L640 400C640 435.3 611.3 464 576 464L559.6 464C555.6 508.9 517.9 544 472 544C426.1 544 388.4 508.9 384.4 464L239.7 464C235.7 508.9 198 544 152.1 544C106.2 544 68.5 508.9 64.5 464L64.1 464C28.8 464 .1 435.3 .1 400L.1 320C.1 289.9 20.8 264.7 48.7 257.9zM440 256L372.8 166.4C369.8 162.4 365 160 360 160L288 160L288 256L440 256zM152 496C174.1 496 192 478.1 192 456C192 433.9 174.1 416 152 416C129.9 416 112 433.9 112 456C112 478.1 129.9 496 152 496zM512 456C512 433.9 494.1 416 472 416C449.9 416 432 433.9 432 456C432 478.1 449.9 496 472 496C494.1 496 512 478.1 512 456z" />
                                    </svg>
                                    <div class='flex flex-col gap-2 items-start'>
                                        <div class='flex-1 flex gap-2 font-semibold items-center text-teal-600'>
                                            <p class="text-lg font-bold">
                                                {{ numero_formateado($item['total_red_vial_nacional']) }}</p>
                                            <p class="text-md">Red Vial Nacional</p>
                                        </div>
                                        <div class='flex-1 flex gap-2 font-semibold items-center text-teal-600'>
                                            <p class="text-lg font-bold">
                                                {{ numero_formateado($item['total_red_vial_departamental']) }}</p>
                                            <p class="text-md">Red Vial Departamental</p>
                                        </div>
                                        <div class='flex-1 flex gap-2 font-semibold items-center text-teal-600'>
                                            <p class="text-lg font-bold">
                                                {{ numero_formateado($item['total_red_vial_vecinal']) }}
                                            </p>
                                            <p class="text-md">Red Vial Vecinal</p>
                                        </div>
                                    </div>
                                </div>
                                {{-- Distritos --}}
                                <div class="flex items-center gap-5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-cyan-600" viewBox="0 0 640 640" fill="currentColor" height="50" width="50">
                                        <path
                                            d="M256 128C256 110.3 270.3 96 288 96C305.7 96 320 110.3 320 128L416 128C433.7 128 448 142.3 448 160C448 177.7 433.7 192 416 192L320 192L320 256L338.7 256C347.2 256 355.3 259.4 361.3 265.4L383.9 288L415.9 288C504.3 288 575.9 359.6 575.9 448C575.9 465.7 561.6 480 543.9 480L479.9 480C462.2 480 447.9 465.7 447.9 448C447.9 430.3 433.6 416 415.9 416L379.8 416C359.6 445 325.9 464 287.9 464C249.9 464 216.2 445 196 416L96 416C78.3 416 64 401.7 64 384L64 320C64 302.3 78.3 288 96 288L192 288L214.6 265.4C220.6 259.4 228.7 256 237.2 256L255.9 256L255.9 192L159.9 192C142.2 192 127.9 177.7 127.9 160C127.9 142.3 142.3 128 160 128L256 128z" />
                                    </svg>
                                    <div
                                        class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                        <p class="text-lg font-bold">{{ numero_formateado($item['total_red_agua']) }}</p>
                                        <p class="text-md">Red de Agua Potable (Tuberias)</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>

    </div>
</body>

</html>

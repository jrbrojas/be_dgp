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
        $data = $plantillas;

        $nivelColorClasses = [
            'MUY ALTO' => 'text-red-500 bg-red-500',
            'ALTO' => 'text-orange-400 bg-orange-400',
            'MEDIO' => 'text-yellow-500 bg-yellow-500',
            'BAJO' => 'text-green-400 bg.green-400',
            'MUY BAJO' => 'text-green-700 bg-green-700',
            '' => 'text-gray-500 bg-gray-500',
        ];

    @endphp

    <div id="capture" class="p-2">
        <div class='flex justify-between gap-4 items-center mb-3'>
            <div class="text-md p-2 font-semibold text-white bg-teal-600 rounded-full">
                <p>PLAN MULTISECTORIAL</p>
            </div>
        </div>

        <div class='flex-1 flex flex-col items-center text-center mb-5'>
            <h4 class="font-bold text-teal-600">
                ESCENARIO DE RIESGO POR INUNDACIONES Y MOVIMIENTOS EN MASA
            </h4>
            <h4 class='font-bold text-green-600/70'>{{ $escenario->nombre }}</h4>
        </div>

        <div class='grid grid-cols-1 lg:grid-cols-2 gap-5 items-stretch'>

            <div class='flex flex-col gap-4 items-stretch justify-start'>
                <div class='grid grid-cols-2 items-stretch border rounded-xl border-teal-600'>
                    <div class='w-full h-full p-3 flex justify-center items-center aspect-[3/4]'>
                        @if ($escenario->mapas)
                            <x-image src="{{ $escenario->mapas[0] ? $escenario->mapas[0]->ruta : '' }}"
                                alt="Mapa principal" ratio="16/9" />
                        @endif
                    </div>
                    <div class='flex flex-col text-center justify-start items-center mt-3'>
                        <span class='font-bold text-teal-600 '>ESCENARIO DE RIESGO POR</span>
                        <span class='font-bold text-green-600/70'>INUNDACIONES</span>
                        <div class="w-full mt-3">
                            <div class="overflow-x-auto w-full p-2">
                                <table
                                    class="sm:min-w-full min-w-[560px] table-fixed border-separate border-spacing-0 w-full">
                                    <thead>
                                        <tr>
                                            <th class="bg-gray-400 text-white text-center">Riesgo</th>
                                            @foreach (array_slice($data['inundaciones'], 0, 2) as $index => $item)
                                                <th
                                                    class="{{ $nivelColorClasses[strtoupper($item['nivel'])] }} text-white text-center">
                                                    {{ $item['nivel'] }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">Distritos: </td>
                                            @foreach (array_slice($data['inundaciones'], 0, 2) as $index => $item)
                                                <td key={index} class="text-start p-2 text-xs">
                                                    {{ numero_formateado($item['total_distritos']) }}
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">Población: </td>
                                            @foreach (array_slice($data['inundaciones'], 0, 2) as $index => $item)
                                                <td key={index} class="text-start p-2 text-xs">
                                                    {{ numero_formateado($item['total_poblacion']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">Viviendas: </td>
                                            @foreach (array_slice($data['inundaciones'], 0, 2) as $index => $item)
                                                <td key={index} class="text-start p-2 text-xs">
                                                    {{ numero_formateado($item['total_vivienda']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">E. Salud: </td>
                                            @foreach (array_slice($data['inundaciones'], 0, 2) as $index => $item)
                                                <td key={index} class="text-start p-2 text-xs">
                                                    {{ numero_formateado($item['total_est_salud']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">I. Educa.: </td>
                                            @foreach (array_slice($data['inundaciones'], 0, 2) as $index => $item)
                                                <td key={index} class="text-start p-2 text-xs">
                                                    {{ numero_formateado($item['total_inst_educativa']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">S. Agrícola: </td>
                                            @foreach (array_slice($data['inundaciones'], 0, 2) as $index => $item)
                                                <td key={index} class="text-start p-2 text-xs">
                                                    {{ numero_formateado($item['total_superficie_agricola']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">Vias (Km): </td>
                                            @foreach (array_slice($data['inundaciones'], 0, 2) as $index => $item)
                                                <td key={index} class="text-start p-2 text-xs">
                                                    {{ numero_formateado($item['total_vias']) }}</td>
                                            @endforeach
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <div class='flex items-center gap-3 mt-3'>
                        <span class="text-xs flex-shrink-0">Fuente: CENEPRED (2025)</span>
                        <a href={escenario.url_base} target="_blank" rel="noreferrer" title={escenario.url_base}
                            class="block bg-teal-600 p-2 text-white rounded-md overflow-hidden whitespace-nowrap text-ellipsis">
                            Ver Informe Escenario de Riesgo
                        </a>
                    </div>
                </div>

            </div>

            <div class='flex flex-col gap-4 items-stretch justify-start'>
                <div class='grid grid-cols-2 items-stretch border rounded-xl border-teal-600'>
                    <div class='w-full h-full p-4 flex justify-center items-center aspect-[3/4]'>
                        @if ($escenario->mapas)
                            <x-image src="{{ $escenario->mapas[1] ? $escenario->mapas[1]->ruta : '' }}"
                                alt="Mapa principal" ratio="16/9" />
                        @endif
                    </div>
                    <div class='flex flex-col text-center justify-start items-center mt-3'>
                        <span class='font-bold text-teal-600 '>ESCENARIO DE RIESGO POR</span>
                        <span class='font-bold text-green-600/70'>MOVIMIENTO EN MASA</span>
                        <div class="w-full mt-3">
                            <div class="overflow-x-auto w-full p-2">
                                <table
                                    class="sm:min-w-full min-w-[560px] table-fixed border-separate border-spacing-0 w-full">
                                    <thead>
                                        <tr>
                                            <th class="bg-gray-400 text-white text-center">Riesgo</th>
                                            @foreach (array_slice($data['movimiento_masa'], 0, 2) as $index => $item)
                                            <th class="{{ $nivelColorClasses[strtoupper($item['nivel'])] }} text-white text-center">
                                                {{ $item['nivel'] }}
                                            </th>
                                            @endforeach
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">Distritos: </td>
                                            @foreach (array_slice($data['movimiento_masa'], 0, 2) as $index => $item)
                                            <td key={index} class="text-start p-2 text-xs">
                                                {{ numero_formateado($item['total_distritos']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">Población: </td>
                                            @foreach (array_slice($data['movimiento_masa'], 0, 2) as $index => $item)
                                            <td key={index} class="text-start p-2 text-xs">
                                                {{ numero_formateado($item['total_poblacion']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">Viviendas: </td>
                                            @foreach (array_slice($data['movimiento_masa'], 0, 2) as $index => $item)
                                            <td key={index} class="text-start p-2 text-xs">
                                                {{ numero_formateado($item['total_vivienda']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">E. Salud: </td>
                                            @foreach (array_slice($data['movimiento_masa'], 0, 2) as $index => $item)
                                            <td key={index} class="text-start p-2 text-xs">
                                                {{ numero_formateado($item['total_est_salud']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">I. Educa.: </td>
                                            @foreach (array_slice($data['movimiento_masa'], 0, 2) as $index => $item)
                                            <td key={index} class="text-start p-2 text-xs">
                                                {{ numero_formateado($item['total_inst_educativa']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">S. Agrícola: </td>
                                            @foreach (array_slice($data['movimiento_masa'], 0, 2) as $index => $item)
                                            <td key={index} class="text-start p-2 text-xs">
                                                {{ numero_formateado($item['total_superficie_agricola']) }}</td>
                                            @endforeach
                                        </tr>
                                        <tr class="odd:bg-gray-50">
                                            <td class="text-start p-2 text-xs">Vias (Km): </td>
                                            @foreach (array_slice($data['movimiento_masa'], 0, 2) as $index => $item)
                                            <td key={index} class="text-start p-2 text-xs">
                                                {{ numero_formateado($item['total_vias']) }}</td>
                                            @endforeach
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</body>

</html>

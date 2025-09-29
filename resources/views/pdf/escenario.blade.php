<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Escenario PDF</title>
    {{-- <link href="{{ public_path('build/assets/app-yTUvw4Cf.css') }}" rel="stylesheet"> --}}
    <style>
        {!! $css !!}
    </style>
</head>

<body class="p-10 font-sans text-sm text-gray-800">

    @php
        $nivelColorClasses = [
            'MUY ALTO' => 'text-red-500 bg-red-500',
            'ALTO' => 'text-orange-400 bg-orange-400',
            'MEDIO' => 'text-yellow-500 bg-yellow-500',
            'BAJO' => 'text-green-400 bg.green-400',
            'MUY BAJO' => 'text-green-700 bg-green-700',
            '' => 'text-gray-500 bg-gray-500',
        ];
    @endphp
    @foreach (array_keys($data) as $index => $tipo)
        <div class="p-6">

            <div class='flex justify-between items-center mb-3'>
                <div class="text-2xl p-2 font-medium text-white bg-teal-600 rounded-lg">
                    <p class='mr-3'>Aviso N° {{ $escenario->aviso }}</p>
                </div>
                <h2 class="text-2xl p-2 font-medium text-white bg-teal-600 rounded-full">
                    <p class='mr-5 ml-5'>CORTO PLAZO</p>
                </h2>
            </div>

            <div class='flex justify-between items-center mb-10'>
                <h1 class="text-5xl font-semibold text-teal-600 ml-8">Escenario de Riesgo</h1>
                <div class="text-2xl p-2 font-semibold text-white bg-green-600 bg-opacity-70 rounded-xl mr-10">
                    <p class='ml-3 mr-3'>{{ $escenario->nombre }}</p>
                </div>
            </div>

            <div class='grid grid-cols-1 lg:grid-cols-3 gap-6'>
                <div class='col-span-2'>
                    <div class='flex flex-col gap-3 justify-center items-center mb-3'>
                        <div class="bg-green-600 bg-opacity-70 text-white text-2xl font-medium rounded-lg">
                            <p class='mr-15 ml-15'>Pronóstico de Lluvia {{ date('Y') }}</p>
                        </div>
                         <div class='text-teal-600'>
                            <p class='text-3xl font-semibold'>
                                {{ $tipo == 'inundaciones' ? 'Inundación' : 'Movimiento en masa' }}</p>
                            <p class='text-lg ml-4'>
                                del {{ \Carbon\Carbon::parse($escenario->fecha_inicio)->format('d') }}
                                al {{ \Carbon\Carbon::parse($escenario->fecha_fin)->format('d') }}
                                de {{ \Carbon\Carbon::parse($escenario->fecha_fin)->translatedFormat('F') }}
                            </p>
                        </div>
                    </div>

                    <div class='grid grid-cols-1 lg:grid-cols-2 gap-3'>
                        @foreach (array_slice($data[$tipo], 0, 2) as $item)
                            @php
                                $departamentosRaw = trim($item['departamentos'], '{}');
                                $departamentosArray = array_filter(
                                    explode(',', $departamentosRaw),
                                    fn($d) => $d !== 'NULL',
                                );
                            @endphp

                            <div class="grid grid-cols-2 border rounded-xl border-teal-600 shadow-md bg-white">
                                <div class="p-2 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <TbMapPin class="text-cyan-600" size={50} />
                                        <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                            <p class="text-xl font-bold">
                                                {{ $tipo == 'inundaciones' ? $item['total_centro_poblado'] : $item['total_distritos'] }}
                                            </p>
                                            <p class="text-md">
                                                {{ $tipo == 'inundaciones' ? 'Centros Poblados' : 'Distritos' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <FaUsers class="text-cyan-600" size={50} />
                                        <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                            <p class="text-xl font-bold">{{ $item['total_poblacion'] }}</p>
                                            <p class="text-md">Población</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <FaHome class="text-cyan-600" size={50} />
                                        <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                            <p class="text-xl font-bold">{{ $item['total_vivienda'] }}</p>
                                            <p class="text-md">Viviendas</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <BiSolidSchool class="text-cyan-600" size={50} />
                                        <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                            <p class="text-xl font-bold">{{ $item['total_inst_educativa'] }}</p>
                                            <p class="text-md">Inst. Educativas</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <BsHospital class="text-cyan-600" size={50} />
                                        <div class='flex-1 flex flex-col gap-1 font-semibold text-center text-teal-600'>
                                            <p class="text-xl font-bold">{{ $item['total_est_salud'] }}</p>
                                            <p class="text-md">Est. de Salud</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-l border-teal-600 p-2 flex flex-col justify-between">
                                    <div class='p-4'>
                                        <div
                                            class="{{ $nivelColorClasses[strtoupper($item['nivel'])] }} text-white text-center font-semibold py-1 rounded">
                                            {{ $item['nivel'] }}
                                        </div>
                                        <p class="text-sm text-teal-600 mt-2">
                                            Departamentos en nivel:
                                            <span
                                                class="{{ $nivelColorClasses[strtoupper($item['nivel'])] }} bg-white font-semibold">
                                                {{ implode(', ', $departamentosArray) }} </span>
                                        </p>
                                    </div>

                                    <div class="mt-4 text-sm text-teal-600 font-semibold">
                                        Departamentos con mayor población expuesta:
                                        @foreach ($item['departamentos_poblacion'] ?? [] as $depa)
                                            <p class='flex justify-between items-center'>
                                                <span class="font-bold">{{ $depa['departamento'] }}</span>
                                                {{ $depa['total_poblacion'] }}
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class='w-full flex justify-center'>
                    <img src="{{ asset('storage/' . $escenario['mapas'][$index]['ruta']) }}" alt="Mapa">
                </div>
            </div>

        </div>
    @endforeach
</body>

</html>

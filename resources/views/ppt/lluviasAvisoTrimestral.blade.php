<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  {{-- Tailwind “standalone” para render fuera del build de Vite --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Ajustes para que el screenshot quede compacto y sin scroll */
    html,body { margin:0; background:#ffffff; }
    .page { width:1280px; /* base de captura */ padding:20px; }
  </style>
</head>
<body>
  <div class="page">
    {{-- === TU CARD === --}}
    <div class="rounded-3xl bg-white p-4 shadow-xl ring-1 ring-slate-200">
      <div class="flex items-center justify-between">
        <h1 class="text-lg font-bold text-slate-700">BAJAS_TEMP_AVISO_TRIMESTRAL</h1>
        <span class="px-3 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">AVISO TRIMESTRAL</span>
      </div>

      <div class="mt-3 grid grid-cols-12 gap-4">
        {{-- Izquierda: mini-mapas --}}
        <div class="col-span-3 space-y-4">
          <img src="{{ $maps['mapa1'] }}" class="w-full rounded-xl ring-1 ring-slate-200" alt="">
          <img src="{{ $maps['mapa2'] }}" class="w-full rounded-xl ring-1 ring-slate-200" alt="">
        </div>

        {{-- Centro: mapa principal --}}
        <div class="col-span-6 flex items-center justify-center">
          <img src="{{ $maps['mapaPrincipal'] }}" class="max-h-[520px]" alt="">
        </div>

        {{-- Derecha: métricas --}}
        <div class="col-span-3">
          <div class="text-slate-500 text-sm">OCTUBRE - OCTUBRE <span class="text-emerald-700 font-extrabold text-3xl leading-none block">{{ $anio }}</span></div>

          <div class="mt-3 space-y-3">
            @foreach($kpis as $k)
              <div class="flex items-center gap-3">
                <div class="shrink-0 w-9 h-9 grid place-items-center rounded-xl ring-1 ring-slate-200">
                  {!! $k['icon'] !!}
                </div>
                <div>
                  <div class="text-xs text-slate-500">{{ $k['label'] }}</div>
                  <div class="text-xl font-bold tabular-nums">{{ number_format($k['value']) }}</div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="mt-4 rounded-xl ring-1 ring-red-200 bg-red-50 p-3">
            <div class="text-xs text-red-600 font-semibold">Muy Alto</div>
            <div class="mt-1 text-[11px] text-slate-600 leading-tight">
              Departamentos con mayor población expuesta:<br>
              @foreach($ranking as $item)
                <span class="font-medium">{{ $item['nombre'] }}</span> {{ number_format($item['poblacion']) }}<br>
              @endforeach
            </div>
          </div>

          <div class="mt-4 text-[11px] text-right text-slate-500">Fuente: {{ $fuente }}</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

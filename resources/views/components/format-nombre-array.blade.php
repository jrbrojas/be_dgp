@props(['pgArray'])

@php
$items = $getItems();
@endphp

@if (!empty($items))
    <div class="flex flex-col gap-1 text-teal-700">
        @foreach ($items as $item)
            <div>• {{ $item }}</div>
        @endforeach
    </div>
@endif

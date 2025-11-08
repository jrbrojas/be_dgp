@props(['pgArray'])

@php
$items = $getItems();
$class = $attributes->get('class', '');
@endphp

@if (count($items) === 1)
 <span class="{{ $class }}">{{ $items[0] }}</span>
@elseif  (!empty($items))
    <div class="flex flex-col gap-1 {{ $class }}">
        @foreach ($items as $item)
            <div>• {{ $item }}</div>
        @endforeach
    </div>
@endif

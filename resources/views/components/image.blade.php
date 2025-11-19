@props(['class' => ''])

@php
    $frameClasses = 'relative w-full bg-white';
    if ($border) {
        $frameClasses .= ' ring-1 ring-slate-200';
    }
    if ($shadow) {
        $frameClasses .= ' shadow-md';
    }
    $imgClasses = "w-full h-auto object-{$fit} rounded-xl";

    $style = $ratio ? "aspect-ratio: {$ratio};" : '';

    \Log::info($src);
@endphp

<figure {{ $attributes->merge(['class' => $class]) }}>

    @if ($src)
        <div class="{{ $frameClasses }}" style="{{ $style }}">
            <img src="{{ public_path('storage/'.$src) }}" alt="{{ $alt }}" class="{{ $imgClasses }}" />
        </div>
    @endif

    @if ($caption)
        <figcaption class="mt-1 text-xs text-center text-slate-500">{{ $caption }}</figcaption>
    @endif
</figure>

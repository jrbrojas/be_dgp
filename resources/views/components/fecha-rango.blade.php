@php
    $texto = $texto();
@endphp

<div>
    @if ($texto)
        <span> {{ $texto }} </span>
    @endif
</div>

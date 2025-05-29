@props([
    'status' => null
])

@php
    $className = 'px-2 py-1 w-fit text-xs rounded-full font-bold ';
@endphp

@if($status)
    @switch($status->type->value)
        @case('success')
            <div class="{{ $className . 'bg-lime-600/30 text-lime-400' }}">
                {{ $status->message }}
            </div>
            @break
        @case('warning')
            <div class="{{ $className . 'bg-yellow-600/30 text-yellow-400' }}">
                {{ $status->message }}
            </div>
            @break
        @case('danger')
            <div class="{{ $className . 'bg-red-600/30 text-red-400' }}">
                {{ $status->message }}
            </div>
            @break
    @endswitch
@else
<p>No status provided</p>
@endif

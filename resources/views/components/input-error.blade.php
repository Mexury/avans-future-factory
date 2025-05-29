@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'p-3 mb-4 bg-red-500 bg-opacity-20 border border-red-500 text-red-500 rounded-sm text-sm text-red-600 dark:text-red-400 space-y-1']) }}>
        <ul>
            @foreach ((array) $messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

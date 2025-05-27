@switch($attributes['variant'])
    @case('primary')
    @default
        <button {{ $attributes->merge(['type' => 'submit', 'class' => 'font-bold inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-sm text-white hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
            {{ $slot }}
        </button>
        @break
    @case('primary:outline')
        <button {{ $attributes->merge(['type' => 'submit', 'class' => 'font-bold inline-flex items-center px-4 py-2 bg-transparent border border-indigo-600 rounded-sm text-white hover:border-indigo-500 focus:border-indigo-500 active:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
            {{ $slot }}
        </button>
        @break
    @case('secondary')
        <button {{ $attributes->merge(['type' => 'submit', 'class' => 'font-bold inline-flex items-center px-4 py-2 bg-white border border-transparent rounded-sm text-black hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
            {{ $slot }}
        </button>
        @break
    @case('secondary:outline')
        <button {{ $attributes->merge(['type' => 'submit', 'class' => 'font-bold inline-flex items-center px-4 py-2 bg-transparent border border-white rounded-sm text-white hover:border-gray-200 focus:border-gray-200 active:border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
            {{ $slot }}
        </button>
        @break
    @case('danger')
        <button {{ $attributes->merge(['type' => 'submit', 'class' => 'font-bold inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-sm text-white hover:bg-red-500 focus:bg-red-500 active:bg-red-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
            {{ $slot }}
        </button>
        @break
@endswitch

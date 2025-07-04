<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Robots') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('robots.store') }}" method="POST" class="flex flex-col">
                        @csrf
                        <h1 class="text-2xl font-bold mb-4">Create a new robot</h1>

                        <div class="flex flex-col gap-2 mb-4">
                            <h2 class="text-xl font-bold mt-2">Enter a name</h2>
                            <input type="text" name="name" placeholder="Name" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('name') }}">
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <div class="flex flex-col gap-4 mb-4">
                            <h2 class="text-xl font-bold mt-2">Select supported vehicle types</h2>
                            <div class="grid grid-cols-4 gap-3">
                                @foreach($vehicleTypes as $vehicleType)
                                    <x-checkbox
                                        name="vehicle_type[{{ $vehicleType }}]"
                                        id="vehicle_type[{{ $vehicleType }}]"
                                        :checked="old('vehicle_type.' . $vehicleType) === 'true'">
                                        {{ snakeToSentenceCase($vehicleType) }}
                                    </x-checkbox>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('vehicle_type')" />
                        </div>

                        <div class="flex flex-col gap-4 mb-4">
                            <h2 class="text-xl font-bold mt-2">Select supported engine types</h2>
                            <div class="grid grid-cols-4 gap-3">
                                @foreach($engineTypes as $engineType)
                                    <x-checkbox
                                        name="engine_type[{{ $engineType }}]"
                                        id="vehicle_type[{{ $engineType }}]"
                                        :checked="old('engine_type.' . $engineType) === 'true'">
                                        {{ snakeToSentenceCase($engineType) }}
                                    </x-checkbox>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('engine_type')" />
                            <x-input-error :messages="$errors->get('selection_error')" />
                        </div>

                        <div class="flex gap-2 ml-auto">
                            <a href="{{ route('robots.index') }}" class="text-white hover:underline font-bold rounded-sm px-4 py-2">
                                Back
                            </a>

                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-sm px-4 py-2">
                                Create robot
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

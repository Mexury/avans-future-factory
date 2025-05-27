<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Vehicles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('vehicles.store') }}" method="POST" class="flex flex-col">
                        @csrf
                        <h1 class="text-2xl font-bold mb-4">Create a new vehicle</h1>
                        <div class="flex gap-2 mb-6">
                            <input type="text" name="name" placeholder="Name" class="grow p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('name') }}">
                            <select name="type" id="type" class="p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent">
                                @foreach($vehicleTypes as $key => $vehicleType)
                                    <option value="{{ $vehicleType }}" @selected(old('type') === $vehicleType || $key === 0)>
                                        {{ snakeToSentenceCase($vehicleType) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <x-input-error class="mt-1" :messages="$errors->get('name')" />

                        <div class="flex gap-2 ml-auto">
                            <a href="{{ route('vehicles.index') }}" class="text-white hover:underline font-bold rounded-sm px-4 py-2">
                                Back
                            </a>

                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-sm px-4 py-2">
                                Create vehicle
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

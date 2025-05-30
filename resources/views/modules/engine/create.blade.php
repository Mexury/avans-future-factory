<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Modules') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('engine.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
                        @csrf
                        <h1 class="text-2xl font-bold mb-4">Create a new module</h1>

                        <div class="flex gap-2">
                            <div class="flex flex-col gap-2 grow">
                                <h2 class="text-xl font-bold mt-2">Enter a name</h2>
                                <input type="text" name="name" placeholder="Name" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('name') }}">
                            </div>

                            <div class="flex flex-col gap-2 grow">
                                <h2 class="text-xl font-bold mt-2">Select the cost</h2>
                                <input type="number" min="0" max="100000" step="1" name="cost" placeholder="Cost" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('cost') }}">
                            </div>

                            <div class="flex flex-col gap-2 grow">
                                <h2 class="text-xl font-bold mt-2">Select assembly time</h2>
                                <select name="assembly_time" id="assembly_time" class="p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent grow">
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" @selected(old('assembly_time') === $i)>
                                            {{ $i * 2 }} hours
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 mt-2">
                            <x-input-error class="mb-0" :messages="$errors->get('name')" />
                            <x-input-error class="mb-0" :messages="$errors->get('cost')" />
                            <x-input-error class="mb-0" :messages="$errors->get('assembly_time')" />
                        </div>

                        <x-file-input
                            name="image"
                            label="Upload module image"
                            accept="image/*"
                            :errorMessages="$errors->get('image')"
                        />

                        <div class="flex gap-2 mt-6">
                            <div class="flex flex-col gap-2 mb-6 grow">
                                <h2 class="text-xl font-bold mt-2">Select an engine type</h2>
                                <div class="flex gap-2 mb-4">
                                    @foreach($engineTypes as $engineType)
                                        <x-radio
                                            class="grow"
                                            name="type"
                                            id="type_{{ $engineType }}"
                                            value="{{ $engineType  }}"
                                            :checked="old('type') == $engineType">
                                            {{ snakeToSentenceCase($engineType) }}
                                        </x-radio>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('type')" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 mb-4">
                            <h2 class="text-xl font-bold mt-2">Enter horse power</h2>
                            <input type="number" name="horse_power" placeholder="Horse power" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('horse_power') }}">
                            <x-input-error :messages="$errors->get('horse_power')" />
                        </div>

                        <div class="flex gap-2 ml-auto">
                            <x-link href="{{ route('modules.index') }}">Back</x-link>

                            <x-button>Create module</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

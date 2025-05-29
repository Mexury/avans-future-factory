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
                    <form action="{{ route('chassis.store') }}" method="POST" class="flex flex-col">
                        @csrf
                        <h1 class="text-2xl font-bold mb-4">Create a new chassis module</h1>

                        <div class="flex gap-2">
                            <div class="flex flex-col gap-2 mb-4 grow">
                                <h2 class="text-xl font-bold mt-2">Enter a name</h2>
                                <input type="text" name="name" placeholder="Name" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('name') }}">
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            <div class="flex flex-col gap-2 mb-4 grow">
                                <h2 class="text-xl font-bold mt-2">Enter a cost</h2>
                                <input type="number" min="0" max="100000" name="cost" placeholder="Cost" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('cost') }}">
                                <x-input-error :messages="$errors->get('cost')" />
                            </div>

                            <div class="flex flex-col gap-2 mb-6 grow">
                                <h2 class="text-xl font-bold mt-2">Select a module type</h2>
                                <select name="type" id="type" class="p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent grow">
                                    @foreach($moduleTypes as $moduleType)
                                        <option value="{{ $moduleType }}" @selected(old('type') === $moduleType)>
                                            {{ snakeToSentenceCase($moduleType) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('type')" />
                            </div>
                        </div>

                        // TODO: Add image
                        <div class="flex flex-col gap-2 mb-4 grow">
                            <h2 class="text-xl font-bold mt-2">Enter a cost</h2>
                            <input type="number" min="0" max="100000" name="cost" placeholder="Cost" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('cost') }}">
                            <x-input-error :messages="$errors->get('cost')" />
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

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
                    <x-forms.modules.create module="wheel_set">
                        <div class="flex gap-2">
                            <div class="flex flex-col gap-2 grow">
                                <h2 class="text-xl font-bold mt-2">
                                    Enter diameter<span class="text-red-500">*</span></h2>
                                <input required type="text" name="diameter" placeholder="Diameter (in inches)" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('diameter') }}">
                            </div>
                            <div class="flex flex-col gap-2 grow">
                                <h2 class="text-xl font-bold mt-2">
                                    Select wheel type<span class="text-red-500">*</span>
                                </h2>
                                <select required name="type" id="type" class="p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent grow">
                                    @foreach($wheelTypes as $wheelType)
                                        <option value="{{ $wheelType }}" @selected(old('type') === $wheelType)>
                                            {{ snakeToSentenceCase($wheelType) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-col gap-2 grow">
                                <h2 class="text-xl font-bold mt-2">
                                    Enter wheel quantity<span class="text-red-500">*</span>
                                </h2>
                                <select required name="wheel_quantity" id="wheel_quantity" class="p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent grow">
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i * 2 }}" @selected(old('wheel_quantity') === $i)>
                                            {{ $i * 2 }} wheels
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <x-slot:formErrors>
                            <x-input-error :messages="$errors->get('special_adjustments')" />
                            <x-input-error :messages="$errors->get('shape')" />
                        </x-slot:formErrors>
                    </x-forms.modules.create>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

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
                    <x-forms.modules.create module="chassis">
                        <div class="flex gap-2">
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
                            <div class="flex flex-col gap-2 grow">
                                <h2 class="text-xl font-bold mt-2">
                                    Select vehicle type<span class="text-red-500">*</span>
                                </h2>
                                <select required name="vehicle_type" id="vehicle_type" class="p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent grow">
                                    @foreach($vehicleTypes as $vehicleType)
                                        <option value="{{ $vehicleType }}" @selected(old('vehicle_type') === $vehicleType)>
                                            {{ snakeToSentenceCase($vehicleType) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 mt-4 grow">
                            <h2 class="text-xl font-bold mt-2">
                                Enter dimensions<span class="text-red-500">*</span>
                            </h2>
                            <div class="flex gap-2 grow">
                                <div class="flex gap-2 grow items-center">
                                    <input required type="number" min="1" max="1000" step="1" name="length" placeholder="Length (in cm)" class="p-3 px-4 grow rounded-sm bg-transparent border-gray-600" value="{{ old('length') }}">
                                    <span class="text-3xl px-2 text-gray-500">&times;</span>
                                    <input required type="number" min="1" max="1000" step="1" name="width" placeholder="Width (in cm)" class="p-3 px-4 grow rounded-sm bg-transparent border-gray-600" value="{{ old('width') }}">
                                    <span class="text-3xl px-2 text-gray-500">&times;</span>
                                    <input required type="number" min="1" max="1000" step="1" name="height" placeholder="Height (in cm)" class="p-3 px-4 grow rounded-sm bg-transparent border-gray-600" value="{{ old('height') }}">
                                </div>
                            </div>
                        </div>
                        <x-slot:formErrors>
                            <x-input-error class="mb-0 grow" :messages="$errors->get('length')" />
                            <x-input-error class="mb-0 grow" :messages="$errors->get('width')" />
                            <x-input-error class="mb-0 grow" :messages="$errors->get('height')" />
                            <x-input-error class="mb-0 grow" :messages="$errors->get('wheel_quantity')" />
                            <x-input-error class="mb-0 grow" :messages="$errors->get('vehicle_type')" />
                        </x-slot:formErrors>
                    </x-forms.modules.create>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

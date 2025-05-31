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
                    <x-forms.modules.create module="steering_wheel">
                        <div class="flex gap-2">
                            <div class="flex flex-col gap-2 grow">
                                <h2 class="text-xl font-bold mt-2">Enter special adjustments</h2>
                                <input type="text" name="special_adjustments" placeholder="Special adjustments" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('special_adjustments') }}">
                            </div>
                            <div class="flex flex-col gap-2 grow">
                                <h2 class="text-xl font-bold mt-2">
                                    Select shape<span class="text-red-500">*</span>
                                </h2>
                                <select required name="shape" id="shape" class="p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent grow">
                                    @foreach($steeringWheelShapes as $steeringWheelShape)
                                        <option value="{{ $steeringWheelShape }}" @selected(old('shape') === $steeringWheelShape)>
                                            {{ snakeToSentenceCase($steeringWheelShape) }}
                                        </option>
                                    @endforeach
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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Vehicle Composition') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('compositions.store') }}" method="POST" class="flex flex-col">
                        @csrf
                        <h1 class="text-2xl font-bold mb-4">Create a new vehicle composition</h1>

                        <h2 class="text-xl font-bold mb-2">Select a vehicle</h2>
                        <div class="flex gap-2 mb-6">
                            <select name="vehicle_id" id="vehicle_id" class="grow p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent">
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') === $vehicle->id)>
                                        {{ $vehicle->name }} - {{ snakeToSentenceCase($vehicle->type->value) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <h2 class="text-xl font-bold mb-2">Select modules</h2>
                        <p class="text-gray-400 mb-4">Select one module of each type to create a complete vehicle.</p>

                        <div id="modules_container" class="space-y-4 mb-6">
                            @foreach($moduleTypes as $moduleType)
                                <div class="border border-gray-600 rounded-sm p-4">
                                    <h3 class="font-bold mb-2 capitalize">{{ snakeToSentenceCase($moduleType->value) }}</h3>
                                    <select name="modules[{{ $moduleType->value }}]" id="module_{{ $moduleType->value }}"
                                            class="w-full p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent module-selector"
                                            data-module-type="{{ $moduleType->value }}">
                                        <option value="">Select {{ $moduleType->value }} module</option>
                                        @foreach($modules[$moduleType->value] as $module)
                                            <option value="{{ $module->id }}"
                                                    data-assembly-time="{{ $module->assembly_time }}"
                                                    data-cost="{{ $module->cost }}"
                                                    @selected(old("modules.{$moduleType->value}") == $module->id)>
                                                {{ $module->name }} (Assembly time: {{ $module->assembly_time }} hr, Cost: ${{ $module->cost }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('modules.' . $moduleType->value)" class="mt-2" />

                                    <div class="mt-4 module-details hidden" id="details_{{ $moduleType->value }}">
                                        <!-- Module details will be shown here -->
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="bg-gray-700 p-4 rounded-sm mb-6">
                            <h3 class="font-bold text-lg mb-2">Composition Summary</h3>
                            <div class="flex justify-between mb-2">
                                <span>Total Assembly Time:</span>
                                <span id="total_assembly_time">0 hours</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Total Cost:</span>
                                <span id="total_cost">$0</span>
                            </div>
                        </div>

                        <div class="flex gap-2 ml-auto">
                            <a href="{{ route('compositions.index') }}" class="text-white hover:underline font-bold rounded-sm px-4 py-2">
                                Back
                            </a>

                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-sm px-4 py-2">
                                Create Composition
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const moduleSelectors = document.querySelectorAll('.module-selector');
            const totalAssemblyTimeElement = document.getElementById('total_assembly_time');
            const totalCostElement = document.getElementById('total_cost');

            // Initialize tracking variables
            let totalAssemblyTime = 0;
            let totalCost = 0;

            // Add event listeners to all module selectors
            moduleSelectors.forEach(selector => {
                selector.addEventListener('change', function() {
                    updateModuleDetails(this);
                    updateTotals();
                });
            });

            // Function to update module details section
            function updateModuleDetails(selector) {
                const moduleType = selector.dataset.moduleType;
                const detailsContainer = document.getElementById(`details_${moduleType}`);
                const selectedOption = selector.options[selector.selectedIndex];

                if (selector.value) {
                    const assemblytime = selectedOption.dataset.assemblyTime;
                    const cost = selectedOption.dataset.cost;

                    detailsContainer.innerHTML = `
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="bg-gray-700 p-2 rounded">
                                <span class="block text-gray-400">Assembly Time</span>
                                <span class="font-bold">${assemblytime} hour${assemblytime != 1 ? 's' : ''}</span>
                            </div>
                            <div class="bg-gray-700 p-2 rounded">
                                <span class="block text-gray-400">Cost</span>
                                <span class="font-bold">$${cost}</span>
                            </div>
                        </div>
                    `;
                    detailsContainer.classList.remove('hidden');
                } else {
                    detailsContainer.classList.add('hidden');
                }
            }

            // Function to update total assembly time and cost
            function updateTotals() {
                totalAssemblyTime = 0;
                totalCost = 0;

                moduleSelectors.forEach(selector => {
                    if (selector.value) {
                        const selectedOption = selector.options[selector.selectedIndex];
                        totalAssemblyTime += parseInt(selectedOption.dataset.assemblyTime || 0);
                        totalCost += parseFloat(selectedOption.dataset.cost || 0);
                    }
                });

                totalAssemblyTimeElement.textContent = `${totalAssemblyTime} hour${totalAssemblyTime != 1 ? 's' : ''}`;
                totalCostElement.textContent = `$${totalCost.toFixed(2)}`;
            }

            // Initialize details for any pre-selected modules (from old input)
            moduleSelectors.forEach(selector => {
                if (selector.value) {
                    updateModuleDetails(selector);
                }
            });

            // Calculate initial totals
            updateTotals();
        });
    </script>
</x-app-layout>

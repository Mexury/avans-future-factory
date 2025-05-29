<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Calendar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('calendar.store', [$year, $month, $day]) }}" method="POST" class="flex flex-col">
                        @csrf
                        <h2 class="text-xl font-bold mb-2">Select a module</h2>
                        <div class="flex flex-col gap-2 mb-6">
                            <select name="module_id" id="module_id" class="grow p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent">
                                @foreach($modules as $key => $module)
                                    <option value="{{ $module->id }}" @selected(old('module_id') === $module->id || $key === 0)>
                                        {{ snakeToSentenceCase($module->type->value) }} &rightarrow; {{ $module->name }}
                                        @switch($module->type->value)
                                            @case('chassis_modules')
                                                ({{ $module->chassisModule->wheel_quantity }} wheels)
                                                ({{ $module->chassisModule->length }}cm &times; {{ $module->chassisModule->width }}cm &times; {{ $module->chassisModule->height }}cm)
                                                @break
                                            @case('engine')
                                                {{ $module->engineModule }}
                                                @break
                                            @case('seating')
                                                {{ $module->seatingModule }}
                                                @break
                                            @case('steering_wheel')
                                                {{ $module->steeringWheelModule }}
                                                @break
                                            @case('wheel_set')
                                                ({{ snakeToSentenceCase($module->wheelSetModule->type->value) }})
                                                ({{ $module->wheelSetModule->diameter }}")
                                                ({{ $module->wheelSetModule->wheel_quantity }} wheels)
                                                @break
                                        @endswitch()
                                        ({{ $module->assembly_time }} timeslot{{ $module->assembly_time === 1 ? '' : 's'}})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-gray-400 text-sm italic">Please note the timeslots, you will need to enter this exact amount below.</p>
                            <x-input-error class="mt-1" :messages="$errors->get('module_id')" />
                        </div>

                        <h2 class="text-xl font-bold mb-2">Select a robot</h2>
                        <div class="flex gap-2 mb-4">
                            @foreach($robots as $key => $robot)
                                <x-radio
                                    class="grow"
                                    name="robot_id"
                                    id="robot_id_{{ $robot['id'] }}"
                                    value="{{ $robot['id'] }}"
                                    :checked="old('robot_id') == $robot['id']">
                                    {{ $robot['name'] }}
                                </x-radio>
                            @endforeach
                        </div>
                        <x-input-error class="mb-4" :messages="$errors->get('robot_id')" />

                        <h2 class="text-xl font-bold mb-2">Select a timeslot</h2>
                        <div class="flex gap-2 mb-4">
                            @foreach($slots as $key => $value)
                                <x-checkbox
                                    class="grow"
                                    name="slot[{{ $key + 1 }}]"
                                    id="slot[{{ $key + 1 }}]"
                                    :checked="old('slot.' . $key + 1) === 'true'">
                                    Slot {{ $key + 1 }} ({{ $value['start_time'] }} - {{ $value['end_time'] }})
                                </x-checkbox>
                            @endforeach
                        </div>
                        <x-input-error class="mb-4" :messages="$errors->get('slot')" />

                        <h2 class="text-xl font-bold mb-2">Select a vehicle</h2>
                        <div class="flex gap-2 mb-6">
                            <select name="vehicle_id" id="vehicle_id" class="grow p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent">
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') === $vehicle->id || $key === 0)>
                                        {{ $vehicle->user->name }} - {{ $vehicle->name }} - {{ $vehicle->created_at }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-1" :messages="$errors->get('vehicle_id')" />
                        </div>

                        <div class="flex gap-2 ml-auto">
                            <a href="{{ route('calendar.show', [$year, $month, $day]) }}" class="text-white hover:underline font-bold rounded-sm px-4 py-2">
                                Back
                            </a>

                            <x-button variant="primary">
                                Create schedule
                            </x-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

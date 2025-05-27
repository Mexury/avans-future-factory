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

                    <form action="/" method="POST" class="flex flex-col">
                        <h2 class="text-xl font-bold mb-2">Select a robot</h2>
                        <div class="flex gap-2 mb-6">
                            @foreach($robots as $key => $robot)
                                <label for="robot_id[{{ $robot['id'] }}]" class="big-radio p-3 px-4 rounded-sm w-1/3 cursor-pointer border border-gray-600 text-gray-600 font-bold has-[input[type=radio]:checked]:bg-gray-600 has-[input[type=radio]:checked]:text-white [&:not(:has(input[type=radio]:checked))]:hover:border-gray-500 [&:not(:has(input[type=radio]:checked))]:hover:text-gray-500">
                                    <input tabindex="0" type="radio" name="robot_id" id="robot_id[{{ $robot['id'] }}]" value="{{ $robot['id'] }}" @checked(old('robot_id') === $robot['id'] || $key === 0)>
                                    <label class="pointer-events-none select-none">{{ $robot['name'] }}</label>
                                </label>
                            @endforeach
                        </div>

                        <h2 class="text-xl font-bold mb-2">Select a timeslot</h2>
                        <div class="flex gap-2 mb-6">
                            @foreach($slots as $key => $value)
                                <label for="slot[{{ $key + 1 }}]" class="big-radio p-3 px-4 rounded-sm w-1/4 cursor-pointer border border-gray-600 text-gray-600 font-bold has-[input[type=radio]:checked]:bg-gray-600 has-[input[type=radio]:checked]:text-white [&:not(:has(input[type=radio]:checked))]:hover:border-gray-500 [&:not(:has(input[type=radio]:checked))]:hover:text-gray-500">
                                    <input tabindex="0" type="radio" name="slot" id="slot[{{ $key + 1 }}]" value="{{ $key + 1 }}" @checked(old('slot') === $key + 1 || $key === 0)>
                                    <label class="pointer-events-none select-none">Slot {{ $key + 1 }} ({{ $value['start_time'] }} - {{ $value['end_time'] }})</label>
                                </label>
                            @endforeach
                        </div>

                        <h2 class="text-xl font-bold mb-2">Select a vehicle</h2>
                        <div class="flex gap-2 mb-6">
                            <select name="vehicle_id" id="vehicle_id" class="grow p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent">
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') === $vehicle->id || $key === 0)>
                                        {{ $vehicle->user->name }} - {{ $vehicle->name }} - {{ $vehicle->created_at }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 ml-auto">
                            <a href="{{ route('calendar.show', [$year, $month, $day]) }}" class="text-white hover:underline font-bold rounded-sm px-4 py-2">
                                Back
                            </a>

                            <x-button variant="primary">
                                Create schedule
                            </x-button>

{{--                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-sm px-4 py-2">--}}
{{--                                Create schedule--}}
{{--                            </button>--}}
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

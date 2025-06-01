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
                    <x-table>
                        <x-slot:thead>
                            <x-table.head>#</x-table.head>
                            <x-table.head>Robot</x-table.head>
                            <x-table.head>Vehicle</x-table.head>
                            <x-table.head>Module</x-table.head>
                            <x-table.head>Slot</x-table.head>
                            <x-table.head>Date</x-table.head>
                            <x-table.head>Actions</x-table.head>
                        </x-slot:thead>
                        @forelse($vehiclePlanning as $key => $schedule)
                            <x-table.row>
                                <x-table.data>{{ $key + 1 }}</x-table.data>
                                <x-table.data>{{ $schedule->robot->name }}</x-table.data>
                                <x-table.data>{{ $schedule->vehicle->name }}</x-table.data>
                                <x-table.data>{{ $schedule->module->name }}</x-table.data>
                                <x-table.data>
                                    @if($schedule->slot_start === $schedule->slot_end)
                                        {{ $schedule->slot_start }}
                                    @else
                                        {{ $schedule->slot_start }} - {{ $schedule->slot_end }}
                                    @endif
                                </x-table.data>
                                <x-table.data>{{ $schedule->date->format('Y-m-d') }}</x-table.data>
                                <x-table.data>
                                    <form action="{{ route('calendar.destroy', [$schedule]) }}" method="POST" class="flex gap-2 justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="actions:delete" />
                                    </form>
                                </x-table.data>
                            </x-table.row>
                        @empty
                            <x-table.row>
                                <x-table.data>Empty table</x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                            </x-table.row>
                        @endforelse
                    </x-table>

                    <div class="flex mt-4">
                        <x-link variant="primary" class="ml-auto" href="{{ route('calendar.create', [$year, $month, $day]) }}">New schedule</x-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

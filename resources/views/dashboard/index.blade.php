<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-table>
                        <x-slot:thead>
                            <x-table.head>#</x-table.head>
                            <x-table.head>Name</x-table.head>
                            <x-table.head>Type</x-table.head>
                            <x-table.head>Ordered by</x-table.head>
                            <x-table.head>Total cost</x-table.head>
                            <x-table.head>Completion date</x-table.head>
                            <x-table.head>Status</x-table.head>
                            <x-table.head>Actions</x-table.head>
                        </x-slot:thead>
                        @forelse($vehicles as $key => $vehicle)
                            <x-table.row>
                                <x-table.data>{{ $key + 1}}</x-table.data>
                                <x-table.data>{{ $vehicle->name }}</x-table.data>
                                <x-table.data>{{ snakeToSentenceCase($vehicle->type->value) }}</x-table.data>
                                <x-table.data>{{ $vehicle->user->name }}</x-table.data>
                                <x-table.data>
                                    &euro;{{ number_format(array_sum($vehicle->planning->pluck('module')->pluck('cost')->toArray()), 2) }}
                                </x-table.data>
                                <x-table.data>
                                    @php $lastPlanning = $vehicle->planning->last(); @endphp
                                    {{ $lastPlanning->date->setHour(9 + 2 * $lastPlanning->slot_end)->format('Y-m-d H:i') }}
                                </x-table.data>
                                <x-table.data>
                                    <x-status :status="$vehicle->status()"/>
                                </x-table.data>
                                <x-table.data>
                                    <form action="{{ route('vehicles.destroy', [$vehicle]) }}" method="POST" class="flex gap-2 justify-end">
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
                                <x-table.data></x-table.data>
                            </x-table.row>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

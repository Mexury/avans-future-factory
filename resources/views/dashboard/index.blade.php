@php use App\UserRole; @endphp
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
                    @if(userHasRole(UserRole::CUSTOMER))
                        <x-table>
                            <x-slot:thead>
                                <x-table.head>#</x-table.head>
                                <x-table.head>Name</x-table.head>
                                <x-table.head>Type</x-table.head>
                                <x-table.head>Total cost</x-table.head>
                                <x-table.head>Completion date</x-table.head>
                                <x-table.head>Status</x-table.head>
                            </x-slot:thead>
                            @forelse($vehicles as $key => $vehicle)
                                <x-table.row>
                                    <x-table.data>{{ $key + 1}}</x-table.data>
                                    <x-table.data>{{ $vehicle->name }}</x-table.data>
                                    <x-table.data>{{ snakeToSentenceCase($vehicle->type->value) }}</x-table.data>
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
                                </x-table.row>
                            @empty
                                <x-table.row>
                                    <x-table.data>You haven't ordered any vehicles yet.</x-table.data>
                                    <x-table.data></x-table.data>
                                    <x-table.data></x-table.data>
                                    <x-table.data></x-table.data>
                                    <x-table.data></x-table.data>
                                    <x-table.data></x-table.data>
                                </x-table.row>
                            @endforelse
                        </x-table>
                    @endif
                    @if(userHasRole(UserRole::PLANNER))
                        <x-table>
                            <x-slot:thead>
                                <x-table.head>#</x-table.head>
                                <x-table.head>Name</x-table.head>
                                <x-table.head>Type</x-table.head>
                                <x-table.head>Ordered by</x-table.head>
                                <x-table.head>Total cost</x-table.head>
                                <x-table.head>Completion date</x-table.head>
                                <x-table.head>Status</x-table.head>
                            </x-slot:thead>
                            @forelse($completedVehicles as $key => $vehicle)
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
                                </x-table.row>
                            @empty
                                <x-table.row>
                                    <x-table.data>No vehicles schedules have completed yet.</x-table.data>
                                    <x-table.data></x-table.data>
                                    <x-table.data></x-table.data>
                                    <x-table.data></x-table.data>
                                    <x-table.data></x-table.data>
                                    <x-table.data></x-table.data>
                                    <x-table.data></x-table.data>
                                </x-table.row>
                            @endforelse
                        </x-table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

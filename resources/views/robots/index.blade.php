<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Robots') }}
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
                            <x-table.head>Compatible vehicle types</x-table.head>
                            <x-table.head>Compatible engine types</x-table.head>
                            <x-table.head>Actions</x-table.head>
                        </x-slot:thead>
                        @forelse($robots as $key => $robot)
                            <x-table.row>
                                <x-table.data>{{ $key + 1 }}</x-table.data>
                                <x-table.data>{{ $robot->name }}</x-table.data>
                                <x-table.data>
                                    {{
                                        $robot->vehicleTypes
                                            ->pluck('vehicle_type')
                                            ->pluck('value')
                                            ->map(fn($value) => snakeToSentenceCase($value))->implode(', ')
                                    }}
                                </x-table.data>
                                <x-table.data>
                                    {{
                                        $robot->engineTypes
                                            ->pluck('engine_type')
                                            ->pluck('value')
                                            ->map(fn($value) => snakeToSentenceCase($value))->implode(', ')
                                    }}
                                </x-table.data>
                                <x-table.data>
                                    <form action="{{ route('robots.destroy', [$robot]) }}" method="POST" class="flex gap-2 justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="actions:delete" />
                                    </form>
                                </x-table.data>
                            </x-table.row>
                        @empty
                            <x-table.row>
                                <x-table.data>No robots yet.</x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                            </x-table.row>
                        @endforelse
                    </x-table>

                    <div class="flex mt-4">
                        <x-link variant="primary" class="ml-auto" href="{{ route('robots.create') }}">New robot</x-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

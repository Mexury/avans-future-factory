@php use App\UserRole; @endphp
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
                    <x-table>
                        <x-slot:thead>
                            <x-table.head>#</x-table.head>
                            <x-table.head>Image</x-table.head>
                            <x-table.head>Name</x-table.head>
                            <x-table.head>Type</x-table.head>
                            <x-table.head>Assembly time</x-table.head>
                            <x-table.head>Assembly cost</x-table.head>
                            <x-table.head>Actions</x-table.head>
                        </x-slot:thead>
                        @forelse($modules as $key => $module)
                            <x-table.row>
                                <x-table.data>{{ $key + 1 }}</x-table.data>
                                <x-table.data>
                                    <img class="h-24 w-24 object-fit bg-white rounded-sm border border-gray-600" src="/storage/{{ $module->image }}" alt="{{ $module->name }}">
                                </x-table.data>
                                <x-table.data>{{ $module->name }}</x-table.data>
                                <x-table.data>{{ snakeToSentenceCase($module->type->value) }}</x-table.data>
                                <x-table.data>
                                    {{ $module->assembly_time * 2 }}h
                                    ({{ $module->assembly_time }} {{ $module->assembly_time == 1 ? 'timeslot' : 'timeslots' }})
                                </x-table.data>
                                <x-table.data>&euro;{{ number_format($module->cost, 2) }}</x-table.data>
                                <x-table.data>
                                    @if(userHasRole(UserRole::ADMIN, UserRole::BUYER))
                                        <form action="{{ route('modules.destroy', [$module]) }}" method="POST"
                                              class="flex gap-2 justify-end">
                                            @csrf
                                            @method('DELETE')
                                            <x-button variant="actions:delete"/>
                                        </form>
                                    @endif
                                </x-table.data>
                            </x-table.row>
                        @empty
                            <x-table.row>
                                <x-table.data>No modules yet.</x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                                <x-table.data></x-table.data>
                            </x-table.row>
                        @endforelse
                    </x-table>

                    @if(userHasRole(UserRole::ADMIN, UserRole::BUYER))
                        <div class="flex mt-4 gap-2 justify-end">
                            @foreach($moduleTypes as $moduleType)
                                <x-link variant="primary" class="flex gap-2 text-sm grow"
                                        href="{{ route($moduleType . '.create') }}">
                                    <x-tabler-plus class="h-4 w-4"/>
                                    {{ strtolower(snakeToSentenceCase($moduleType)) }} module
                                </x-link>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $composition->name }}
            </h2>
            <a href="{{ route('compositions.index') }}" class="text-indigo-500 hover:underline font-bold">
                Back to Compositions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-sm shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Vehicle Information</h3>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                            <dt class="text-gray-500 dark:text-gray-400">Name:</dt>
                            <dd>{{ $composition->vehicle->name }}</dd>

                            <dt class="text-gray-500 dark:text-gray-400">Type:</dt>
                            <dd>{{ snakeToSentenceCase($composition->vehicle->type->value) }}</dd>

                            <dt class="text-gray-500 dark:text-gray-400">Created by:</dt>
                            <dd>{{ $composition->user->name }}</dd>

                            <dt class="text-gray-500 dark:text-gray-400">Created on:</dt>
                            <dd>{{ $composition->created_at->format('M d, Y') }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-sm shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Composition Summary</h3>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                            <dt class="text-gray-500 dark:text-gray-400">Module Count:</dt>
                            <dd>{{ $composition->modules->count() }}</dd>

                            <dt class="text-gray-500 dark:text-gray-400">Assembly Time:</dt>
                            <dd>{{ $composition->total_assembly_time }} hour{{ $composition->total_assembly_time != 1 ? 's' : '' }}</dd>

                            <dt class="text-gray-500 dark:text-gray-400">Total Cost:</dt>
                            <dd>${{ number_format($composition->total_cost, 2) }}</dd>

                            <dt class="text-gray-500 dark:text-gray-400">Status:</dt>
                            <dd>
                                @if($composition->isComplete())
                                    <span class="text-green-500 font-semibold">Complete</span>
                                @else
                                    <span class="text-yellow-500 font-semibold">Incomplete</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-sm shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Actions</h3>
                        <div class="flex flex-col gap-2">
                            @if($composition->isComplete())
                                <a href="{{ route('calendar.index') }}" class="bg-green-600 hover:bg-green-500 text-white font-bold rounded-sm px-4 py-2 text-center">
                                    Schedule Assembly
                                </a>
                            @endif

                            <a href="{{ route('compositions.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-sm px-4 py-2 text-center">
                                Create New Composition
                            </a>

                            <form action="{{ route('compositions.destroy', $composition) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold rounded-sm px-4 py-2"
                                        onclick="return confirm('Are you sure you want to delete this composition?')">
                                    Delete Composition
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-bold mb-6">Selected Modules</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($composition->modules as $module)
                            <div class="border border-gray-700 rounded-sm overflow-hidden">
                                <div class="aspect-video bg-gray-900 overflow-hidden flex items-center justify-center">
                                    <img src="{{ $module->image }}" alt="{{ $module->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold">{{ $module->name }}</h4>
                                        <span class="bg-indigo-700 text-xs px-2 py-1 rounded uppercase">
                                            {{ snakeToSentenceCase($module->type->value) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-gray-400">Assembly Time:</span>
                                        <span>{{ $module->assembly_time }} hour{{ $module->assembly_time != 1 ? 's' : '' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Cost:</span>
                                        <span>${{ number_format($module->cost, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

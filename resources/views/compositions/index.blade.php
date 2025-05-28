<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Vehicle Compositions') }}
            </h2>
            <a href="{{ route('compositions.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-sm px-4 py-2">
                Create New Composition
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-sm mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-500 text-white p-4 rounded-sm mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($compositions->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">You haven't created any vehicle compositions yet.</p>
                            <a href="{{ route('compositions.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-sm px-4 py-2">
                                Create Your First Composition
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($compositions as $composition)
                                <div class="border border-gray-700 rounded-sm overflow-hidden">
                                    <div class="bg-gray-700 p-3">
                                        <h3 class="font-bold text-lg truncate">{{ $composition->name }}</h3>
                                        <p class="text-sm text-gray-300">{{ $composition->vehicle->name }} - {{ snakeToSentenceCase($composition->vehicle->type->value) }}</p>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex justify-between mb-2">
                                            <span class="text-gray-400">Assembly Time:</span>
                                            <span>{{ $composition->total_assembly_time }} hour{{ $composition->total_assembly_time != 1 ? 's' : '' }}</span>
                                        </div>
                                        <div class="flex justify-between mb-4">
                                            <span class="text-gray-400">Total Cost:</span>
                                            <span>${{ number_format($composition->total_cost, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between mb-2">
                                            <span class="text-gray-400">Modules:</span>
                                            <span>{{ $composition->modules->count() }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Created:</span>
                                            <span>{{ $composition->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex border-t border-gray-700">
                                        <a href="{{ route('compositions.show', $composition) }}" class="flex-1 text-center py-2 hover:bg-gray-700 transition">
                                            View Details
                                        </a>
                                        <form action="{{ route('compositions.destroy', $composition) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full py-2 text-red-400 hover:bg-gray-700 transition"
                                                    onclick="return confirm('Are you sure you want to delete this composition?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

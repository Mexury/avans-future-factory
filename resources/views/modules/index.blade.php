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
                    <table class="w-full text-sm text-left trl:text-right text-gray-500 dark:text-gray-400 table-auto">
                        <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td class="px-6 py-3">Image</td>
                                <td class="px-6 py-3">Type</td>
                                <td class="px-6 py-3">Name</td>
                                <td class="px-6 py-3">Assembly Time</td>
                                <td class="px-6 py-3">Assembly Cost</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modules as $module)
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800">
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4">
                                        <img src="{{ $module->image }}" alt="Image">
                                    </td>
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4">
                                        {{ snakeToSentenceCase($module->type->value) }}
                                    </td>
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4">
                                        {{ $module->name }}
                                    </td>
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4">
                                        {{ $module->assembly_time }} {{ $module->assembly_time == 1 ? 'timeslot' : 'timeslots' }}
                                    </td>
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4">
                                        {{ $module->cost }}
                                    </td>
                                </tr>
                            @empty
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800">
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4">No modules found...</td>
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4"></td>
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4"></td>
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4"></td>
                                    <td class="border-b dark:border-gray-700 border-gray-200 px-6 py-4"></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

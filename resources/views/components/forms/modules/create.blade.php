@props([
    'module' => ''
])

<form action="{{ route($module . '.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
    @csrf
    <h1 class="text-2xl font-bold mb-6">Create a new {{ strtolower(snakeToSentenceCase($module)) }} module</h1>

    <div class="flex flex-col gap-4">
        <div class="flex gap-2">
            <div class="flex flex-col gap-2 grow">
                <h2 class="text-xl font-bold">
                    Enter a name<span class="text-red-500">*</span>
                </h2>
                <input required type="text" name="name" placeholder="Name" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('name') }}">
            </div>

            <div class="flex flex-col gap-2 grow">
                <h2 class="text-xl font-bold">
                    Select the cost<span class="text-red-500">*</span>
                </h2>
                <input required type="number" min="0" max="100000" step="1" name="cost" placeholder="Cost" class="p-3 px-4 rounded-sm bg-transparent border-gray-600" value="{{ old('cost') }}">
            </div>

            <div class="flex flex-col gap-2 grow">
                <h2 class="text-xl font-bold">
                    Select assembly time<span class="text-red-500">*</span>
                </h2>
                <select required name="assembly_time" id="assembly_time" class="p-3 px-4 rounded-sm cursor-pointer border border-gray-600 text-white font-bold bg-transparent grow">
                    @for($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" @selected(old('assembly_time') === $i)>
                            {{ $i * 2 }} hours
                        </option>
                    @endfor
                </select>
            </div>
        </div>

        <x-file-input
            name="image"
            label="Upload module image"
            accept="image/*"
        />
    </div>

    <div class="flex flex-col mt-4">
        {{ $slot }}
    </div>

    <div class="flex flex-col gap-2 mt-4">
        <x-input-error class="mb-0" :messages="$errors->get('name')" />
        <x-input-error class="mb-0" :messages="$errors->get('cost')" />
        <x-input-error class="mb-0" :messages="$errors->get('assembly_time')" />
        <x-input-error class="mb-0" :messages="$errors->get('image')" />
        {{ $formErrors }}
    </div>

    <div class="flex gap-2 ml-auto mt-4">
        <x-link href="{{ route('modules.index') }}">Back</x-link>
        <x-button>Create {{ strtolower(snakeToSentenceCase($module)) }} module</x-button>
    </div>
</form>

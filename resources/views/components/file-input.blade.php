@props([
    'name' => 'image',
    'label' => 'Upload file',
    'accept' => 'image/*'
])

<div class="flex flex-col gap-3 grow" x-data="{ fileName: 'No file chosen', previewUrl: null }">
    <h2 class="text-xl font-bold mt-2">
        {{ $label }}<span class="text-red-500">*</span>
    </h2>

    <div class="border border-gray-600 rounded-sm p-4 flex items-center justify-center bg-gray-800/30 h-48">
        <template x-if="previewUrl">
            <img :src="previewUrl" class="max-h-40 max-w-full rounded-sm object-contain" alt="Preview">
        </template>
        <template x-if="!previewUrl">
            <div class="text-gray-500 flex flex-col items-center">
                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>No image selected</span>
            </div>
        </template>
    </div>

    <div class="relative">
        <label for="{{ $name }}-upload" class="flex items-center justify-between p-3 px-4 rounded-sm bg-transparent border border-gray-600 text-gray-300 cursor-pointer hover:border-gray-500">
            <span x-text="fileName"></span>
            <span class="bg-gray-700 px-3 py-1 rounded-sm text-sm">Browse</span>
        </label>
        <input
            type="file"
            id="{{ $name }}-upload"
            name="{{ $name }}"
            class="hidden"
            accept="{{ $accept }}"
            required
            @change="
                fileName = $event.target.files.length ? $event.target.files[0].name : 'No file chosen';
                if($event.target.files.length) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewUrl = e.target.result;
                    };
                    reader.readAsDataURL($event.target.files[0]);
                } else {
                    previewUrl = null;
                }
            "
        >
    </div>
</div>

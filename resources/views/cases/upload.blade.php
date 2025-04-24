@extends('layouts.app')

@section('title', 'Upload Image')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-6">Upload Bullet/Casing Image</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('upload.process') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        @csrf

        <!-- Drag and Drop Zone -->
        <div class="mb-6">
            <label for="image-upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Image File (JPEG, PNG, TIFF)</label>
            <div id="drop-zone" class="flex justify-center items-center w-full h-64 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-semibold">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">JPEG, PNG, TIFF up to 10MB</p>
                    <input id="image-upload" name="image" type="file" class="sr-only" accept=".jpeg,.jpg,.png,.tiff,.tif" required>
                </div>
            </div>
            <div id="file-preview" class="mt-4"></div>
            @error('image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Metadata Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="case_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Case Number</label>
                <input type="text" name="case_number" id="case_number" value="{{ old('case_number') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @error('case_number')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="firearm_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Firearm Type</label>
                <input type="text" name="firearm_type" id="firearm_type" value="{{ old('firearm_type') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                 @error('firearm_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="crime_scene_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Crime Scene Notes</label>
            <textarea name="crime_scene_notes" id="crime_scene_notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('crime_scene_notes') }}</textarea>
             @error('crime_scene_notes')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Upload and Process
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('image-upload');
    const filePreview = document.getElementById('file-preview');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-indigo-600', 'dark:border-indigo-400');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-indigo-600', 'dark:border-indigo-400');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-indigo-600', 'dark:border-indigo-400');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFiles(fileInput.files);
        }
    });

    fileInput.addEventListener('change', () => {
        handleFiles(fileInput.files);
    });

    function handleFiles(files) {
        filePreview.innerHTML = ''; // Clear previous preview
        if (files.length) {
            const file = files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const previewElement = document.createElement('div');
                previewElement.classList.add('flex', 'items-center', 'space-x-4', 'p-2', 'border', 'rounded-md', 'dark:border-gray-600');
                previewElement.innerHTML = `
                    <img src="${e.target.result}" alt="Image preview" class="h-16 w-16 object-cover rounded">
                    <div>
                        <p class="text-sm font-medium">${file.name}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                    </div>
                `;
                filePreview.appendChild(previewElement);
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
@endsection

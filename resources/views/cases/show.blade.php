@extends('layouts.app')

@section('title', 'Case Details - ' . $case->case_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-4">Case: {{ $case->case_number }}</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-6">Firearm Type: {{ $case->firearm_type }} | Status: {{ ucfirst($case->status) }}</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Image Viewer & Analysis -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-medium mb-4">Evidence Images</h2>
            @if($images->count() > 0)
                <div class="mb-6">
                    {{-- Placeholder for High-resolution image viewer with zoom/rotate --}}
                    <div class="border dark:border-gray-700 rounded p-4 h-96 flex items-center justify-center text-gray-500">
                        Image Viewer Placeholder (e.g., OpenSeadragon)
                    </div>
                    {{-- Image thumbnails/selector --}}
                    <div class="flex space-x-2 mt-4 overflow-x-auto">
                        @foreach($images as $image)
                            <img src="{{ Storage::url($image->path) }}" alt="{{ $image->original_filename }}" class="h-16 w-16 object-cover rounded cursor-pointer border-2 border-transparent hover:border-indigo-500">
                        @endforeach
                    </div>
                </div>

                <h2 class="text-xl font-medium mb-4">Analysis</h2>
                {{-- Placeholder for Comparative analysis panel --}}
                <div class="border dark:border-gray-700 rounded p-4 mb-6 min-h-[200px]">
                    Comparative Analysis Panel Placeholder
                </div>

                <div class="flex justify-between items-center">
                    <form action="{{ route('case.analyze', $case->id) }}" method="POST">
                        @csrf
                        {{-- Add algorithm selection if needed --}}
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Run Analysis
                        </button>
                    </form>
                    <form action="{{ route('case.export', $case->id) }}" method="GET">
                        {{-- Add format selection (PDF/CSV) --}}
                        <input type="hidden" name="format" value="pdf">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Export Report
                        </button>
                    </form>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No images uploaded for this case yet.</p>
                <a href="{{ route('upload.form') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Upload Image
                </a>
            @endif
        </div>

        <!-- Activity Log -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-medium mb-4">Activity Log</h2>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse($activityLogs as $log)
                    <div class="text-sm border-b dark:border-gray-700 pb-2">
                        <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ $log->description }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            By {{ $log->user->name ?? 'System' }} on {{ $log->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No activity recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

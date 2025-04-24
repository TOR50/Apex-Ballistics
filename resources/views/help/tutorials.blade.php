@extends('layouts.app')

@section('title', 'Video Tutorials')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-6">Video Tutorials</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tutorials as $tutorial)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                {{-- Placeholder for video embed or thumbnail --}}
                <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-medium mb-1">{{ $tutorial->title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $tutorial->description }}</p>
                    <a href="{{ $tutorial->url }}" target="_blank" class="text-indigo-600 hover:underline text-sm">Watch Video &rarr;</a>
                </div>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 md:col-span-2 lg:col-span-3">No tutorials available yet.</p>
        @endforelse
    </div>
</div>
@endsection

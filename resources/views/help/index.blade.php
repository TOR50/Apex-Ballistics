@extends('layouts.app')

@section('title', 'Help & Documentation')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-6">Help & Documentation</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Content Area -->
        <div class="md:col-span-2 space-y-6">
            <!-- Searchable Knowledge Base -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-medium mb-4">Knowledge Base</h2>
                <input type="search" placeholder="Search documentation..." class="w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-4">
                {{-- Placeholder for search results / AI suggestions --}}
                <div class="min-h-[100px] border dark:border-gray-700 rounded p-4 text-gray-500">
                    Search results will appear here...
                </div>
            </div>

            <!-- Compliance Checklist -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-medium mb-4">Forensic Standards Compliance Checklist</h2>
                {{-- Placeholder for checklist items --}}
                <ul class="list-disc list-inside space-y-1 text-sm">
                    <li>Chain of Custody Logging (Enabled)</li>
                    <li>Data Integrity Hashing (Active)</li>
                    <li>Role-Based Access Control (Configured)</li>
                    <li>GDPR Anonymization (Available)</li>
                    <li>Regular Audit Reviews (Recommended)</li>
                </ul>
            </div>

             <!-- Feature Request -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-medium mb-4">Request a Feature</h2>
                <form id="feature-request-form" action="{{ route('help.request') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="feature-title" class="block text-sm font-medium">Title</label>
                        <input type="text" id="feature-title" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                     <div class="mb-3">
                        <label for="feature-description" class="block text-sm font-medium">Description</label>
                        <textarea id="feature-description" name="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    </div>
                     <div class="mb-4">
                        <label for="feature-priority" class="block text-sm font-medium">Priority</label>
                        <select id="feature-priority" name="priority" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Submit Request
                    </button>
                    <div id="feature-request-response" class="mt-3 text-sm"></div>
                </form>
            </div>
        </div>

        <!-- Sidebar Links -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-medium mb-4">Quick Links</h2>
                <ul class="space-y-2">
                    <li><a href="{{ route('help.api') }}" class="text-indigo-600 hover:underline">API Documentation (Swagger/OpenAPI)</a></li>
                    <li><a href="{{ route('help.tutorials') }}" class="text-indigo-600 hover:underline">Video Tutorials</a></li>
                    {{-- Add more links as needed --}}
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-medium mb-4">Popular Topics</h2>
                 <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach($popularTopics as $topic)
                        <li>{{ $topic }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Handle Feature Request Form Submission via AJAX
    const featureForm = document.getElementById('feature-request-form');
    const featureResponseDiv = document.getElementById('feature-request-response');

    featureForm.addEventListener('submit', function(e) {
        e.preventDefault();
        featureResponseDiv.textContent = 'Submitting...';
        featureResponseDiv.className = 'mt-3 text-sm text-blue-600';

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                featureResponseDiv.textContent = `${data.message}. ${data.github_issue_url ? 'Track on GitHub: ' + data.github_issue_url : ''}`;
                featureResponseDiv.className = 'mt-3 text-sm text-green-600';
                featureForm.reset();
            } else {
                // Handle validation errors or other issues
                let errorMessage = data.message || 'Submission failed.';
                if(data.errors) {
                    errorMessage += ' Errors: ' + Object.values(data.errors).flat().join(' ');
                }
                featureResponseDiv.textContent = errorMessage;
                featureResponseDiv.className = 'mt-3 text-sm text-red-600';
            }
        })
        .catch(error => {
            console.error('Error submitting feature request:', error);
            featureResponseDiv.textContent = 'An error occurred during submission.';
            featureResponseDiv.className = 'mt-3 text-sm text-red-600';
        });
    });
</script>
@endpush
@endsection

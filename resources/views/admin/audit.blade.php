@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Audit Logs</h1>
        <div>
             <button id="verify-integrity-btn" class="inline-flex items-center px-3 py-1.5 border border-yellow-500 text-sm font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 mr-2">
                Verify Integrity
            </button>
            <a href="{{ route('admin.audit.export') }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                Export Logs
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.audit') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">User</label>
                <input type="text" name="user_id" id="user_id" value="{{ request('user_id') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
            </div>
            <div>
                <label for="action" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Action</label>
                <input type="text" name="action" id="action" value="{{ request('action') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date To</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <a href="{{ route('admin.audit') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 mr-2">Clear</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Filter</button>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Timestamp</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Case #</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">IP Address</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Integrity</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($logs as $log)
                    <tr class="log-row" data-log-id="{{ $log->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $log->action }}</td>
                        <td class="px-6 py-4 text-sm max-w-xs truncate" title="{{ $log->description }}">{{ $log->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($log->case_id)
                            <a href="{{ route('case.show', $log->case_id) }}" class="text-indigo-600 hover:underline">{{ $log->case->case_number ?? $log->case_id }}</a>
                            @else
                            N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $log->ip_address }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-400" title="{{ $log->integrity_hash }}">
                            {{ substr($log->integrity_hash, 0, 8) }}...
                        </td>
                    </tr>
                    {{-- Add expandable row for raw data/metadata --}}
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No audit logs found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         @if ($logs->hasPages())
            <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
    {{-- Add Modal for Integrity Check Results --}}
    <div id="integrity-modal" class="hidden fixed z-10 inset-0 overflow-y-auto">... Modal Content ...</div>
</div>

@push('scripts')
<script>
    document.getElementById('verify-integrity-btn').addEventListener('click', function() {
        this.textContent = 'Verifying...';
        this.disabled = true;
        fetch('{{ route("admin.audit.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Display results in a modal or alert
            alert(`Integrity Check: ${data.status}\nMessage: ${data.message}\nDetails: ${JSON.stringify(data.details)}`);
            this.textContent = 'Verify Integrity';
            this.disabled = false;
        })
        .catch(error => {
            alert('Error performing integrity check.');
            console.error('Integrity Check Error:', error);
            this.textContent = 'Verify Integrity';
            this.disabled = false;
        });
    });
</script>
@endpush
@endsection

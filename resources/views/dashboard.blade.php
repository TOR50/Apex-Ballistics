@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Recent Cases -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-medium mb-4">Recent Cases</h2>
            <div class="space-y-4">
                @forelse($recentCases ?? [] as $case)
                    <div class="border-b dark:border-gray-700 pb-2">
                        <p class="font-medium">
                            <a href="{{ route('case.show', $case->id) }}" class="text-indigo-600 hover:underline">
                                {{ $case->case_number }}
                            </a>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $case->firearm_type }}</p>
                        <p class="text-xs text-gray-500">Created {{ $case->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No recent cases found.</p>
                @endforelse
            </div>
        </div>

        <!-- Pending Analyses -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-medium mb-4">Pending Analyses</h2>
            <div class="space-y-4">
                @forelse($pendingAnalyses ?? [] as $analysis)
                    <div class="border-b dark:border-gray-700 pb-2">
                        <p class="font-medium">
                            <a href="{{ route('analysis.show', $analysis->id) }}" class="text-indigo-600 hover:underline">
                                Analysis #{{ $analysis->id }}
                            </a>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Case: {{ $analysis->case->case_number ?? 'N/A' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Priority: {{ ucfirst($analysis->priority) }}
                            @if($analysis->due_date)
                                | Due: {{ $analysis->due_date->format('M d, Y') }}
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No pending analyses found.</p>
                @endforelse
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-medium mb-4">System Health</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium">Storage Usage</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-1">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $systemHealth['storage']['percentage'] ?? 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $systemHealth['storage']['used'] ?? '0' }} / {{ $systemHealth['storage']['total'] ?? '0' }} GB</p>
                </div>

                <div>
                    <p class="text-sm font-medium">Active Users</p>
                    <p class="text-2xl font-bold mt-1">{{ $systemHealth['activeUsers'] ?? 0 }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium">Processing Queue</p>
                    <p class="text-sm mt-1">{{ $systemHealth['processingQueue']['pending'] ?? 0 }} pending | {{ $systemHealth['processingQueue']['processing'] ?? 0 }} processing</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

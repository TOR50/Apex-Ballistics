@extends('layouts.app')

@section('title', 'Analysis Results - Case ' . $case->case_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-4">Analysis Results</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-6">Case: <a href="{{ route('case.show', $case->id) }}" class="text-indigo-600 hover:underline">{{ $case->case_number }}</a> | Analysis ID: {{ $analysis->id }} | Status: {{ ucfirst($analysis->status) }}</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Analysis Display -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-medium mb-4">Match Results</h2>

            {{-- Placeholder for Heatmap/Visualization --}}
            <div class="border dark:border-gray-700 rounded p-4 h-64 flex items-center justify-center text-gray-500 mb-6">
                Heatmap Overlay / Striation Pattern Visualization Placeholder
            </div>

            {{-- Match Probability --}}
            <div class="mb-6 p-4 border rounded dark:border-gray-700">
                <h3 class="text-lg font-medium mb-2">Overall Match Probability</h3>
                {{-- Placeholder for dynamic score --}}
                <div class="text-4xl font-bold text-indigo-600">
                    {{ $analysis->results->avg('match_percentage') ?? 'N/A' }}%
                </div>
                {{-- Placeholder for confidence interval --}}
                <p class="text-sm text-gray-500 dark:text-gray-400">Confidence Interval: [Placeholder]</p>
            </div>

            {{-- Threshold Adjustment --}}
            <div class="mb-6">
                <label for="similarity-threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Similarity Threshold</label>
                <input type="range" id="similarity-threshold" name="threshold" min="0" max="100" value="75" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                <span id="threshold-value" class="text-sm">75%</span>
                {{-- Add JS to handle dynamic filtering based on threshold --}}
            </div>
        </div>

        <!-- Historical Matches & Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-medium mb-4">Potential Matches</h2>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse($matches as $match)
                    <div class="border dark:border-gray-700 rounded p-3 match-item" data-score="{{ $match->match_percentage }}">
                        <p class="font-medium">Case: <a href="{{ route('case.show', $match->matched_case_id) }}" class="text-indigo-600 hover:underline">{{ $match->matchedCase->case_number }}</a></p>
                        <p class="text-sm">Score: {{ number_format($match->match_percentage, 2) }}%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Date: {{ $match->matchedCase->created_at->format('M d, Y') }}</p>
                        <div class="mt-2">
                            {{-- Flagging Action --}}
                            <button class="text-xs text-red-600 hover:underline flag-match-btn" data-match-id="{{ $match->id }}">
                                {{ $match->flagged ? 'Unflag' : 'Flag Match' }}
                            </button>
                            @if($match->flagged)
                                <span class="text-xs text-red-500 ml-2">(Flagged by {{ $match->flagger->name ?? 'N/A' }})</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No potential matches found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Basic threshold filtering example
    const thresholdSlider = document.getElementById('similarity-threshold');
    const thresholdValue = document.getElementById('threshold-value');
    const matchItems = document.querySelectorAll('.match-item');

    thresholdSlider.addEventListener('input', function() {
        const threshold = parseInt(this.value);
        thresholdValue.textContent = `${threshold}%`;

        matchItems.forEach(item => {
            const score = parseFloat(item.dataset.score);
            if (score >= threshold) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
        // Add AJAX call here to update server/log threshold adjustment if needed
        // fetch('{{ route("analysis.adjust", $analysis->id) }}', { method: 'POST', body: JSON.stringify({ threshold: threshold }), headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'} });
    });

    // Basic flagging example (requires more robust implementation with modals/reasons)
    document.querySelectorAll('.flag-match-btn').forEach(button => {
        button.addEventListener('click', function() {
            const matchId = this.dataset.matchId;
            const isFlagged = this.textContent.includes('Unflag');
            const flag = !isFlagged;
            const reason = flag ? prompt("Reason for flagging:") : ''; // Simple prompt, use modal in real app

            if (flag && reason === null) return; // Cancelled prompt change here

            fetch('{{ route("analysis.flag", $analysis->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ match_id: matchId, flag: flag, reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // Update UI dynamically - toggle button text, show/hide flag info
                    alert(data.message); // Replace with better UI update
                    location.reload(); // Simple refresh for now
                } else {
                    alert('Error flagging match.');
                }
            });
        });
    });
</script>
@endpush
@endsection

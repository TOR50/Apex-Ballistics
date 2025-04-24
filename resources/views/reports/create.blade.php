@extends('layouts.app')

@section('title', 'Generate Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-6">Generate New Report</h1>

    <form id="generate-report-form" action="{{ route('reports.generate') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Report Title</label>
                <input type="text" name="title" id="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label for="case_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Case</label>
                <select name="case_id" id="case_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">-- Select a Case --</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}">{{ $case->case_number }} - {{ $case->firearm_type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label for="template_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Report Template</label>
            <select name="template_id" id="template_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                 <option value="">-- Select a Template --</option>
                 @foreach($templates as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                 @endforeach
            </select>
        </div>

        {{-- Placeholder for Custom Report Builder (Drag & Drop Sections) --}}
        <div class="mb-6 border dark:border-gray-700 rounded p-4">
            <h3 class="text-lg font-medium mb-3">Report Sections</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">(Placeholder: Implement drag-and-drop section selection here)</p>
            <div class="space-y-2">
                {{-- Example Checkboxes (replace with actual builder) --}}
                <div><input type="checkbox" name="sections[]" value="case_summary" checked> Case Summary</div>
                <div><input type="checkbox" name="sections[]" value="ballistics_analysis" checked> Ballistics Analysis</div>
                <div><input type="checkbox" name="sections[]" value="match_results" checked> Match Results</div>
                <div><input type="checkbox" name="sections[]" value="images"> Images</div>
                <div><input type="checkbox" name="sections[]" value="conclusions"> Conclusions</div>
                <div><input type="checkbox" name="sections[]" value="audit_trail"> Audit Trail</div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Generate Report
            </button>
        </div>
    </form>
</div>
@endsection

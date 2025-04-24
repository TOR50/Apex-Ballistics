@extends('layouts.app')

@section('title', 'API Documentation')

@push('styles')
{{-- Link to Swagger UI CSS if using it --}}
{{-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.1.0/swagger-ui.min.css" /> --}}
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-6">API Documentation</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        {{-- Placeholder for Swagger UI or other API doc tool --}}
        <div id="swagger-ui">
            <p class="text-gray-500">API documentation (e.g., Swagger UI) will be rendered here.</p>
            <p class="mt-4">You would typically generate an OpenAPI specification (e.g., `openapi.yaml`) and use Swagger UI JS library to display it.</p>
        </div>
    </div>
</div>

@push('scripts')
{{-- Link to Swagger UI JS if using it --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.1.0/swagger-ui-bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.1.0/swagger-ui-standalone-preset.js"></script>
<script>
    window.onload = function() {
        // Build a system
        const ui = SwaggerUIBundle({
            url: "/path/to/your/openapi.yaml", // Replace with your OpenAPI spec URL
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout"
        });
        window.ui = ui;
    };
</script> --}}
@endpush
@endsection

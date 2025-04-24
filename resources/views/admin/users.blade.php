@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-6">User Management</h1>

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

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Login</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="inline-flex items-center update-role-form">
                                @csrf
                                <select name="role_id" class="text-sm rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white role-select" data-original-role="{{ $user->role_id }}">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                {{-- Hidden password input for confirmation --}}
                                <input type="password" name="current_password" class="hidden ml-2 p-1 border rounded text-xs" placeholder="Your Password" required>
                                <button type="submit" class="hidden ml-2 text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Save</button>
                            </form>
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($user->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive/Anonymized</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            {{-- Placeholder for MFA config --}}
                            <button class="text-indigo-600 hover:text-indigo-900 mr-2">MFA</button>
                            @if($user->is_active)
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline-block delete-user-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Anonymize</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
         @if ($users->hasPages())
            <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.role-select').forEach(select => {
        select.addEventListener('change', function() {
            const form = this.closest('form');
            const passwordInput = form.querySelector('input[name="current_password"]');
            const saveButton = form.querySelector('button[type="submit"]');
            if (this.value !== this.dataset.originalRole) {
                passwordInput.classList.remove('hidden');
                saveButton.classList.remove('hidden');
            } else {
                passwordInput.classList.add('hidden');
                saveButton.classList.add('hidden');
            }
        });
    });

    document.querySelectorAll('.delete-user-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to anonymize this user? This action cannot be undone and complies with GDPR.')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
@endsection

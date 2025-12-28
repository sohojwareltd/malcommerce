@extends('layouts.admin')

@section('title', 'Admin Users')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Admin Users</h1>
        <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Manage admin user accounts</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-light transition text-sm sm:text-base text-center">
        + Add Admin User
    </a>
</div>

<!-- Search Form -->
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1 relative">
                <input type="text" 
                       name="search" 
                       id="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search by name or phone..."
                       class="w-full px-4 py-2 pl-10 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-sm sm:text-base">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button type="submit" class="bg-primary text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="bg-neutral-200 text-neutral-700 px-4 sm:px-6 py-2 rounded-lg hover:bg-neutral-300 transition font-semibold text-sm sm:text-base text-center">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($users as $user)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-neutral-900">{{ $user->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                        {{ $user->phone }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-primary hover:text-primary-light">Edit</a>
                            @if($user->id !== Auth::id())
                            <span class="text-neutral-300">|</span>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this admin user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-neutral-500">No admin users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Card View -->
    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($users as $user)
        <div class="p-4 hover:bg-neutral-50 transition-colors">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-neutral-900 truncate mb-1">{{ $user->name }}</h3>
                    <div class="space-y-1 text-xs text-neutral-500">
                        <div>
                            <span class="text-neutral-500">Phone:</span>
                            <span class="text-neutral-900 font-medium ml-1">{{ $user->phone }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-500">Created:</span>
                            <span class="text-neutral-900 font-medium ml-1">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 pt-2 border-t border-neutral-200">
                <a href="{{ route('admin.users.edit', $user) }}" class="text-primary hover:text-primary-light font-medium text-sm">Edit</a>
                @if($user->id !== Auth::id())
                <span class="text-neutral-300">|</span>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this admin user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-sm">Delete</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-neutral-500">
            No admin users found
        </div>
        @endforelse
    </div>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection


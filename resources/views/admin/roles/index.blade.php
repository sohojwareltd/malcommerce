@extends('layouts.admin')

@section('title', 'Roles')

@section('content')
<div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold">Roles</h1>
        <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Manage roles and permissions</p>
    </div>
    @can('roles.create')
    <a href="{{ route('admin.roles.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-light transition font-semibold text-sm sm:text-base text-center">
        + Create Role
    </a>
    @endcan
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Users</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
                @forelse($roles as $role)
                <tr class="hover:bg-neutral-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-neutral-900">{{ $role->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-neutral-600">{{ $role->permissions_count }} permission(s)</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                        {{ $role->users_count }} user(s)
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @can('roles.update')
                        <a href="{{ route('admin.roles.edit', $role) }}" class="text-primary hover:text-primary-light font-medium">Edit</a>
                        @endcan
                        @can('roles.delete')
                        @if($role->name !== 'Super Admin')
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Are you sure you want to delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-neutral-500">No roles found. Run <code class="bg-neutral-100 px-1 rounded">php artisan permissions:sync</code> then <code class="bg-neutral-100 px-1 rounded">php artisan db:seed --class=RoleSeeder</code></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="lg:hidden divide-y divide-neutral-200">
        @forelse($roles as $role)
        <div class="p-4 hover:bg-neutral-50 transition-colors">
            <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm font-semibold text-neutral-900">{{ $role->name }}</h3>
                <span class="text-xs text-neutral-500">{{ $role->permissions_count }} perm · {{ $role->users_count }} users</span>
            </div>
            <div class="flex gap-3 pt-2 border-t border-neutral-200">
                @can('roles.update')
                <a href="{{ route('admin.roles.edit', $role) }}" class="text-primary hover:text-primary-light font-medium text-sm">Edit</a>
                @endcan
                @can('roles.delete')
                @if($role->name !== 'Super Admin')
                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-sm">Delete</button>
                </form>
                @endif
                @endcan
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-neutral-500">No roles found.</div>
        @endforelse
    </div>
</div>
@endsection

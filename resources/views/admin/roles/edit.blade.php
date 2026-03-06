@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="mb-4 sm:mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Edit Role</h1>
            <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Update role name and permissions</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="text-neutral-600 hover:text-neutral-900 px-4 py-2 rounded-lg hover:bg-neutral-100 transition text-sm sm:text-base">
            ← Back
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Role Name <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Permissions</label>
            <div class="space-y-4 max-h-96 overflow-y-auto border border-neutral-200 rounded-lg p-4">
                @php $rolePerms = $role->permissions->pluck('name')->toArray(); @endphp
                @foreach($permissions as $group => $perms)
                <div>
                    <h4 class="text-sm font-semibold text-neutral-700 mb-2 capitalize">{{ $group }}</h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach($perms as $perm)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                {{ in_array($perm->name, old('permissions', $rolePerms)) ? 'checked' : '' }}
                                class="rounded border-neutral-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-neutral-700">{{ $perm->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-neutral-200">
            <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-primary-light transition font-semibold">
                Update Role
            </button>
            <a href="{{ route('admin.roles.index') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2.5 rounded-lg hover:bg-neutral-300 transition font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

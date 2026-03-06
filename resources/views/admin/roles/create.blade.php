@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
<div class="mb-4 sm:mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Create Role</h1>
            <p class="text-neutral-600 mt-1 sm:mt-2 text-sm sm:text-base">Create a new role with permissions</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="text-neutral-600 hover:text-neutral-900 px-4 py-2 rounded-lg hover:bg-neutral-100 transition text-sm sm:text-base">
            ← Back
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
    <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-neutral-700 mb-2">Role Name <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-red-500 @enderror"
                placeholder="e.g. Content Manager">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">Permissions</label>
            <p class="text-sm text-neutral-500 mb-3">Select which permissions this role should have. Permissions come from policies.</p>
            <div class="space-y-4 max-h-96 overflow-y-auto border border-neutral-200 rounded-lg p-4">
                @foreach($permissions as $group => $perms)
                <div>
                    <h4 class="text-sm font-semibold text-neutral-700 mb-2 capitalize">{{ $group }}</h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach($perms as $perm)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                {{ in_array($perm->name, old('permissions', [])) ? 'checked' : '' }}
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
                Create Role
            </button>
            <a href="{{ route('admin.roles.index') }}" class="bg-neutral-200 text-neutral-700 px-6 py-2.5 rounded-lg hover:bg-neutral-300 transition font-semibold">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

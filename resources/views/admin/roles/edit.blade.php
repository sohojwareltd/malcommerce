@extends('layouts.admin')

@section('title', 'Edit Role')

@php use Illuminate\Support\Str; @endphp

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
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-neutral-700">Permissions</label>
                <span id="perm-count" class="text-xs text-neutral-500 font-medium">0 selected</span>
            </div>
            <p class="text-sm text-neutral-500 mb-4">Select the actions this role can perform. Permissions are grouped by resource.</p>
            @php $rolePerms = old('permissions', $role->permissions->pluck('name')->toArray()); @endphp
            <div class="space-y-3 max-h-[28rem] overflow-y-auto pr-1 -mr-1">
                @foreach($permissions as $group => $perms)
                <div class="rounded-xl border border-neutral-200 bg-neutral-50/60 overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-neutral-200/60">
                        <span class="text-sm font-semibold text-neutral-800 capitalize">{{ $group }}</span>
                        <div class="flex items-center gap-3">
                            <button type="button" class="text-xs text-primary hover:text-primary/80 font-medium select-group-all">Select all</button>
                            <button type="button" class="text-xs text-neutral-500 hover:text-neutral-700 font-medium select-group-none">Clear</button>
                        </div>
                    </div>
                    <div class="px-4 py-2" data-perm-group="{{ $group }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-2">
                            @foreach($perms as $perm)
                            @php
                                $checked = in_array($perm->name, $rolePerms);
                                $parts = explode('.', $perm->name, 2);
                                $resource = $parts[0] ?? '';
                                $action = $parts[1] ?? '';
                                $actionLabels = ['viewAny' => 'View list', 'view' => 'View', 'create' => 'Create', 'update' => 'Update', 'delete' => 'Delete', 'restore' => 'Restore', 'bulkDelete' => 'Bulk delete', 'updateStatus' => 'Update status', 'builder' => 'Page builder', 'changePassword' => 'Change password', 'sales' => 'View sales report'];
                                $actionLabel = $actionLabels[$action] ?? ucfirst($action);
                                $resourceLabel = ucfirst(Str::singular($resource));
                                $label = match($perm->name) {
                                    'reports.sales' => 'View sales report',
                                    'profile.changePassword' => 'Change password',
                                    default => $actionLabel . ' ' . $resourceLabel,
                                };
                            @endphp
                            <label class="perm-row flex items-center gap-3 py-2 px-3 rounded-lg hover:bg-neutral-100/80 cursor-pointer transition-colors">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" {{ $checked ? 'checked' : '' }}
                                    class="perm-checkbox w-4 h-4 rounded border-neutral-300 text-primary focus:ring-primary">
                                <span class="text-sm text-neutral-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.perm-checkbox');
            const countEl = document.getElementById('perm-count');
            function updateCount() {
                const n = document.querySelectorAll('.perm-checkbox:checked').length;
                const total = checkboxes.length;
                countEl.textContent = n + ' of ' + total + ' selected';
            }
            checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
            document.querySelectorAll('.select-group-all').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const card = this.closest('.rounded-xl');
                    const group = card?.querySelector('[data-perm-group]');
                    if (group) group.querySelectorAll('.perm-checkbox').forEach(cb => { cb.checked = true; });
                    updateCount();
                });
            });
            document.querySelectorAll('.select-group-none').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const card = this.closest('.rounded-xl');
                    const group = card?.querySelector('[data-perm-group]');
                    if (group) group.querySelectorAll('.perm-checkbox').forEach(cb => { cb.checked = false; });
                    updateCount();
                });
            });
            updateCount();
        });
        </script>
        @endpush

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

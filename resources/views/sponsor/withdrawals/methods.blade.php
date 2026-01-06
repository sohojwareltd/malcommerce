@extends('layouts.sponsor')

@section('title', 'Withdrawal Methods')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold">Withdrawal Methods</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white border border-neutral-200 rounded-lg p-4">
        <h2 class="font-semibold mb-3 text-sm">Existing Methods</h2>
        @if(empty($methods))
            <p class="text-sm text-neutral-500">No withdrawal method added yet.</p>
        @else
            <ul class="space-y-3 text-sm">
                @foreach($methods as $key => $method)
                    @php
                        $number = $method['number'] ?? '';
                        $masked = $number ? substr($number, 0, 3) . 'XXX-XXXXX' : '';
                    @endphp
                    <li class="border border-neutral-200 rounded-lg p-3 flex items-center justify-between">
                        <div>
                            <div class="font-semibold flex items-center gap-2">
                                <span class="uppercase text-xs">{{ $method['provider'] }}</span>
                                <span>{{ $method['label'] ?? 'MFS' }}</span>
                                @if(!empty($method['is_default']) || $user->default_withdrawal_method === $key)
                                    <span class="ml-1 text-xs text-green-600 border border-green-500 rounded px-1">Default</span>
                                @endif
                                <span class="ml-1 text-[10px] px-1 rounded border border-neutral-300">
                                    {{ ucfirst($method['account_type'] ?? 'personal') }}
                                </span>
                            </div>
                            <div class="text-neutral-600 text-xs mt-1">
                                {{ $masked }}
                            </div>
                            <div class="text-neutral-500 text-[11px]">
                                {{ $method['holder_name'] ?? '' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('sponsor.withdrawal-methods', ['edit' => $key]) }}" class="text-xs text-primary">Edit</a>
                            <form action="{{ route('sponsor.withdrawal-methods.delete', $key) }}" method="POST" onsubmit="return confirm('Delete this method?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600">Delete</button>
                            </form>
                            @if($user->default_withdrawal_method !== $key)
                                <form action="{{ route('sponsor.withdrawal-methods.default', $key) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs bg-neutral-200 px-2 py-1 rounded">Set Default</button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="bg-white border border-neutral-200 rounded-lg p-4">
        <h2 class="font-semibold mb-3 text-sm">{{ $editingMethod ? 'Edit MFS Method' : 'Add MFS Method' }}</h2>
        <form action="{{ route('sponsor.withdrawal-methods') }}" method="POST" class="space-y-3 text-sm">
            @csrf
            <input type="hidden" name="existing_key" value="{{ $editingKey }}">

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-1">Provider</label>
                <select name="provider" class="w-full border border-neutral-300 rounded-lg px-3 py-2">
                    @foreach(['bkash' => 'bKash','nagad' => 'Nagad','rocket' => 'Rocket'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('provider', $editingMethod['provider'] ?? 'bkash') === $val)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('provider')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-1">Mobile Number</label>
                <input type="text" name="number" class="w-full border border-neutral-300 rounded-lg px-3 py-2"
                       value="{{ old('number', $editingMethod['number'] ?? '') }}" placeholder="01XXXXXXXXX">
                @error('number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-1">Account Type</label>
                <select name="account_type" class="w-full border border-neutral-300 rounded-lg px-3 py-2">
                    <option value="personal" @selected(old('account_type', $editingMethod['account_type'] ?? 'personal') === 'personal')>Personal</option>
                    <option value="agent" @selected(old('account_type', $editingMethod['account_type'] ?? 'personal') === 'agent')>Agent</option>
                </select>
                @error('account_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-1">Account Holder Name</label>
                <input type="text" name="holder_name" class="w-full border border-neutral-300 rounded-lg px-3 py-2"
                       value="{{ old('holder_name', $editingMethod['holder_name'] ?? '') }}">
                @error('holder_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-1">Reference / Nickname (optional)</label>
                <input type="text" name="label" class="w-full border border-neutral-300 rounded-lg px-3 py-2"
                       value="{{ old('label', $editingMethod['label'] ?? '') }}" placeholder="My Primary bKash">
                @error('label')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="set_default" name="set_default" value="1"
                       @checked(old('set_default') || (!$editingMethod && empty($user->default_withdrawal_method)))>
                <label for="set_default" class="text-sm text-neutral-700">Set as default withdrawal method</label>
            </div>

            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                {{ $editingMethod ? 'Save Changes' : 'Save Method' }}
            </button>
        </form>
    </div>
</div>
@endsection



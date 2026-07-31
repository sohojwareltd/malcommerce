@extends('layouts.admin')

@section('title', 'Code Bans')

@section('content')
<h1 class="mb-6 text-2xl font-bold">Code entry bans</h1>
<div class="overflow-x-auto rounded-xl border bg-white shadow-sm">
    <table class="min-w-full text-sm"><thead class="bg-neutral-50"><tr><th class="px-4 py-3 text-left">User</th><th class="px-4 py-3 text-left">Exam</th><th class="px-4 py-3 text-left">Wrong tries</th><th class="px-4 py-3 text-left">Banned at</th><th class="px-4 py-3 text-left"></th></tr></thead>
    <tbody class="divide-y">@foreach($bans as $ban)<tr><td class="px-4 py-3">{{ $ban->user->name }}<br><span class="text-xs text-neutral-500">{{ $ban->user->phone }}</span></td><td class="px-4 py-3">{{ $ban->exam->title }}</td><td class="px-4 py-3">{{ $ban->wrong_attempts }}</td><td class="px-4 py-3">{{ $ban->banned_at?->format('Y-m-d H:i') }}</td><td class="px-4 py-3"><form method="POST" action="{{ route('admin.exam-code-bans.unban') }}">@csrf<input type="hidden" name="user_id" value="{{ $ban->user_id }}"><input type="hidden" name="exam_id" value="{{ $ban->exam_id }}"><button class="text-primary text-sm">Unban</button></form></td></tr>@endforeach</tbody></table>
</div>
<div class="mt-4">{{ $bans->links() }}</div>
@endsection

<div class="rounded-2xl border shadow-sm hover:shadow-md transition-all bg-white overflow-hidden" style="border-color: rgba(189,232,245,0.9);">
    <div class="p-3 sm:p-4" style="background: linear-gradient(135deg, rgba(189,232,245,0.28) 0%, rgba(255,255,255,1) 65%);">
        <div class="flex gap-3">
            <div class="w-16 h-16 sm:w-20 sm:h-20 shrink-0">
                @if($referral->photo)
                    <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}" class="w-full h-full rounded-xl object-cover border-2 shadow-sm" style="border-color: white;">
                @else
                    <div class="w-full h-full rounded-xl flex items-center justify-center border-2 shadow-sm" style="background: var(--color-light); border-color: white;">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm sm:text-base truncate" style="color: var(--color-dark);">{{ $referral->name }}</h3>
                        <p class="text-[11px] sm:text-xs font-mono truncate" style="color: var(--color-medium);">{{ $referral->affiliate_code }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-[10px] sm:text-xs font-semibold whitespace-nowrap" style="background: rgba(15,40,84,0.08); color: var(--color-dark);">
                        {{ $referral->orders_count }} orders
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-2 mt-3">
                    <div class="rounded-lg px-2.5 py-2" style="background: rgba(16,185,129,0.10);">
                        <p class="text-[10px] uppercase tracking-wide text-green-700">Purchase</p>
                        <p class="text-xs sm:text-sm font-bold text-green-700">৳{{ number_format((float) ($referral->current_month_purchase_amount ?? 0), 2) }}</p>
                    </div>
                </div>

                <p class="text-[11px] sm:text-xs mt-2" style="color: var(--color-medium);">
                    Joined: {{ $referral->created_at->format('M d, Y') }}
                </p>
            </div>
        </div>

        @if($referral->address)
            <div class="mt-3 p-2.5 rounded-lg border text-xs line-clamp-2" style="border-color: rgba(189,232,245,0.9); color: var(--color-medium); background: rgba(255,255,255,0.9);">
                {{ $referral->address }}
            </div>
        @endif

        @if($referral->comment)
            <div class="mt-2 p-2.5 rounded-lg text-xs font-medium" style="background: rgba(59,130,246,0.08); color: var(--color-dark);">
                {{ $referral->comment }}
            </div>
        @endif
    </div>

    <div class="p-3 border-t" style="border-color: rgba(189,232,245,0.9);">
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('sponsor.users.show', $referral) }}"
               class="text-xs font-semibold px-3 py-2 rounded-lg text-center"
               style="background: var(--color-medium); color: white;">
                View
            </a>
            <a href="{{ route('sponsor.users.edit', $referral) }}"
               class="text-xs font-semibold px-3 py-2 rounded-lg text-center border"
               style="border-color: var(--color-accent); color: var(--color-medium);">
                Edit
            </a>
            <button type="button"
                    @click="$dispatch('open-team-purchase', { id: {{ $referral->id }}, name: {{ json_encode($referral->name) }} })"
                    class="text-xs font-semibold px-3 py-2 rounded-lg border"
                    style="border-color: var(--color-accent); color: var(--color-medium);">
                Add purchase
            </button>
            <a href="{{ route('sponsor.gallery.index', ['user_id' => $referral->id]) }}#upload"
               class="text-xs font-semibold px-3 py-2 rounded-lg text-center border"
               style="border-color: var(--color-accent); color: var(--color-medium);">
                Upload Photo
            </a>
        </div>

        @if($referral->phone)
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $referral->phone) }}"
               class="mt-2 inline-flex items-center justify-center gap-1 w-full text-xs font-semibold px-3 py-2 rounded-lg border"
               style="border-color: var(--color-accent); color: var(--color-medium);">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1H9c-1.1 0-2-.9-2-2v-3.5c0-.55.45-1 1-1h1.5c0-1.25.2-2.45.57-3.57.11-.35.03-.74-.25-1.02l-2.2-2.2z"/></svg>
                Call
            </a>
        @endif
    </div>
</div>
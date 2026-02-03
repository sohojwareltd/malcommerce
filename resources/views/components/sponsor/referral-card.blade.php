<div class="p-3 rounded-2xl border-2 hover:shadow-md transition bg-white" style="border-color: var(--color-accent);">
    {{-- Row 1: Grid - 1/3 image, 2/3 data --}}
    <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-5 gap-3">
        {{-- Column 1: Image (~1/3 width) --}}
        <div class="col-span-2 sm:col-span-2 md:col-span-2 aspect-square">
            @if($referral->photo)
                <img src="{{ Storage::disk('public')->url($referral->photo) }}" alt="{{ $referral->name }}"
                     class="w-full h-full rounded-xl object-cover">
            @else
                <div class="w-full h-full rounded-xl flex items-center justify-center" style="background: var(--color-light);">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Column 2: Data (~2/3 width) --}}
        <div class="col-span-3 sm:col-span-4 md:col-span-3 min-w-0 flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between gap-2 mb-1">
                    <div class="min-w-0">
                        <h3 class="font-semibold text-sm truncate" style="color: var(--color-dark);">{{ $referral->name }}</h3>
                        <p class="text-xs font-mono" style="color: var(--color-medium);">{{ $referral->affiliate_code }}</p>
                    </div>
                    <p class="text-xs font-medium whitespace-nowrap" style="color: var(--color-dark);">
                        {{ $referral->orders_count }} orders
                    </p>
                </div>

                @if($referral->address)
                    <p class="text-xs mt-1 line-clamp-1" style="color: var(--color-medium);">
                        {{ $referral->address }}
                    </p>
                @endif
            </div>

            <div class="flex items-center justify-between mt-2">
                <p class="text-xs" style="color: var(--color-medium);">
                    Joined: {{ $referral->created_at->format('M d, Y') }}
                </p>
            </div>
            <div class="flex items-center justify-between mt-2">
                <p class="text-xs font-mono font-medium font-semibold" style="color: green;">
                  {{$referral->comment ?: 'N/A'}}
                </p>
            </div>
            {{-- Row 2: Buttons --}}
    <div class="flex items-center justify-end gap-2 mt-4 pt-3 border-t border-gray-100 flex-wrap">
        @if($referral->phone)
        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $referral->phone) }}"
           class="text-xs font-semibold px-3 py-1 rounded-full border border-dashed inline-flex items-center gap-1"
           style="color: var(--color-medium); border-color: var(--color-medium);">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1H9c-1.1 0-2-.9-2-2v-3.5c0-.55.45-1 1-1h1.5c0-1.25.2-2.45.57-3.57.11-.35.03-.74-.25-1.02l-2.2-2.2z"/></svg>
            Call
        </a>
        @endif
        <a href="{{ route('sponsor.users.edit', $referral) }}"
           class="text-xs font-semibold px-3 py-1 rounded-full border border-dashed"
           style="color: var(--color-medium); border-color: var(--color-medium);">
            Edit
        </a>
        <a href="{{ route('sponsor.users.show', $referral) }}"
           class="text-xs font-semibold px-3 py-1 rounded-full border border-dashed"
           style="color: var(--color-medium); border-color: var(--color-medium);">
            View
        </a>
    </div>
        </div>
    </div>

  
</div>
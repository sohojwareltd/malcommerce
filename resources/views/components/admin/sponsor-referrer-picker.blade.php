@props([
    'referrers',
    'name' => 'sponsor_id',
    'selected' => null,
    'label' => 'Referrer (upline sponsor)',
    'showLabel' => true,
    'hint' => null,
])

@php
    $raw = old($name, $selected);
    $selectedInt = ($raw !== null && $raw !== '') ? (int) $raw : null;
    $options = $referrers->map(fn ($o) => [
        'id' => $o->id,
        'name' => $o->name,
        'code' => (string) $o->affiliate_code,
        'phone' => (string) ($o->phone ?? ''),
        'deleted' => $o->trashed(),
    ])->values();
@endphp

@once
@push('scripts')
<script>
window.adminReferrerPicker = function (options, initialId) {
    return {
        options: options || [],
        selectedId: initialId,
        query: '',
        open: false,
        label(o) {
            return o.name + ' (' + o.code + ')' + (o.deleted ? ' — deleted' : '');
        },
        get filtered() {
            const q = (this.query || '').trim().toLowerCase();
            let list = this.options;
            if (q.length) {
                const digits = q.replace(/\D/g, '');
                list = list.filter((o) => {
                    if (o.name.toLowerCase().includes(q) || o.code.includes(q)) return true;
                    if (o.phone && digits.length) {
                        return o.phone.replace(/\D/g, '').includes(digits);
                    }
                    return o.phone && o.phone.toLowerCase().includes(q);
                });
            }
            return list.slice(0, 80);
        },
        get selectedLabel() {
            if (this.selectedId == null) return '';
            const o = this.options.find((x) => x.id === this.selectedId);
            return o ? this.label(o) : '';
        },
        select(o) {
            this.selectedId = o.id;
            this.query = this.label(o);
            this.open = false;
        },
        clear() {
            this.selectedId = null;
            this.query = '';
            this.open = false;
        },
        onInput() {
            this.open = true;
            const o = this.selectedId != null ? this.options.find((x) => x.id === this.selectedId) : null;
            if (o && this.query !== this.label(o)) {
                this.selectedId = null;
            }
        },
        init() {
            if (this.selectedId != null) {
                const o = this.options.find((x) => x.id === this.selectedId);
                if (o) this.query = this.label(o);
            }
        },
    };
};
</script>
@endpush
@endonce

<div
    class="relative"
    x-data="window.adminReferrerPicker(@js($options), @js($selectedInt))"
    @click.outside="open = false"
>
    @if($showLabel)
        <label for="referrer-search-{{ $name }}" class="block text-sm font-medium text-neutral-700 mb-2">{{ $label }}</label>
    @endif
    <input type="hidden" name="{{ $name }}" :value="selectedId ?? ''" />
    <div class="flex gap-2">
        <div class="relative flex-1 min-w-0">
            <input
                type="text"
                id="referrer-search-{{ $name }}"
                x-model="query"
                @focus="open = true"
                @input="onInput()"
                autocomplete="off"
                placeholder="Search by name, partner code, or phone…"
                class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            />
            <div
                x-show="open && filtered.length"
                x-transition
                x-cloak
                class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto rounded-lg border border-neutral-200 bg-white shadow-lg"
            >
                <template x-for="o in filtered" :key="o.id">
                    <button
                        type="button"
                        class="w-full px-3 py-2 text-left text-sm hover:bg-neutral-50 border-b border-neutral-100 last:border-0"
                        @click="select(o)"
                        x-text="label(o)"
                    ></button>
                </template>
            </div>
            <p x-show="open && query.trim() && !filtered.length" x-cloak class="absolute z-50 mt-1 w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm text-neutral-500 shadow-lg">
                No matches
            </p>
        </div>
        <button type="button" @click="clear()" class="shrink-0 px-3 py-2 text-sm font-medium text-neutral-600 border border-neutral-300 rounded-lg hover:bg-neutral-50">
            Clear
        </button>
    </div>
    @if($hint)
        <p class="mt-1 text-xs text-neutral-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

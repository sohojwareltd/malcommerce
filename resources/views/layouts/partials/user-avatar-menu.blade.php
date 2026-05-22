@php
    $user = auth()->user();
    $myCoursesCount = $user
        ? \App\Models\DigitalCourseEnrollment::where('user_id', $user->id)->count()
        : 0;
    $userSubtitle = $user->isSponsor() && $user->affiliate_code
        ? $user->affiliate_code
        : ($user->phone ?? '');
@endphp
<div class="relative" x-data="{ userMenuOpen: false }">
    <button
        type="button"
        @click="userMenuOpen = !userMenuOpen"
        class="flex items-center focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-full transition"
        aria-label="Account menu"
        aria-expanded="false"
        :aria-expanded="userMenuOpen"
    >
        @if($user->photo)
            <img src="{{ Storage::disk('public')->url($user->photo) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 hover:border-primary transition-colors">
        @else
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center border-2 border-gray-200 hover:border-primary transition-colors">
                <span class="text-white font-semibold text-sm">{{ substr($user->name, 0, 1) }}</span>
            </div>
        @endif
    </button>
    <div
        x-show="userMenuOpen"
        @click.away="userMenuOpen = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-60 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
        x-cloak
        style="display: none;"
    >
        <div class="px-4 py-3 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
            @if($userSubtitle)
                <p class="text-xs text-gray-500 truncate">{{ $userSubtitle }}</p>
            @endif
        </div>

        <a href="{{ route('my-courses.index') }}" @click="userMenuOpen = false"
           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-50 font-bangla border-b border-gray-100">
            <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span class="flex-1 font-semibold">আমার কেনা কোর্স</span>
            @if($myCoursesCount > 0)
                <span class="text-xs font-bold bg-primary/10 text-primary px-2 py-0.5 rounded-full">{{ $myCoursesCount }}</span>
            @endif
        </a>

        @if($user->isAdmin())
            <a href="{{ route('admin.dashboard') }}" @click="userMenuOpen = false" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Admin dashboard</a>
        @elseif($user->isSponsor())
            <a href="{{ route('sponsor.dashboard') }}" @click="userMenuOpen = false" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 font-bangla">পার্টনার ড্যাশবোর্ড</a>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="font-bangla">লগআউট</span>
            </button>
        </form>
    </div>
</div>

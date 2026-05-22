{{-- $course, $enrolled, $activeLessonId (optional), $lessonRoute callable or use defaults --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <p class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-gray-100 font-bangla">লেসন তালিকা</p>
    <ul class="max-h-[60vh] overflow-y-auto divide-y divide-gray-100">
        @foreach($course->activeLessons as $i => $item)
        <li>
            @if($item->isWatchable($enrolled))
                @php
                    $href = $enrolled
                        ? route('my-courses.show', ['course' => $course->slug, 'lesson' => $i])
                        : route('courses.lessons.watch', ['course' => $course->slug, 'lesson' => $item->id]);
                    $isActive = isset($activeLessonId) && $activeLessonId === $item->id;
                @endphp
                <a href="{{ $href }}"
                   class="flex items-center gap-3 px-4 py-3 text-sm {{ $isActive ? 'bg-primary/5 text-primary font-medium' : 'text-gray-800 hover:bg-gray-50' }}">
                    @include('courses.partials.lesson-icons', ['unlocked' => true])
                    <span class="min-w-0 flex-1"><span class="text-gray-400 mr-1">{{ $i + 1 }}.</span>{{ $item->title }}</span>
                </a>
            @else
                <button type="button" @click="buyOpen = true"
                        class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm text-gray-600 hover:bg-gray-50">
                    @include('courses.partials.lesson-icons', ['unlocked' => false])
                    <span class="min-w-0 flex-1"><span class="text-gray-400 mr-1">{{ $i + 1 }}.</span>{{ $item->title }}</span>
                </button>
            @endif
        </li>
        @endforeach
    </ul>
</div>

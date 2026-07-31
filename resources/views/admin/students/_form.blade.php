@php
    $inputClass = 'w-full rounded-lg border border-neutral-300 px-4 py-2.5 text-sm';
    $labelClass = 'mb-1.5 block text-sm font-medium text-neutral-700';
    $errorClass = 'border-red-500 focus:border-red-500 focus:ring-red-500/20';
    $selectedInstitute = (string) old('institute_id', $student?->institute_id ?? '');
    $selectedCourse = (string) old('student_course_id', $student?->student_course_id ?? '');
@endphp

<form method="POST" action="{{ $student ? route('admin.students.update', $student) : route('admin.students.store') }}" enctype="multipart/form-data"
    x-data="{
        instituteId: @js($selectedInstitute),
        courseId: @js($selectedCourse),
        addedInstitutes: [],
        addedCourses: [],
        showInstituteModal: false,
        showCourseModal: false,
        newInstituteName: '',
        newCourseTitle: '',
        instituteError: '',
        courseError: '',
        savingInstitute: false,
        savingCourse: false,
        csrfToken: document.querySelector('meta[name=csrf-token]').content,
        syncBeforeSubmit() {
            this.$refs.instituteIdInput.value = this.instituteId || '';
            this.$refs.courseIdInput.value = this.courseId || '';
        },
        async createInstitute() {
            this.instituteError = '';
            this.savingInstitute = true;
            try {
                const res = await fetch('{{ route('admin.institutes.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({ name: this.newInstituteName }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.instituteError = data.message || Object.values(data.errors || {}).flat()[0] || 'Could not create institute.';
                    return;
                }
                const item = { id: String(data.id), name: data.name };
                if (!this.addedInstitutes.some(i => i.id === item.id)) {
                    this.addedInstitutes.push(item);
                }
                this.instituteId = item.id;
                this.newInstituteName = '';
                this.showInstituteModal = false;
            } catch (e) {
                this.instituteError = 'Something went wrong. Please try again.';
            } finally {
                this.savingInstitute = false;
            }
        },
        async createCourse() {
            this.courseError = '';
            this.savingCourse = true;
            try {
                const res = await fetch('{{ route('admin.student-courses.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({ title: this.newCourseTitle }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.courseError = data.message || Object.values(data.errors || {}).flat()[0] || 'Could not create course.';
                    return;
                }
                const item = { id: String(data.id), title: data.title };
                if (!this.addedCourses.some(c => c.id === item.id)) {
                    this.addedCourses.push(item);
                }
                this.courseId = item.id;
                this.newCourseTitle = '';
                this.showCourseModal = false;
            } catch (e) {
                this.courseError = 'Something went wrong. Please try again.';
            } finally {
                this.savingCourse = false;
            }
        },
    }"
    @submit="syncBeforeSubmit()"
    class="rounded-xl border bg-white p-6 shadow-sm space-y-6 min-w-0 max-w-full">
    @csrf
    @if($student) @method('PUT') @endif

    @if($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <p class="font-semibold">Please fix the following:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <input type="hidden" name="institute_id" x-ref="instituteIdInput" :value="instituteId">
    <input type="hidden" name="student_course_id" x-ref="courseIdInput" :value="courseId">

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 min-w-0">
        <div>
            <label class="{{ $labelClass }}">Name *</label>
            <input type="text" name="name" value="{{ old('name', $student?->name) }}" required class="{{ $inputClass }} @error('name') {{ $errorClass }} @enderror">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div><label class="{{ $labelClass }}">Registration number</label><input type="text" name="registration_number" value="{{ old('registration_number', $student?->registration_number) }}" class="{{ $inputClass }}"></div>
        <div><label class="{{ $labelClass }}">Roll number</label><input type="text" name="roll_number" value="{{ old('roll_number', $student?->roll_number) }}" class="{{ $inputClass }}"></div>
        <div><label class="{{ $labelClass }}">Gender</label>
            <select name="gender" class="{{ $inputClass }} @error('gender') {{ $errorClass }} @enderror">
                <option value="">—</option>
                @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('gender', $student?->gender) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('gender')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div><label class="{{ $labelClass }}">Father's name</label><input type="text" name="father_name" value="{{ old('father_name', $student?->father_name) }}" class="{{ $inputClass }}"></div>
        <div><label class="{{ $labelClass }}">Mother's name</label><input type="text" name="mother_name" value="{{ old('mother_name', $student?->mother_name) }}" class="{{ $inputClass }}"></div>
        <div><label class="{{ $labelClass }}">Student thana</label><input type="text" name="student_thana" value="{{ old('student_thana', $student?->student_thana) }}" class="{{ $inputClass }}"></div>
        <div><label class="{{ $labelClass }}">Student district</label><input type="text" name="student_district" value="{{ old('student_district', $student?->student_district) }}" class="{{ $inputClass }}"></div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 min-w-0">
        <div>
            <div class="mb-1.5 flex items-center justify-between gap-2">
                <label class="{{ $labelClass }} mb-0">Institute *</label>
                @can('institutes.create')
                <button type="button" @click="showInstituteModal = true; instituteError = ''" class="text-xs font-semibold text-primary hover:underline">+ Add institute</button>
                @endcan
            </div>
            <select x-model="instituteId" class="{{ $inputClass }} @error('institute_id') {{ $errorClass }} @enderror">
                <option value="">Select institute</option>
                @foreach($institutes as $institute)
                <option value="{{ $institute->id }}" @selected($selectedInstitute === (string) $institute->id)>{{ $institute->name }}</option>
                @endforeach
                <template x-for="institute in addedInstitutes" :key="'inst-'+institute.id">
                    <option :value="institute.id" x-text="institute.name"></option>
                </template>
            </select>
            @error('institute_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <div class="mb-1.5 flex items-center justify-between gap-2">
                <label class="{{ $labelClass }} mb-0">Course *</label>
                @can('studentCourses.create')
                <button type="button" @click="showCourseModal = true; courseError = ''" class="text-xs font-semibold text-primary hover:underline">+ Add course</button>
                @endcan
            </div>
            <select x-model="courseId" class="{{ $inputClass }} @error('student_course_id') {{ $errorClass }} @enderror">
                <option value="">Select course</option>
                @foreach($courses as $course)
                <option value="{{ $course->id }}" @selected($selectedCourse === (string) $course->id)>{{ $course->title }}</option>
                @endforeach
                <template x-for="course in addedCourses" :key="'course-'+course.id">
                    <option :value="course.id" x-text="course.title"></option>
                </template>
            </select>
            @error('student_course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div><label class="{{ $labelClass }}">Course duration</label><input type="text" name="course_duration" value="{{ old('course_duration', $student?->course_duration) }}" placeholder="3 Months" class="{{ $inputClass }}"></div>
        <div><label class="{{ $labelClass }}">Session</label><input type="text" name="session" value="{{ old('session', $student?->session) }}" placeholder="January To March 2019" class="{{ $inputClass }}"></div>
        <div><label class="{{ $labelClass }}">Examinee type</label>
            <select name="examinee_type" class="{{ $inputClass }}">
                @foreach(['regular' => 'Regular', 'irregular' => 'Irregular'] as $value => $label)
                <option value="{{ $value }}" @selected(old('examinee_type', $student?->examinee_type ?? 'regular') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="border-t pt-6">
        <h2 class="mb-4 text-lg font-bold">Result & marksheet</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 min-w-0">
            <div>
                <label class="{{ $labelClass }}">CGPA</label>
                <input type="number" step="0.01" min="0" max="4" name="cgpa" value="{{ old('cgpa', $student?->cgpa) }}" class="{{ $inputClass }} @error('cgpa') {{ $errorClass }} @enderror">
                @error('cgpa')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div><label class="{{ $labelClass }}">Letter grade</label><input type="text" name="letter_grade" value="{{ old('letter_grade', $student?->letter_grade) }}" placeholder="A+" class="{{ $inputClass }}"></div>
            <div><label class="{{ $labelClass }}">Exam month</label><input type="text" name="exam_month" value="{{ old('exam_month', $student?->exam_month) }}" placeholder="March 2019" class="{{ $inputClass }}"></div>
            <div><label class="{{ $labelClass }}">Issue date</label><input type="date" name="issue_date" value="{{ old('issue_date', $student?->issue_date?->format('Y-m-d')) }}" class="{{ $inputClass }} @error('issue_date') {{ $errorClass }} @enderror"></div>
            <div><label class="{{ $labelClass }}">Written marks</label><input type="number" name="marks_written" value="{{ old('marks_written', $student?->marks_written) }}" class="{{ $inputClass }} @error('marks_written') {{ $errorClass }} @enderror"></div>
            <div><label class="{{ $labelClass }}">Internship marks</label><input type="number" name="marks_internship" value="{{ old('marks_internship', $student?->marks_internship) }}" class="{{ $inputClass }} @error('marks_internship') {{ $errorClass }} @enderror"></div>
            <div><label class="{{ $labelClass }}">Viva marks</label><input type="number" name="marks_viva" value="{{ old('marks_viva', $student?->marks_viva) }}" class="{{ $inputClass }} @error('marks_viva') {{ $errorClass }} @enderror"></div>
            <div><label class="{{ $labelClass }}">Full marks</label><input type="number" name="marks_full" value="{{ old('marks_full', $student?->marks_full) }}" class="{{ $inputClass }} @error('marks_full') {{ $errorClass }} @enderror"></div>
        </div>
    </div>

    <div>
        <label class="{{ $labelClass }}">Photo</label>
        <input type="file" name="photo" accept="image/*" class="{{ $inputClass }} @error('photo') {{ $errorClass }} @enderror">
        @error('photo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @if($student?->photo_url)<img src="{{ $student->photo_url }}" alt="" class="mt-2 h-24 rounded object-cover">@endif
    </div>

    <div class="flex gap-3">
        <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white">Save student</button>
        <a href="{{ route('admin.students.index') }}" class="rounded-lg border px-5 py-2.5 text-sm">Back</a>
    </div>

    @can('institutes.create')
    <div x-show="showInstituteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4" @keydown.escape.window="showInstituteModal = false">
        <div class="absolute inset-0 bg-black/50" @click="showInstituteModal = false"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.stop>
            <h3 class="text-lg font-bold text-neutral-900">Add institute</h3>
            <p class="mt-1 text-sm text-neutral-500">Enter the institute name only.</p>
            <div class="mt-4">
                <label class="{{ $labelClass }}">Name *</label>
                <input type="text" x-model="newInstituteName" class="{{ $inputClass }}" placeholder="Bangladesh Medical Education Institute" @keydown.enter.prevent="createInstitute()">
            </div>
            <p x-show="instituteError" x-text="instituteError" class="mt-2 text-sm text-red-600"></p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="showInstituteModal = false" class="rounded-lg border px-4 py-2 text-sm">Cancel</button>
                <button type="button" @click="createInstitute()" :disabled="savingInstitute || !newInstituteName.trim()" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
                    <span x-show="!savingInstitute">Save</span>
                    <span x-show="savingInstitute">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endcan

    @can('studentCourses.create')
    <div x-show="showCourseModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4" @keydown.escape.window="showCourseModal = false">
        <div class="absolute inset-0 bg-black/50" @click="showCourseModal = false"></div>
        <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.stop>
            <h3 class="text-lg font-bold text-neutral-900">Add course</h3>
            <p class="mt-1 text-sm text-neutral-500">Enter the course title only.</p>
            <div class="mt-4">
                <label class="{{ $labelClass }}">Title *</label>
                <input type="text" x-model="newCourseTitle" class="{{ $inputClass }}" placeholder="Diploma in Ayurvedic medicine & Surgery (DAMS)" @keydown.enter.prevent="createCourse()">
            </div>
            <p x-show="courseError" x-text="courseError" class="mt-2 text-sm text-red-600"></p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="showCourseModal = false" class="rounded-lg border px-4 py-2 text-sm">Cancel</button>
                <button type="button" @click="createCourse()" :disabled="savingCourse || !newCourseTitle.trim()" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
                    <span x-show="!savingCourse">Save</span>
                    <span x-show="savingCourse">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endcan
</form>

@once
@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush
@endonce

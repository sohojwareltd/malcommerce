@php
    $questionRows = old('questions');
    if ($questionRows === null && isset($exam)) {
        $questionRows = $exam->questions->map(fn ($q) => [
            'id' => $q->id,
            'question_text' => $q->question_text,
            'points' => $q->points,
            'existing_image_url' => $q->image_url,
            'image_preview' => $q->image_url,
            'remove_image' => false,
            'options' => $q->options->map(fn ($o) => [
                'id' => $o->id,
                'option_text' => $o->option_text,
                'is_correct' => $o->is_correct,
                'existing_image_url' => $o->image_url,
                'image_preview' => $o->image_url,
                'remove_image' => false,
            ])->values()->all(),
        ])->values()->all();
    }
    $questionRows = $questionRows ?: [[
        'question_text' => '',
        'points' => 1,
        'existing_image_url' => '',
        'image_preview' => '',
        'remove_image' => false,
        'options' => [
            ['option_text' => '', 'is_correct' => true, 'existing_image_url' => '', 'image_preview' => '', 'remove_image' => false],
            ['option_text' => '', 'is_correct' => false, 'existing_image_url' => '', 'image_preview' => '', 'remove_image' => false],
        ],
    ]];
    $inputClass = 'w-full rounded-lg border border-neutral-300 bg-white px-4 py-2.5 text-sm';
    $labelClass = 'mb-1.5 block text-sm font-medium text-neutral-700';
@endphp

<div class="rounded-xl border border-neutral-200 bg-white shadow-sm"
     x-data="{
        questions: {{ json_encode(array_values($questionRows)) }},
        addQuestion() {
            this.questions.push({
                question_text: '',
                points: 1,
                existing_image_url: '',
                image_preview: '',
                remove_image: false,
                options: [
                    { option_text: '', is_correct: true, existing_image_url: '', image_preview: '', remove_image: false },
                    { option_text: '', is_correct: false, existing_image_url: '', image_preview: '', remove_image: false },
                ],
            });
        },
        removeQuestion(i) { this.questions.splice(i, 1); },
        addOption(qi) {
            this.questions[qi].options.push({ option_text: '', is_correct: false, existing_image_url: '', image_preview: '', remove_image: false });
        },
        removeOption(qi, oi) { this.questions[qi].options.splice(oi, 1); },
        setCorrect(qi, oi) {
            this.questions[qi].options.forEach((o, idx) => { o.is_correct = idx === oi; });
        },
        onQuestionImageChange(qi, event) {
            const file = event.target.files?.[0];
            if (!file) return;
            this.questions[qi].remove_image = false;
            const reader = new FileReader();
            reader.onload = (e) => { this.questions[qi].image_preview = e.target.result; };
            reader.readAsDataURL(file);
        },
        clearQuestionImage(qi) {
            this.questions[qi].image_preview = '';
            this.questions[qi].remove_image = true;
            const input = document.getElementById('question-image-' + qi);
            if (input) input.value = '';
        },
        onOptionImageChange(qi, oi, event) {
            const file = event.target.files?.[0];
            if (!file) return;
            this.questions[qi].options[oi].remove_image = false;
            const reader = new FileReader();
            reader.onload = (e) => { this.questions[qi].options[oi].image_preview = e.target.result; };
            reader.readAsDataURL(file);
        },
        clearOptionImage(qi, oi) {
            this.questions[qi].options[oi].image_preview = '';
            this.questions[qi].options[oi].remove_image = true;
            const input = document.getElementById('option-image-' + qi + '-' + oi);
            if (input) input.value = '';
        },
     }">
    <form method="POST"
          action="{{ $exam ? route('admin.exams.update', $exam) : route('admin.exams.store') }}"
          enctype="multipart/form-data"
          class="divide-y divide-neutral-200">
        @csrf
        @if($exam) @method('PUT') @endif

        <div class="border-b border-neutral-200 px-6 pt-6">
            <div class="inline-flex rounded-lg border border-neutral-200 overflow-hidden">
                <button type="button" data-tab-target="settings" class="tab-button px-4 py-2 text-sm font-semibold text-primary bg-primary/10">Settings</button>
                <button type="button" data-tab-target="questions" class="tab-button px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-100">Questions</button>
            </div>
        </div>

        <div id="tab-settings" class="tab-panel p-6">
            <div class="mx-auto max-w-3xl space-y-4">
                <h2 class="text-base font-bold">Exam settings</h2>
                <div>
                    <label class="{{ $labelClass }}">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $exam?->title) }}" required class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $exam?->slug) }}" class="{{ $inputClass }} font-mono text-xs">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Description</label>
                    <textarea name="description" rows="4" class="{{ $inputClass }}">{{ old('description', $exam?->description) }}</textarea>
                </div>
                <div>
                    <label class="{{ $labelClass }}">Access type *</label>
                    <select name="access_type" class="{{ $inputClass }}">
                        @foreach(\App\Models\Exam::accessTypeLabels() as $value => $label)
                        <option value="{{ $value }}" @selected(old('access_type', $exam?->access_type ?? 'free') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Price (BDT)</label>
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $exam?->price ?? 0) }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Pass mark (%)</label>
                        <input type="number" min="1" max="100" name="pass_mark" value="{{ old('pass_mark', $exam?->pass_mark ?? 60) }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Max exam attempts</label>
                        <input type="number" min="1" max="100" name="max_exam_attempts" value="{{ old('max_exam_attempts', $exam?->max_exam_attempts ?? 3) }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Max wrong code tries</label>
                        <input type="number" min="1" max="100" name="max_code_attempts" value="{{ old('max_code_attempts', $exam?->max_code_attempts ?? 6) }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Duration (minutes)</label>
                        <input type="number" min="1" max="1440" name="duration_minutes" value="{{ old('duration_minutes', $exam?->duration_minutes) }}" class="{{ $inputClass }}" placeholder="Optional">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Sort order</label>
                        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $exam?->sort_order ?? 0) }}" class="{{ $inputClass }}">
                    </div>
                </div>
                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="certificate_enabled" value="1" @checked(old('certificate_enabled', $exam?->certificate_enabled ?? true))> Issue certificate on pass</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $exam?->is_active ?? true))> Active</label>
                </div>
            </div>
        </div>

        <div id="tab-questions" class="tab-panel hidden p-6">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold">Questions</h2>
                    <p class="mt-1 text-xs text-neutral-500">Add text and/or an image for each question and option.</p>
                </div>
                <button type="button" @click="addQuestion()" class="rounded-lg border px-3 py-1.5 text-sm">Add question</button>
            </div>

            <div class="space-y-4">
                <template x-for="(question, qi) in questions" :key="qi">
                    <div class="rounded-lg border border-neutral-200 p-4 space-y-4">
                        <input type="hidden" :name="'questions['+qi+'][id]'" x-model="question.id">
                        <input type="hidden" :name="'questions['+qi+'][remove_image]'" :value="question.remove_image ? 1 : 0">

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 space-y-3">
                                <div>
                                    <label class="{{ $labelClass }}">Question <span x-text="qi + 1"></span></label>
                                    <textarea :name="'questions['+qi+'][question_text]'" x-model="question.question_text" rows="2" class="{{ $inputClass }}" placeholder="Question text (optional if image is set)"></textarea>
                                </div>
                                <div>
                                    <label class="{{ $labelClass }}">Question image</label>
                                    <input type="file" :id="'question-image-' + qi" :name="'questions['+qi+'][image]'" accept="image/*" @change="onQuestionImageChange(qi, $event)" class="{{ $inputClass }}">
                                    <template x-if="question.image_preview">
                                        <div class="mt-2 flex items-start gap-3">
                                            <img :src="question.image_preview" alt="" class="h-24 w-auto rounded-lg border object-cover">
                                            <button type="button" @click="clearQuestionImage(qi)" class="text-sm text-red-600">Remove image</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <button type="button" @click="removeQuestion(qi)" class="text-red-600 text-sm mt-6">Remove</button>
                        </div>

                        <div>
                            <label class="{{ $labelClass }}">Points</label>
                            <input type="number" min="1" :name="'questions['+qi+'][points]'" x-model="question.points" class="{{ $inputClass }} w-24">
                        </div>

                        <div class="space-y-3">
                            <p class="text-sm font-medium text-neutral-700">Options</p>
                            <template x-for="(option, oi) in question.options" :key="oi">
                                <div class="rounded-lg border border-neutral-100 bg-neutral-50 p-3 space-y-2">
                                    <input type="hidden" :name="'questions['+qi+'][options]['+oi+'][id]'" x-model="option.id">
                                    <input type="hidden" :name="'questions['+qi+'][options]['+oi+'][remove_image]'" :value="option.remove_image ? 1 : 0">
                                    <input type="hidden" :name="'questions['+qi+'][options]['+oi+'][is_correct]'" :value="option.is_correct ? 1 : 0">

                                    <div class="flex items-start gap-2">
                                        <input type="radio" :name="'correct_'+qi" @change="setCorrect(qi, oi)" :checked="option.is_correct" class="mt-2">
                                        <div class="flex-1 space-y-2">
                                            <input type="text" :name="'questions['+qi+'][options]['+oi+'][option_text]'" x-model="option.option_text" class="{{ $inputClass }}" placeholder="Option text (optional if image is set)">
                                            <div>
                                                <input type="file" :id="'option-image-' + qi + '-' + oi" :name="'questions['+qi+'][options]['+oi+'][image]'" accept="image/*" @change="onOptionImageChange(qi, oi, $event)" class="{{ $inputClass }}">
                                                <template x-if="option.image_preview">
                                                    <div class="mt-2 flex items-start gap-3">
                                                        <img :src="option.image_preview" alt="" class="h-16 w-auto rounded border object-cover">
                                                        <button type="button" @click="clearOptionImage(qi, oi)" class="text-xs text-red-600">Remove image</button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeOption(qi, oi)" class="text-red-500 text-xs mt-2">×</button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" @click="addOption(qi)" class="text-sm text-primary">+ Add option</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-between p-6">
            <a href="{{ route('admin.exams.index') }}" class="text-sm text-neutral-600">← Back</a>
            <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white">Save exam</button>
        </div>
    </form>
</div>

@if($exam)
<div class="mt-8 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
    <h2 class="mb-4 text-base font-bold">Access codes</h2>
    <form method="POST" action="{{ route('admin.exams.generate-codes', $exam) }}" class="mb-6 flex flex-wrap items-end gap-3">
        @csrf
        <div>
            <label class="{{ $labelClass }}">Quantity</label>
            <input type="number" name="quantity" min="1" max="100" value="5" class="{{ $inputClass }} w-28">
        </div>
        <div>
            <label class="{{ $labelClass }}">Max uses per code</label>
            <input type="number" name="max_uses" min="1" max="1000" value="1" class="{{ $inputClass }} w-28">
        </div>
        <div>
            <label class="{{ $labelClass }}">Expires at</label>
            <input type="datetime-local" name="expires_at" class="{{ $inputClass }}">
        </div>
        <button class="rounded-lg bg-neutral-900 px-4 py-2.5 text-sm font-semibold text-white">Generate</button>
    </form>

    @if($exam->accessCodes->count())
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-neutral-50 text-left"><tr><th class="px-3 py-2">Code</th><th class="px-3 py-2">Uses</th><th class="px-3 py-2">Expires</th><th class="px-3 py-2">Active</th></tr></thead>
            <tbody class="divide-y">
                @foreach($exam->accessCodes->sortByDesc('created_at') as $code)
                <tr>
                    <td class="px-3 py-2 font-mono font-bold">{{ $code->code }}</td>
                    <td class="px-3 py-2">{{ $code->used_count }} / {{ $code->max_uses }}</td>
                    <td class="px-3 py-2">{{ $code->expires_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $code->is_active ? 'Yes' : 'No' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabButtons = document.querySelectorAll('.tab-button');
    var tabPanels = document.querySelectorAll('.tab-panel');

    function activateTab(target) {
        tabPanels.forEach(function (panel) {
            panel.classList.toggle('hidden', panel.id !== 'tab-' + target);
        });
        tabButtons.forEach(function (button) {
            var isActive = button.dataset.tabTarget === target;
            button.classList.toggle('bg-primary/10', isActive);
            button.classList.toggle('text-primary', isActive);
            button.classList.toggle('text-neutral-700', !isActive);
        });
    }

    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            activateTab(button.dataset.tabTarget);
        });
    });

    activateTab('settings');
});
</script>
@endpush

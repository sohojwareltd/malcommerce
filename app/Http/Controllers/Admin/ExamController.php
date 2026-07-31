<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAccessCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::query();

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $exams = $query->withCount(['questions', 'enrollments', 'attempts'])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        return view('admin.exams.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateExam($request);

        $slug = $validated['slug'] ?? Str::slug($validated['title']);
        $slug = $this->uniqueSlug($slug);

        $exam = Exam::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'access_type' => $validated['access_type'],
            'price' => $validated['price'] ?? 0,
            'pass_mark' => $validated['pass_mark'],
            'max_exam_attempts' => $validated['max_exam_attempts'],
            'max_code_attempts' => $validated['max_code_attempts'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'certificate_enabled' => $request->boolean('certificate_enabled', true),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $this->syncQuestions($exam, $request);

        return redirect()->route('admin.exams.edit', $exam)->with('success', 'Exam created.');
    }

    public function edit(Exam $exam)
    {
        $exam->load(['questions.options', 'accessCodes']);

        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $this->validateExam($request, $exam->id);

        $slug = $validated['slug'] ?? Str::slug($validated['title']);
        if ($slug !== $exam->slug) {
            $slug = $this->uniqueSlug($slug, $exam->id);
        }

        $exam->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'access_type' => $validated['access_type'],
            'price' => $validated['price'] ?? 0,
            'pass_mark' => $validated['pass_mark'],
            'max_exam_attempts' => $validated['max_exam_attempts'],
            'max_code_attempts' => $validated['max_code_attempts'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'certificate_enabled' => $request->boolean('certificate_enabled', true),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $this->syncQuestions($exam, $request);

        return redirect()->route('admin.exams.edit', $exam)->with('success', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted.');
    }

    public function restore(int $exam)
    {
        $exam = Exam::onlyTrashed()->findOrFail($exam);
        $exam->restore();

        return redirect()->route('admin.exams.index', ['trashed' => 1])->with('success', 'Exam restored.');
    }

    public function generateCodes(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'max_uses' => 'required|integer|min:1|max:1000',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $codes = [];
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $codes[] = ExamAccessCode::create([
                'exam_id' => $exam->id,
                'code' => ExamAccessCode::generateUniqueCode($exam->id),
                'max_uses' => $validated['max_uses'],
                'expires_at' => $validated['expires_at'] ?? null,
            ]);
        }

        return back()->with('success', count($codes) . ' access code(s) generated.');
    }

    protected function validateExam(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('exams', 'slug')->ignore($ignoreId),
            ],
            'description' => 'nullable|string',
            'access_type' => ['required', Rule::in(array_keys(Exam::accessTypeLabels()))],
            'price' => 'nullable|numeric|min:0',
            'pass_mark' => 'required|integer|min:1|max:100',
            'max_exam_attempts' => 'required|integer|min:1|max:100',
            'max_code_attempts' => 'required|integer|min:1|max:100',
            'duration_minutes' => 'nullable|integer|min:1|max:1440',
            'sort_order' => 'nullable|integer|min:0',
            'questions' => 'nullable|array',
            'questions.*.question_text' => 'nullable|string',
            'questions.*.points' => 'nullable|integer|min:1|max:100',
            'questions.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'questions.*.remove_image' => 'nullable|boolean',
            'questions.*.options' => 'nullable|array|min:2',
            'questions.*.options.*.option_text' => 'nullable|string',
            'questions.*.options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'questions.*.options.*.remove_image' => 'nullable|boolean',
            'questions.*.options.*.is_correct' => 'nullable|boolean',
        ]);
    }

    protected function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug;
        $counter = 1;

        while (Exam::withTrashed()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function syncQuestions(Exam $exam, Request $request): void
    {
        $questions = $request->input('questions', []);
        $keptQuestionIds = [];

        foreach (array_values($questions) as $index => $row) {
            $existingQuestion = isset($row['id'])
                ? $exam->questions()->find($row['id'])
                : null;

            $questionImage = $this->resolveUploadedImage(
                $request,
                "questions.{$index}.image",
                $existingQuestion?->image,
                !empty($row['remove_image'])
            );

            if (!$this->rowHasContent($row['question_text'] ?? null, $questionImage)) {
                continue;
            }

            if (!$existingQuestion) {
                $question = $exam->questions()->create([
                    'question_text' => $row['question_text'] ?? '',
                    'image' => $questionImage,
                    'sort_order' => $index,
                    'points' => $row['points'] ?? 1,
                ]);
            } else {
                $existingQuestion->update([
                    'question_text' => $row['question_text'] ?? '',
                    'image' => $questionImage,
                    'sort_order' => $index,
                    'points' => $row['points'] ?? 1,
                ]);
                $question = $existingQuestion;
            }

            $keptQuestionIds[] = $question->id;
            $keptOptionIds = [];

            foreach (array_values($row['options'] ?? []) as $optIndex => $optionRow) {
                $existingOption = isset($optionRow['id'])
                    ? $question->options()->find($optionRow['id'])
                    : null;

                $optionImage = $this->resolveUploadedImage(
                    $request,
                    "questions.{$index}.options.{$optIndex}.image",
                    $existingOption?->image,
                    !empty($optionRow['remove_image'])
                );

                if (!$this->rowHasContent($optionRow['option_text'] ?? null, $optionImage)) {
                    continue;
                }

                if (!$existingOption) {
                    $option = $question->options()->create([
                        'option_text' => $optionRow['option_text'] ?? '',
                        'image' => $optionImage,
                        'is_correct' => !empty($optionRow['is_correct']),
                        'sort_order' => $optIndex,
                    ]);
                } else {
                    $existingOption->update([
                        'option_text' => $optionRow['option_text'] ?? '',
                        'image' => $optionImage,
                        'is_correct' => !empty($optionRow['is_correct']),
                        'sort_order' => $optIndex,
                    ]);
                    $option = $existingOption;
                }

                $keptOptionIds[] = $option->id;
            }

            $question->options()->whereNotIn('id', $keptOptionIds)->each(function ($option) {
                $this->deleteStoredImage($option->image);
                $option->delete();
            });
        }

        $exam->questions()->whereNotIn('id', $keptQuestionIds)->each(function ($question) {
            $question->options()->each(function ($option) {
                $this->deleteStoredImage($option->image);
                $option->delete();
            });
            $this->deleteStoredImage($question->image);
            $question->delete();
        });
    }

    protected function resolveUploadedImage(Request $request, string $key, ?string $existing, bool $remove): ?string
    {
        if ($request->hasFile($key)) {
            $this->deleteStoredImage($existing);

            return $request->file($key)->store('exams', 'public');
        }

        if ($remove) {
            $this->deleteStoredImage($existing);

            return null;
        }

        return $existing;
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    protected function rowHasContent(?string $text, ?string $image): bool
    {
        return trim((string) $text) !== '' || $image !== null;
    }
}

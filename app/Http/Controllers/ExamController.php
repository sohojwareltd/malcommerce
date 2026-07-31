<?php

namespace App\Http\Controllers;

use App\Models\Exam;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::active()
            ->withCount('questions')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        if (!$exam->is_active) {
            abort(404);
        }

        $exam->loadCount('questions');

        $accessService = app(\App\Services\ExamAccessService::class);
        $user = auth()->user();

        $enrollment = $user ? $accessService->enrollment($user, $exam) : null;
        $certificate = $user ? $accessService->certificate($user, $exam) : null;
        $remainingAttempts = $user ? $accessService->remainingExamAttempts($user, $exam) : null;
        $remainingCodeAttempts = $user ? $accessService->remainingCodeAttempts($user, $exam) : null;
        $codeBanned = $user ? $accessService->isCodeBanned($user, $exam) : false;
        $passed = $user ? $accessService->hasPassed($user, $exam) : false;

        return view('exams.show', compact(
            'exam',
            'enrollment',
            'certificate',
            'remainingAttempts',
            'remainingCodeAttempts',
            'codeBanned',
            'passed'
        ));
    }
}

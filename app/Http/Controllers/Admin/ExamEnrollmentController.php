<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamCertificate;
use App\Models\ExamCodeAttempt;
use App\Models\ExamEnrollment;
use App\Models\User;
use App\Services\ExamAccessService;
use Illuminate\Http\Request;

class ExamEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamEnrollment::with(['user', 'exam']);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        $enrollments = $query->orderByDesc('granted_at')->paginate(30)->withQueryString();
        $exams = Exam::orderBy('title')->get();

        return view('admin.exam-enrollments.index', compact('enrollments', 'exams'));
    }

    public function attempts(Request $request)
    {
        $query = ExamAttempt::with(['user', 'exam']);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        $attempts = $query->orderByDesc('started_at')->paginate(30)->withQueryString();
        $exams = Exam::orderBy('title')->get();

        return view('admin.exam-attempts.index', compact('attempts', 'exams'));
    }

    public function certificates(Request $request)
    {
        $query = ExamCertificate::with(['user', 'exam']);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        $certificates = $query->orderByDesc('issued_at')->paginate(30)->withQueryString();
        $exams = Exam::orderBy('title')->get();

        return view('admin.exam-certificates.index', compact('certificates', 'exams'));
    }

    public function unbanCode(Request $request, ExamAccessService $accessService)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'exam_id' => 'required|exists:exams,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $exam = Exam::findOrFail($validated['exam_id']);

        $accessService->unbanCode($user, $exam);

        return back()->with('success', 'Code ban removed for this user.');
    }

    public function grantAccess(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'exam_id' => 'required|exists:exams,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $exam = Exam::findOrFail($validated['exam_id']);

        ExamEnrollment::grant($user, $exam, ExamEnrollment::ACCESS_ADMIN);

        return back()->with('success', 'Access granted.');
    }

    public function codeBans(Request $request)
    {
        $query = ExamCodeAttempt::with(['user', 'exam'])->whereNotNull('banned_at');

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        $bans = $query->orderByDesc('banned_at')->paginate(30)->withQueryString();
        $exams = Exam::orderBy('title')->get();

        return view('admin.exam-code-bans.index', compact('bans', 'exams'));
    }
}

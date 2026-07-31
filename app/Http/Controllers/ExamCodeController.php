<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Services\ExamAccessService;
use Illuminate\Http\Request;

class ExamCodeController extends Controller
{
    public function redeem(Request $request, Exam $exam, ExamAccessService $accessService)
    {
        if (!$exam->is_active || !$exam->requiresCode()) {
            abort(404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:10',
        ]);

        $result = $accessService->redeemCode($request->user(), $exam, $validated['code']);

        if (!$result['success']) {
            return back()->with('error', $result['message'])
                ->with('remaining_code_attempts', $result['remaining'] ?? null);
        }

        return redirect()->route('exams.show', $exam)
            ->with('success', $result['message']);
    }
}

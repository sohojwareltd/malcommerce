<?php

namespace App\Http\Controllers;

use App\Models\ExamCertificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function show(ExamCertificate $certificate)
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        $certificate->load('exam');

        return view('certificates.show', compact('certificate'));
    }

    public function verify(Request $request, CertificateService $certificateService)
    {
        $code = $request->query('code', '');

        if ($code === '') {
            return view('certificates.verify', ['certificate' => null, 'code' => '']);
        }

        $certificate = $certificateService->findByVerificationCode($code);

        return view('certificates.verify', compact('certificate', 'code'));
    }
}

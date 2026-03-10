<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Order;
use App\Models\SmsLog;
use App\Models\WorkshopEnrollment;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsSendController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('settings.smsSend');
        $products = \App\Models\Product::orderBy('name')->get(['id', 'name']);
        $jobCirculars = \App\Models\JobCircular::orderBy('title')->get(['id', 'title']);
        $workshopSeminars = \App\Models\WorkshopSeminar::orderBy('title')->get(['id', 'title']);

        return view('admin.sms-send.index', compact('products', 'jobCirculars', 'workshopSeminars'));
    }

    public function preview(Request $request)
    {
        $this->authorize('settings.smsSend');
        $recipients = $this->buildRecipients($request);
        if ($recipients->isEmpty()) {
            return redirect()->route('admin.settings.sms-send')
                ->withInput()
                ->with('error', 'No recipients found for the selected filters.');
        }
        $message = $request->validate(['message' => 'required|string|max:1000'])['message'];

        return view('admin.sms-send.preview', [
            'recipients' => $recipients,
            'message' => $message,
            'source' => $request->input('source'),
            'filters' => $this->getFiltersForLog($request),
            'requestParams' => $request->only([
                'source', 'manual_numbers',
                'order_status', 'order_product_id', 'order_date_from', 'order_date_to',
                'enrollment_workshop_id', 'enrollment_status', 'enrollment_date_from', 'enrollment_date_to',
                'application_job_id', 'application_status', 'application_date_from', 'application_date_to',
            ]),
        ]);
    }

    public function send(Request $request, SmsService $smsService)
    {
        $this->authorize('settings.smsSend');
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'source' => 'required|in:manual,orders,enrollments,applications',
        ]);
        $recipients = $this->buildRecipients($request);
        if ($recipients->isEmpty()) {
            return redirect()->route('admin.settings.sms-send')
                ->with('error', 'No recipients to send to.');
        }

        $messages = $recipients->map(function ($r) use ($validated) {
            $text = $validated['message'];
            $text = str_replace('{name}', $r['name'] ?: 'Customer', $text);
            return ['to' => $r['phone'], 'message' => $text];
        })->values()->all();

        $result = $smsService->sendMany($messages);

        $filters = $this->getFiltersForLog($request);
        SmsLog::create([
            'user_id' => Auth::id(),
            'source' => $validated['source'],
            'filters' => $filters,
            'recipient_count' => count($recipients),
            'message_preview' => \Illuminate\Support\Str::limit($validated['message'], 200),
        ]);

        if (!$result['success']) {
            return redirect()->route('admin.settings.sms-send')
                ->withInput()
                ->with('error', $result['message'] ?? 'Failed to send SMS.');
        }

        return redirect()->route('admin.settings.sms-send')
            ->with('success', 'SMS sent to ' . count($recipients) . ' recipient(s).');
    }

    protected function buildRecipients(Request $request): \Illuminate\Support\Collection
    {
        $source = $request->input('source', 'manual');
        if ($source === 'manual') {
            $raw = $request->input('manual_numbers', '');
            $lines = preg_split('/[\r\n,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
            $recipients = collect($lines)->map(function ($line) {
                $line = trim($line);
                return $line ? ['phone' => $line, 'name' => ''] : null;
            })->filter()->values();
            return $this->normalizeRecipientPhones($recipients);
        }
        if ($source === 'orders') {
            $query = Order::query()->select('customer_phone', 'customer_name');
            if ($request->filled('order_status')) {
                $query->where('status', $request->order_status);
            }
            if ($request->filled('order_product_id')) {
                $query->where('product_id', $request->order_product_id);
            }
            if ($request->filled('order_date_from')) {
                $query->whereDate('created_at', '>=', $request->order_date_from);
            }
            if ($request->filled('order_date_to')) {
                $query->whereDate('created_at', '<=', $request->order_date_to);
            }
            $rows = $query->get()->unique('customer_phone')->map(fn ($o) => [
                'phone' => $o->customer_phone,
                'name' => $o->customer_name ?? '',
            ])->filter(fn ($r) => !empty($r['phone']))->values();
            return $this->normalizeRecipientPhones($rows);
        }
        if ($source === 'enrollments') {
            $query = WorkshopEnrollment::query()->select('phone', 'name');
            if ($request->filled('enrollment_workshop_id')) {
                $query->where('workshop_seminar_id', $request->enrollment_workshop_id);
            }
            if ($request->filled('enrollment_status')) {
                $query->where('status', $request->enrollment_status);
            }
            if ($request->filled('enrollment_date_from')) {
                $query->whereDate('created_at', '>=', $request->enrollment_date_from);
            }
            if ($request->filled('enrollment_date_to')) {
                $query->whereDate('created_at', '<=', $request->enrollment_date_to);
            }
            $rows = $query->get()->map(fn ($e) => ['phone' => $e->phone, 'name' => $e->name ?? ''])->filter(fn ($r) => !empty($r['phone']))->values();
            return $this->normalizeRecipientPhones($rows);
        }
        if ($source === 'applications') {
            $query = JobApplication::query()->select('phone', 'name');
            if ($request->filled('application_job_id')) {
                $query->where('job_circular_id', $request->application_job_id);
            }
            if ($request->filled('application_status')) {
                $query->where('status', $request->application_status);
            }
            if ($request->filled('application_date_from')) {
                $query->whereDate('created_at', '>=', $request->application_date_from);
            }
            if ($request->filled('application_date_to')) {
                $query->whereDate('created_at', '<=', $request->application_date_to);
            }
            $rows = $query->get()->map(fn ($a) => ['phone' => $a->phone, 'name' => $a->name ?? ''])->filter(fn ($r) => !empty($r['phone']))->values();
            return $this->normalizeRecipientPhones($rows);
        }
        return collect();
    }

    protected function normalizeRecipientPhones(\Illuminate\Support\Collection $recipients): \Illuminate\Support\Collection
    {
        $sms = app(SmsService::class);
        return $recipients->map(function ($r) use ($sms) {
            $normalized = $sms->normalizePhone($r['phone'] ?? '');
            return $normalized ? ['phone' => $normalized, 'name' => $r['name'] ?? ''] : null;
        })->filter()->unique('phone')->values();
    }

    protected function getFiltersForLog(Request $request): array
    {
        $source = $request->input('source', 'manual');
        $filters = ['source' => $source];
        if ($source === 'manual') {
            return $filters;
        }
        if ($source === 'orders') {
            $filters['status'] = $request->input('order_status');
            $filters['product_id'] = $request->input('order_product_id');
            $filters['date_from'] = $request->input('order_date_from');
            $filters['date_to'] = $request->input('order_date_to');
        }
        if ($source === 'enrollments') {
            $filters['workshop_seminar_id'] = $request->input('enrollment_workshop_id');
            $filters['status'] = $request->input('enrollment_status');
            $filters['date_from'] = $request->input('enrollment_date_from');
            $filters['date_to'] = $request->input('enrollment_date_to');
        }
        if ($source === 'applications') {
            $filters['job_circular_id'] = $request->input('application_job_id');
            $filters['status'] = $request->input('application_status');
            $filters['date_from'] = $request->input('application_date_from');
            $filters['date_to'] = $request->input('application_date_to');
        }
        return $filters;
    }
}

<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\WithdrawalMethodLog;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $withdrawals = $user->withdrawals()->latest()->paginate(20);

        return view('sponsor.withdrawals.index', [
            'user' => $user,
            'withdrawals' => $withdrawals,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        $methods = $this->normalizeMethods($user->withdrawal_methods ?? []);
        $defaultKey = $user->default_withdrawal_method;
        $defaultMethod = $defaultKey && isset($methods[$defaultKey])
            ? $methods[$defaultKey]
            : null;

        return view('sponsor.withdrawals.create', [
            'user' => $user,
            'methods' => $methods,
            'defaultMethod' => $defaultMethod,
        ]);
    }

    public function store(Request $request, WithdrawalService $withdrawalService)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'method_key' => ['required', 'string'],
        ]);

        $methods = $this->normalizeMethods($user->withdrawal_methods ?? []);
        $methodKey = $request->method_key;
        $method = $methods[$methodKey] ?? null;

        if (!$method) {
            throw ValidationException::withMessages([
                'method_key' => 'Selected withdrawal method is invalid.',
            ]);
        }

        // Ensure there's a default and at least one method
        if (empty($methods) || !$user->default_withdrawal_method || !isset($methods[$user->default_withdrawal_method])) {
            throw ValidationException::withMessages([
                'amount' => 'You must configure a default MFS withdrawal method before requesting a withdrawal.',
            ]);
        }

        try {
            $withdrawal = $withdrawalService->requestWithdrawal(
                $user,
                (float) $request->amount,
                array_merge($method, ['method_key' => $methodKey])
            );
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()
            ->route('sponsor.withdrawals.index')
            ->with('success', 'Withdrawal request submitted successfully.');
    }

    /**
     * Manage withdrawal methods (list + add / edit).
     */
    public function methods(Request $request)
    {
        $user = Auth::user();

        $methods = $this->normalizeMethods($user->withdrawal_methods ?? []);
        $editKey = $request->get('edit');
        $editingMethod = $editKey && isset($methods[$editKey]) ? $methods[$editKey] : null;

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'provider' => 'required|string|in:bkash,nagad,rocket',
                'number' => 'required|string|max:20',
                'account_type' => 'required|string|in:personal,agent',
                'holder_name' => 'required|string|max:255',
                'label' => 'nullable|string|max:255',
                'set_default' => 'nullable|boolean',
                'existing_key' => 'nullable|string',
            ]);

            // Normalize number and enforce uniqueness per provider
            $number = preg_replace('/\D+/', '', $data['number']);
            foreach ($methods as $key => $method) {
                if (!empty($data['existing_key']) && $key === $data['existing_key']) {
                    continue;
                }
                if (($method['provider'] ?? null) === $data['provider'] && ($method['number'] ?? null) === $number) {
                    throw ValidationException::withMessages([
                        'number' => 'This mobile number is already used for ' . ucfirst($data['provider']) . '.',
                    ]);
                }
            }

            $key = $data['existing_key'] ?: ($data['provider'] . '_' . Str::random(8));

            $methods[$key] = [
                'provider' => $data['provider'],
                'number' => $number,
                'account_type' => $data['account_type'],
                'holder_name' => $data['holder_name'],
                'label' => $data['label'] ?: ucfirst($data['provider']) . ' ' . substr($number, -4),
                'is_default' => false, // set below
                'verified' => $methods[$key]['verified'] ?? false,
            ];

            // First method auto default, or explicit checkbox
            if (empty($user->default_withdrawal_method) || !empty($data['set_default'])) {
                $user->default_withdrawal_method = $key;
            }

            $user->withdrawal_methods = $methods;
            $user->save();

            WithdrawalMethodLog::create([
                'user_id' => $user->id,
                'method_key' => $key,
                'action' => $data['existing_key'] ? 'updated' : 'created',
                'payload' => $methods[$key],
            ]);

            return redirect()
                ->route('sponsor.withdrawal-methods')
                ->with('success', 'Withdrawal method saved.');
        }

        return view('sponsor.withdrawals.methods', [
            'user' => $user,
            'methods' => $methods,
            'editingKey' => $editKey,
            'editingMethod' => $editingMethod,
        ]);
    }

    public function setDefaultMethod(string $methodKey)
    {
        $user = Auth::user();

        $methods = $this->normalizeMethods($user->withdrawal_methods ?? []);
        if (!isset($methods[$methodKey])) {
            return redirect()
                ->route('sponsor.withdrawal-methods')
                ->with('error', 'Method not found.');
        }

        $user->default_withdrawal_method = $methodKey;
        $user->withdrawal_methods = $methods;
        $user->save();

        return redirect()
            ->route('sponsor.withdrawal-methods')
            ->with('success', 'Default withdrawal method updated.');
    }

    public function deleteMethod(string $methodKey)
    {
        $user = Auth::user();
        $methods = $this->normalizeMethods($user->withdrawal_methods ?? []);

        if (!isset($methods[$methodKey])) {
            return redirect()->route('sponsor.withdrawal-methods')->with('error', 'Method not found.');
        }

        // Cannot delete last remaining method
        if (count($methods) <= 1) {
            return redirect()->route('sponsor.withdrawal-methods')->with('error', 'You cannot delete the last withdrawal method.');
        }

        // Cannot delete default unless another exists and will become default
        if ($user->default_withdrawal_method === $methodKey && count($methods) <= 1) {
            return redirect()->route('sponsor.withdrawal-methods')->with('error', 'You cannot delete the only default withdrawal method.');
        }

        // Prevent deletion if method is used by active withdrawal
        $hasActive = $user->withdrawals()
            ->whereIn('status', Withdrawal::activeStatuses())
            ->where('receiving_account_information->method_key', $methodKey)
            ->exists();

        if ($hasActive) {
            return redirect()->route('sponsor.withdrawal-methods')->with('error', 'This method is used by an active withdrawal and cannot be deleted.');
        }

        $snapshot = $methods[$methodKey];
        unset($methods[$methodKey]);

        // If deleted was default, pick another as default
        if ($user->default_withdrawal_method === $methodKey) {
            $user->default_withdrawal_method = array_key_first($methods);
        }

        $user->withdrawal_methods = $methods;
        $user->save();

        WithdrawalMethodLog::create([
            'user_id' => $user->id,
            'method_key' => $methodKey,
            'action' => 'deleted',
            'payload' => $snapshot,
        ]);

        return redirect()->route('sponsor.withdrawal-methods')->with('success', 'Withdrawal method deleted.');
    }

    /**
     * Normalize withdrawal_methods JSON into associative [key => method] form.
     */
    protected function normalizeMethods($raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        // If already associative (keys are strings and contain provider_), assume correct
        $isAssoc = array_keys($raw) !== range(0, count($raw) - 1);
        if ($isAssoc) {
            return $raw;
        }

        // Convert legacy numeric array [{id,type,fields...}] into keyed by id or generated key
        $methods = [];
        foreach ($raw as $item) {
            $key = $item['id'] ?? (($item['type'] ?? 'mfs') . '_' . Str::random(6));
            $methods[$key] = [
                'provider' => $item['type'] ?? ($item['provider'] ?? 'bkash'),
                'number' => $item['fields']['account_number'] ?? ($item['number'] ?? ''),
                'account_type' => $item['fields']['account_type'] ?? ($item['account_type'] ?? 'personal'),
                'holder_name' => $item['fields']['account_name'] ?? ($item['holder_name'] ?? ''),
                'label' => $item['label'] ?? ucfirst($item['type'] ?? 'MFS'),
                'is_default' => $item['is_default'] ?? false,
                'verified' => $item['verified'] ?? false,
            ];
        }

        return $methods;
    }
}



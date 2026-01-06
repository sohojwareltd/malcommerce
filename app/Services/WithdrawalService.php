<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawalService
{
    /**
     * Request a new withdrawal for the given sponsor.
     *
     * @throws ValidationException
     */
    public function requestWithdrawal(User $sponsor, float $amount, array $receivingAccountInformation): Withdrawal
    {
        return DB::transaction(function () use ($sponsor, $amount, $receivingAccountInformation) {
            // Ensure only one active withdrawal
            $hasActive = $sponsor->withdrawals()
                ->whereIn('status', Withdrawal::activeStatuses())
                ->exists();

            if ($hasActive) {
                throw ValidationException::withMessages([
                    'amount' => 'You already have an active withdrawal request.',
                ]);
            }

            // Determine minimum withdrawal limit (user-specific or global)
            $minLimit = $sponsor->minimum_withdrawal_limit;
            if ($minLimit === null) {
                $minLimit = (float) Setting::get('minimum_withdrawal_limit', 0);
            }

            if ($minLimit > 0 && $amount < $minLimit) {
                throw ValidationException::withMessages([
                    'amount' => "Minimum withdrawal amount is ৳" . number_format($minLimit, 2),
                ]);
            }

            if ($amount > (float) $sponsor->balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Requested amount exceeds your available balance.',
                ]);
            }

            // Strategy: deduct balance immediately when requesting
            $sponsor->decrement('balance', $amount);

            return Withdrawal::create([
                'sponsor_id' => $sponsor->id,
                'amount' => $amount,
                'receiving_account_information' => $receivingAccountInformation,
                'status' => Withdrawal::STATUS_PENDING,
                'requested_at' => now(),
            ]);
        });
    }

    /**
     * Approve withdrawal.
     */
    public function approve(Withdrawal $withdrawal, ?string $adminNote = null): Withdrawal
    {
        return DB::transaction(function () use ($withdrawal, $adminNote) {
            if ($withdrawal->status === Withdrawal::STATUS_APPROVED) {
                return $withdrawal;
            }

            $withdrawal->status = Withdrawal::STATUS_APPROVED;
            $withdrawal->admin_note = $adminNote;
            $withdrawal->processed_at = now();
            $withdrawal->save();

            // Balance was already deducted on request.
            return $withdrawal;
        });
    }

    /**
     * Cancel withdrawal. Returns amount to sponsor balance if previously deducted.
     */
    public function cancel(Withdrawal $withdrawal, ?string $adminNote = null): Withdrawal
    {
        return DB::transaction(function () use ($withdrawal, $adminNote) {
            if (!in_array($withdrawal->status, Withdrawal::activeStatuses(), true)) {
                return $withdrawal;
            }

            $withdrawal->status = Withdrawal::STATUS_CANCELLED;
            $withdrawal->admin_note = $adminNote;
            $withdrawal->processed_at = now();
            $withdrawal->save();

            // Return funds to sponsor balance
            $withdrawal->sponsor->increment('balance', $withdrawal->amount);

            return $withdrawal;
        });
    }

    /**
     * Mark withdrawal as inquiry with note.
     */
    public function markInquiry(Withdrawal $withdrawal, string $note): Withdrawal
    {
        $withdrawal->status = Withdrawal::STATUS_INQUIRY;
        $withdrawal->inquiry_note = $note;
        $withdrawal->save();

        return $withdrawal;
    }
}



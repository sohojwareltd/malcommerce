<?php

namespace App\Http\Controllers\Concerns;

trait NormalizesBangladeshPhone
{
    protected function normalizePhone(string $phone): string
    {
        if (empty($phone)) {
            throw new \Exception('Phone number is required');
        }

        $phone = preg_replace('/[^0-9+]/', '', $phone);
        $phone = str_replace('+', '', $phone);

        if (str_starts_with($phone, '880')) {
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            throw new \Exception('Invalid Bangladesh phone number format.');
        }

        if (str_starts_with($phone, '0')) {
            $phone = '880' . substr($phone, 1);
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            throw new \Exception('Invalid Bangladesh phone number format.');
        }

        if (strlen($phone) === 11 && $phone[0] === '1') {
            return '880' . $phone;
        }

        if (strlen($phone) === 10 && $phone[0] === '1') {
            return '880' . $phone;
        }

        throw new \Exception('Invalid phone number. Please enter a valid Bangladesh mobile number.');
    }
}

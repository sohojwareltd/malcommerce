<?php

namespace App\Services;

use App\Models\DigitalCourseOrder;
use Illuminate\Support\Facades\Log;

class DigitalCourseSmsService
{
    public const STATUS_COMPLETED = 'completed';

    public const STATUS_PENDING = 'pending';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_FAILED = 'failed';

    /** @return array<string, string> */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_COMPLETED => 'Purchase completed',
            self::STATUS_PENDING => 'Order placed (pending payment)',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_FAILED => 'Payment failed',
        ];
    }

    public function buildMessage(DigitalCourseOrder $order, string $status): ?string
    {
        $order->loadMissing('course');
        $course = $order->course;

        if (!$course) {
            return null;
        }

        $templates = $course->sms_templates ?? [];
        $template = $templates[$status] ?? null;

        if ($template === null || trim($template) === '') {
            $template = $this->defaultTemplate($status);
        }

        if ($template === null || trim($template) === '') {
            return null;
        }

        $courseTitle = $course->title;

        $replacements = [
            '{order_number}' => $order->order_number,
            '{customer_name}' => $order->customer_name,
            '{customer_phone}' => $order->customer_phone,
            '{course_title}' => $courseTitle,
            '{course_name}' => $courseTitle,
            '{product_name}' => $courseTitle,
            '{status}' => ucfirst($status),
            '{total_price}' => number_format((float) $order->total_price, 0),
            '{my_courses_url}' => url(route('my-courses.index')),
            '{login_url}' => url(route('login')),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    public function send(DigitalCourseOrder $order, string $status): void
    {
        $message = $this->buildMessage($order, $status);

        if (!$message) {
            return;
        }

        try {
            $result = app(SmsService::class)->send($order->customer_phone, $message);
            if (!($result['success'] ?? false)) {
                Log::warning('Course order SMS failed', [
                    'order_id' => $order->id,
                    'status' => $status,
                    'error' => $result['error'] ?? 'Unknown',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Course order SMS exception', [
                'order_id' => $order->id,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function defaultTemplate(string $status): ?string
    {
        return match ($status) {
            self::STATUS_COMPLETED => 'আপনি "{course_title}" কোর্সটি কিনেছেন। অর্ডার #{order_number}। প্রথমে আপনার মোবাইল নম্বর ({customer_phone}) দিয়ে লগইন করুন: {login_url} তারপর আমার কেনা কোর্স থেকে ভিডিও দেখুন: {my_courses_url}',
            self::STATUS_PENDING => 'আপনার কোর্স অর্ডার #{order_number} গ্রহণ করা হয়েছে। মোট ৳{total_price}। পেমেন্ট সম্পন্ন করুন।',
            self::STATUS_CANCELLED => 'আপনার কোর্স অর্ডার #{order_number} বাতিল করা হয়েছে।',
            self::STATUS_FAILED => 'আপনার কোর্স অর্ডার #{order_number} এর পেমেন্ট সম্পন্ন হয়নি।',
            default => null,
        };
    }
}

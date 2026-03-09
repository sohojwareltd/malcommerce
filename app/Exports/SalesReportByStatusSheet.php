<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportByStatusSheet implements FromArray, WithTitle
{
    public function __construct(public array $byStatus) {}

    public function array(): array
    {
        $rows = [['Status', 'Count', 'Revenue', 'Pending Revenue']];
        foreach ($this->byStatus as $status => $data) {
            $rows[] = [
                ucfirst($status),
                $data['count'] ?? 0,
                number_format($data['revenue'] ?? 0, 2),
                number_format($data['pending_revenue'] ?? 0, 2),
            ];
        }
        return $rows;
    }

    public function title(): string
    {
        return 'By Status';
    }
}

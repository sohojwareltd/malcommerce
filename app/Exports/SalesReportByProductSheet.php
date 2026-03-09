<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportByProductSheet implements FromArray, WithTitle
{
    public function __construct(public Collection $byProduct) {}

    public function array(): array
    {
        $rows = [['Product', 'Orders', 'Quantity', 'Revenue', 'Pending Revenue']];
        foreach ($this->byProduct as $row) {
            $rows[] = [
                $row['name'] ?? 'Unknown',
                $row['count'] ?? 0,
                $row['quantity'] ?? 0,
                number_format($row['revenue'] ?? 0, 2),
                number_format($row['pending_revenue'] ?? 0, 2),
            ];
        }
        return $rows;
    }

    public function title(): string
    {
        return 'By Product';
    }
}

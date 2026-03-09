<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportByProductTypeSheet implements FromArray, WithTitle
{
    public function __construct(public array $byProductType) {}

    public function array(): array
    {
        $rows = [['Product Type', 'Orders', 'Quantity', 'Revenue', 'Pending Revenue']];
        foreach ($this->byProductType as $type => $data) {
            $rows[] = [
                $data['name'] ?? ucfirst($type),
                $data['count'] ?? 0,
                $data['quantity'] ?? 0,
                number_format($data['revenue'] ?? 0, 2),
                number_format($data['pending_revenue'] ?? 0, 2),
            ];
        }
        return $rows;
    }

    public function title(): string
    {
        return 'By Product Type';
    }
}

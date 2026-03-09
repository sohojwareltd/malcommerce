<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportSummarySheet implements FromArray, WithTitle
{
    public function __construct(
        public array $stats,
        public string $dateFrom,
        public string $dateTo,
    ) {}

    public function array(): array
    {
        $s = $this->stats;
        return [
            ['Sales Report Summary'],
            ['Date Range', $this->dateFrom . ' to ' . $this->dateTo],
            [],
            ['Metric', 'Value'],
            ['Total Orders', $s['total_orders'] ?? 0],
            ['Revenue (Delivered)', number_format($s['revenue'] ?? 0, 2)],
            ['Pending Revenue', number_format($s['pending_revenue'] ?? 0, 2)],
            ['Average Order Value (Delivered)', number_format($s['average_order_value'] ?? 0, 2)],
            ['Total Items Sold', $s['total_items_sold'] ?? 0],
            ['Total Expenses', number_format($s['total_expenses'] ?? 0, 2)],
            ['Profit', number_format($s['profit'] ?? 0, 2)],
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
}

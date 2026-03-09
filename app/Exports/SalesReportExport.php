<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        public array $stats,
        public Collection $orders,
        public string $dateFrom,
        public string $dateTo,
    ) {}

    public function sheets(): array
    {
        return [
            new SalesReportSummarySheet($this->stats, $this->dateFrom, $this->dateTo),
            new SalesReportByProductTypeSheet($this->stats['by_product_type'] ?? []),
            new SalesReportByProductSheet($this->stats['by_product'] ?? collect()),
            new SalesReportByStatusSheet($this->stats['by_status'] ?? []),
            new SalesReportOrdersSheet($this->orders),
        ];
    }
}

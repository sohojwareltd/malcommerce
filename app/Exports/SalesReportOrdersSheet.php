<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportOrdersSheet implements FromArray, WithTitle
{
    public function __construct(public Collection $orders) {}

    public function array(): array
    {
        $rows = [['Order #', 'Date', 'Product', 'Type', 'Customer', 'Quantity', 'Amount', 'Status']];
        foreach ($this->orders as $o) {
            $rows[] = [
                $o->order_number,
                $o->created_at->format('Y-m-d'),
                $o->product?->name ?? '',
                ($o->product && $o->product->is_digital) ? 'Digital' : 'Physical',
                $o->customer_name,
                $o->quantity,
                $o->total_price,
                $o->status,
            ];
        }
        return $rows;
    }

    public function title(): string
    {
        return 'Orders';
    }
}

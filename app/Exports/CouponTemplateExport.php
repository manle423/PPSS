<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CouponTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            ['EXAMPLE10', 0.1, 100000, 50000, '2024-01-01', '2024-12-31', 1],
        ];
    }

    public function headings(): array
    {
        return [
            'code',
            'discount_value',
            'min_order_value',
            'max_discount',
            'start_date',
            'end_date',
            'status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

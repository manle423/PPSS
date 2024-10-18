<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Example Product', '', 'This is a description', 'Example Category', 250000, 100, 1500, 10, 5, 2, ''],
            ['Example Product', 'Variant 1', '', '', 275000, 50, 1600, 11, 6, 3, '2023-12-31'],
            ['Example Product', 'Variant 2', '', '', 300000, 30, 1700, 12, 7, 4, '2023-12-31'],
            ['Another Product', '', 'Another description', 'Another Category', 500000, 200, 2500, 20, 15, 12, ''],
            ['Another Product', 'Variant A', '', '', 550000, 100, 2600, 21, 16, 13, '2023-12-31'],
            ['Another Product', 'Variant B', '', '', 600000, 80, 2700, 22, 17, 14, '2023-12-31'],
        ];
    }

    public function headings(): array
    {
        return [
            'product_name', 'variant_name', 'description', 'category', 'price', 'stock_quantity', 
            'weight', 'length', 'width', 'height', 'exp_date'
        ];
    }
}
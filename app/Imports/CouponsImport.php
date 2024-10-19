<?php

namespace App\Imports;

use App\Models\Coupon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class CouponsImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    use SkipsErrors;

    private $duplicates = [];

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $existingCoupon = Coupon::where('code', $row['code'])->first();
        if ($existingCoupon) {
            $this->duplicates[] = $row['code'];
            return null;
        }

        return new Coupon([
            'code' => $row['code'],
            'discount_value' => $row['discount_value'],
            'min_order_value' => $row['min_order_value'],
            'max_discount' => $row['max_discount'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'status' => $row['status'],
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:255',
            'discount_value' => 'required|numeric|min:0|max:1',
            'min_order_value' => 'required|numeric|min:0',
            'max_discount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|boolean',
        ];
    }

    public function getDuplicates()
    {
        return $this->duplicates;
    }
}

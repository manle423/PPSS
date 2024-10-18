<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $category = Category::firstOrCreate(['name' => $row['category']]);

        $product = Product::create([
            'name' => $row['name'],
            'description' => $row['description'],
            'category_id' => $category->id,
            'price' => $row['price'],
            'stock_quantity' => $row['stock_quantity'],
            'weight' => $row['weight'] ?? null,
            'length' => $row['length'] ?? null,
            'width' => $row['width'] ?? null,
            'height' => $row['height'] ?? null,
        ]);

        // Create variants
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($row["variant_{$i}_name"])) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $row["variant_{$i}_name"],
                    'variant_price' => $row["variant_{$i}_price"],
                    'stock_quantity' => $row["variant_{$i}_stock_quantity"],
                    'weight' => $row["variant_{$i}_weight"] ?? null,
                    'length' => $row["variant_{$i}_length"] ?? null,
                    'width' => $row["variant_{$i}_width"] ?? null,
                    'height' => $row["variant_{$i}_height"] ?? null,
                    'exp_date' => $row["variant_{$i}_exp_date"] ?? null,
                ]);
            }
        }

        return $product;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'category' => 'required',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
        ];
    }
}

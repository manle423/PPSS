<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProductsImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                if (empty($row['variant_name'])) {
                    // This is a main product
                    $category = Category::firstOrCreate(['name' => $row['category']]);

                    Product::updateOrCreate(
                        ['name' => $row['product_name']],
                        [
                            'description' => $row['description'],
                            'category_id' => $category->id,
                            'price' => $row['price'],
                            'stock_quantity' => $row['stock_quantity'],
                            'weight' => $row['weight'] ?? null,
                            'length' => $row['length'] ?? null,
                            'width' => $row['width'] ?? null,
                            'height' => $row['height'] ?? null,
                        ]
                    );
                } else {
                    // This is a variant
                    $product = Product::where('name', $row['product_name'])->first();
                    if (!$product) {
                        throw new \Exception("Parent product '{$row['product_name']}' not found for variant '{$row['variant_name']}'.");
                    }

                    ProductVariant::updateOrCreate(
                        ['product_id' => $product->id, 'variant_name' => $row['variant_name']],
                        [
                            'variant_price' => $row['price'],
                            'stock_quantity' => $row['stock_quantity'],
                            'weight' => $row['weight'] ?? null,
                            'length' => $row['length'] ?? null,
                            'width' => $row['width'] ?? null,
                            'height' => $row['height'] ?? null,
                            'exp_date' => $row['exp_date'] ?? null,
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->errors[] = "Row {$row['product_name']}: " . $e->getMessage();
                Log::error("Import error for row {$row['product_name']}: " . $e->getMessage());
                continue; // Skip this row and continue with the next
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

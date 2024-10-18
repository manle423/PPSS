<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Validation\Rule;

class CategoriesImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    use SkipsErrors;

    private $rowCount = 0;
    private $restoredCount = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $category = Category::withTrashed()->where('name', $row['name'])->first();

        if ($category) {
            if ($category->trashed()) {
                $category->restore();
                $this->restoredCount++;
            }
            $category->update([
                'description' => $row['description'],
            ]);
            return $category;
        } else {
            $this->rowCount++;
            return new Category([
                'name' => $row['name'],
                'description' => $row['description'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name must not exceed 255 characters.',
        ];
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getRestoredCount()
    {
        return $this->restoredCount;
    }
}

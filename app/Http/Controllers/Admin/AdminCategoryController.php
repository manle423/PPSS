<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\CategoriesImport;
use App\Exports\CategoryTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;

class AdminCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    // public function index(){
    //     return view('shop-page.home');
    // } 

    //hien thi form them danh muc
    public function create()
    {
        return view('admin.categories.create');
    }

    // show list danh muc
    public function list(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $categories = $query->paginate(10);
        return view('admin.categories.list', compact('categories'));
    }

    //xu ly form them danh muc
    public function store(Request $request)
    {
        //xac thuc du lieu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        //kiem tra danh muc da ton tai chua
        $exist = Category::where('name', $request->input('name'))->first();
        if ($exist) {
            return redirect()->back()->withErrors(['name' => 'Category already exists.'])->withInput();
        }
        // tạo mới danh mục

        $category = new Category();
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->created_at = now();
        $category->save();

        return redirect()->route('admin.category.create')->with('success', 'Category added successfully. ID: ' . $category->id);
    }

    //cap nhat cate
    public function edit(int $id)
    {

        $category = Category::findOrFail($id);

        return view('admin.categories.edit', compact('category'));
    }


    public function update(Request $request, int $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Tìm kiếm category theo id
        $category = Category::findOrFail($id);

        // Cập nhật thông tin category
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->save();


        return redirect()->route('admin.category.list')->with('success', 'Category updated successfully!');
    }
    public function delete($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            return redirect()->route('admin.category.list')->with('success', 'Category deleted successfully.');
        }

        return redirect()->route('admin.category.list')->with('error', 'Category not found.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $import = new CategoriesImport;
            $result = Excel::import($import, $request->file('file'));

            $importedCount = $import->getRowCount();
            $restoredCount = $import->getRestoredCount();

            $message = "Categories imported successfully. ";
            $message .= "$importedCount new categories added. ";
            $message .= "$restoredCount existing categories restored or updated.";

            return redirect()->route('admin.category.list')->with('success', $message);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->back()->with('error', 'Error importing categories: ' . implode('<br>', $errors));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing categories: ' . $e->getMessage());
        }
    }

    public function exportTemplate()
    {
        return Excel::download(new CategoryTemplateExport, 'categories_template.xlsx');
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids');
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No categories selected.');
        }

        if ($action === 'delete') {
            Category::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Selected categories deleted successfully.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }
}

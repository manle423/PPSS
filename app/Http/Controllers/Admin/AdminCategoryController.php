<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function __construct(){
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
    public function list()
    {
        $categories = Category::paginate(10);
        if($categories==null)  return view('admin.categories.list');
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
            return redirect()->back()->withErrors(['name' => 'Category already exist.']);
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
}

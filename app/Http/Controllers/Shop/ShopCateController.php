<?php

namespace App\Http\Controllers\Shop;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShopCateController extends Controller
{
    //   //
    public function index(){
        return view('shop-page.home');
    } 
 
//hien thi form them danh muc
    public function create(){
        return view('shop-page.add-cate');
    } 
  // show list danh muc
    public function listCate(){
        $categories = Category::whereNull('deleted_at')->get();
        return view('shop-page.list-cate',compact('categories'));
    } 
 
    //xu ly form them danh muc
    public function store(Request $request){
        //xac thuc du lieu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        //kiem tra danh muc da ton tai chua
        $exist=Category::where('name',$request->input('name'))->first();
        if($exist){
            return redirect()->back()->withErrors(['name'=>'Category already exist.']);
        }
        // tạo mới danh mục

        $category=new Category();
        $category->name=$request->input('name');
        $category->description=$request->input('description');
        $category->created_at=now();
        $category->save();

        return redirect()->route('shop.addcate')->with('success', 'Category added successfully. ID: ' . $category->id);
    }

    //cap nhat cate
    public function editCate(int $id){
    
    $category = Category::findOrFail($id);
   
    return view('shop-page.edit-cate', compact('category'));
    }


    public function updateCate(Request $request, int $id)
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

   
    return redirect()->route('shop.listCate')->with('success', 'Category updated successfully!');
}
    public function deleteCate($id){
        $category = Category::find($id);

        if ($category) {
            $category->delete(); 
            return redirect()->route('shop.listCate')->with('success', 'Category deleted successfully.'); 
        }
    
        return redirect()->route('shop.listCate')->with('error', 'Category not found.');
    }
    


   
}

<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdminProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }
    public function list()
    {
        $products = Product::paginate(10);
        if ($products == null)  return view('admin.products.list');
        return view('admin.products.list', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants.*.variant_name' => 'nullable|string|max:255',
            'variants.*.variant_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.weight' => 'nullable|numeric|min:0',
            'variants.*.length' => 'nullable|numeric|min:0',
            'variants.*.width' => 'nullable|numeric|min:0',
            'variants.*.height' => 'nullable|numeric|min:0',
            'variants.*.exp_date' => 'nullable|date',
            'variants.*.variant_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Check if product already exists
            $existingProduct = Product::where('name', $request->name)
                ->where('category_id', $request->category_id)
                ->whereNull('deleted_at')
                ->first();

            if ($existingProduct) {
                throw new \Exception('This product already exists in the selected category.');
            }

            if ($request->hasFile('image')) {
                $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
            }

            $product = Product::create([
                'name' => $request->name ?? '',
                'description' => $request->description,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'image' => $uploadedFileUrl ?? null,
                'stock_quantity' => $request->stock_quantity,
                'weight' => $request->weight,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
                'created_at' => now(),
            ]);

            if ($request->has('variants')) {
                foreach ($request->variants as $index => $variant) {
                    $variantImageUrl = null;
                    if (isset($variant['variant_image']) && $variant['variant_image'] instanceof \Illuminate\Http\UploadedFile) {
                        $variantImageUrl = Cloudinary::upload($variant['variant_image']->getRealPath())->getSecurePath();
                    }

                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_name' => $variant['variant_name'],
                        'variant_price' => $variant['variant_price'],
                        'stock_quantity' => $variant['stock_quantity'],
                        'weight' => $variant['weight'],
                        'length' => $variant['length'],
                        'width' => $variant['width'],
                        'height' => $variant['height'],
                        'exp_date' => $variant['exp_date'],
                        'image' => $variantImageUrl ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.products.create')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }


    public function edit($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants.*.variant_name' => 'nullable|string|max:255',
            'variants.*.variant_price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.weight' => 'nullable|numeric|min:0',
            'variants.*.length' => 'nullable|numeric|min:0',
            'variants.*.width' => 'nullable|numeric|min:0',
            'variants.*.height' => 'nullable|numeric|min:0',
            'variants.*.exp_date' => 'nullable|date',
            'variants.*.variant_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);

            $uploadedFileUrl = $product->image;

            if ($request->hasFile('image')) {
                if ($product->image) {
                    $this->deleteImageFromCloudinary($product->image);
                }
                $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
            }

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'image' => $uploadedFileUrl,
                'stock_quantity' => $request->stock_quantity,
                'weight' => $request->weight, // cập nhật weight
                'length' => $request->length, // cập nhật length
                'width' => $request->width, // cập nhật width
                'height' => $request->height, // cập nhật height
                'updated_at' => now(),
            ]);


            $updatedVariantIds = [];
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    if (!empty($variantData['variant_name']) || !empty($variantData['variant_price'])) {
                        $variant = ProductVariant::updateOrCreate(
                            ['id' => $variantData['id'] ?? null, 'product_id' => $product->id],
                            [
                                'variant_name' => $variantData['variant_name'],
                                'variant_price' => $variantData['variant_price'],
                                'stock_quantity' => $variantData['stock_quantity'],
                                'weight' => $variantData['weight'], // cập nhật weight cho variant
                                'length' => $variantData['length'], // cập nhật length cho variant
                                'width' => $variantData['width'], // cập nhật width cho variant
                                'height' => $variantData['height'], // cập nhật height cho variant
                                'exp_date' => $variantData['exp_date'],
                            ]
                        );

                        if (isset($variantData['variant_image']) && $variantData['variant_image'] instanceof \Illuminate\Http\UploadedFile) {
                            if ($variant->image) {
                                $this->deleteImageFromCloudinary($variant->image);
                            }
                            $variantImageUrl = Cloudinary::upload($variantData['variant_image']->getRealPath())->getSecurePath();
                            $variant->image = $variantImageUrl;
                            $variant->save();
                        }

                        $updatedVariantIds[] = $variant->id;
                    }
                }
            }

            $existingVariantIds = $product->variants->pluck('id')->toArray();
            $variantsToDelete = array_diff($existingVariantIds, $updatedVariantIds);
            ProductVariant::destroy($variantsToDelete);

            DB::commit();
            return redirect()->route('admin.products.list')->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);


        if ($product->image) {
            $this->deleteImageFromCloudinary($product->image);
        }

        $product->delete();
        return redirect()->route('admin.products.list')->with('success', 'Product deleted successfully.');
    }

    private function deleteImageFromCloudinary($imageUrl)
    {
        // Extract public ID from Cloudinary URL
        $publicId = $this->getPublicIdFromUrl($imageUrl);
        if ($publicId) {
            Cloudinary::destroy($publicId);
        }
    }

    private function getPublicIdFromUrl($url)
    {
        // Extract public ID from Cloudinary URL
        $parts = parse_url($url);
        $path = explode('/', $parts['path']);
        $filename = end($path);
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    public function filter(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);
        $query = Product::query();
        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Lọc theo đơn giá
        if ($request->has('price') && $request->price != '') {
            $query->where('price', '<=', $request->price);
        }

        // Lọc theo số lượng sản phẩm
        if ($request->has('stock_quantity') && $request->stock_quantity != '') {
            $query->where('stock_quantity', '>=', $request->stock_quantity);
        }

        $products = $query->paginate(10);

        return view('admin.products.list', compact('products'));
    }

    public function destroyVariant($id)
    {
        $variant = ProductVariant::findOrFail($id);

        if ($variant->image) {
            $this->deleteImageFromCloudinary($variant->image);
        }

        $variant->delete();

        return response()->json(['success' => true]);
    }

    public function sale($id)
    {
        //lay chi tiet hoa don tu id san pham
        $product_sales = OrderItem::where('item_id', $id)->get();
        //lay variant

        $product = Product::find($id);

        // Kiểm tra nếu sản phẩm không tồn tại
        if (!$product) {
            return redirect()->back()->withErrors(['message' => 'Product not found.']);
        }

        return view('admin.products.sale', [
            'productName' => $product->name,
            'productId' => $product->id,

            'productSales' => $product_sales,  // Truyền dữ liệu OrderItem vào view
        ]);
    }
    public function search(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
        ]);
        $id = $request->input('id');
        $date = $request->input('date');
        $product = Product::find($id);
        // Lọc doanh thu sản phẩm theo ngày
        $productSales = OrderItem::whereDate('created_at', $date)
            ->where('item_id', $id)
            ->with('order')
            ->get();

        return view('admin.products.sale', [
            'productSales' => $productSales,
            'productName' =>  $product->name, // Thay thế bằng tên sản phẩm thật nếu cần
            'productId' => $product->id, // Hoặc id sản phẩm nếu cần
        ]);
    }

    public function find(Request $request)
    {
        $queryInput = $request->input('query');

        // Truy vấn sản phẩm dựa trên tên và tên danh mục
        $products = Product::where('name', 'LIKE', "%{$queryInput}%")
            ->orWhereHas('category', function ($query) use ($queryInput) {
                $query->where('name', 'LIKE', "%{$queryInput}%");
            })
            ->paginate(5);


        if ($products->isEmpty()) {
            session()->flash('message', 'Product not found.');
            return view('admin.products.index', compact('queryInput', 'products'));
        }

        return view('admin.products.index', compact('products', 'queryInput'));
    }

    public function importForm()
    {
        return view('admin.products.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return redirect()->route('admin.products.list')->with('success', 'Products imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing products: ' . $e->getMessage());
        }
    }

    public function exportTemplate()
    {
        return Excel::download(new TemplateExport, 'products_import_template.xlsx');
    }
}

class TemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'Example Product', 'This is a description', 'Example Category', 10.99, 100, 1.5, 10, 5, 2,
                'Variant 1', 11.99, 50, 1.6, 11, 6, 3, '2023-12-31',
                'Variant 2', 12.99, 30, 1.7, 12, 7, 4, '2023-12-31',
                'Variant 3', 13.99, 20, 1.8, 13, 8, 5, '2023-12-31'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'name', 'description', 'category', 'price', 'stock_quantity', 'weight', 'length', 'width', 'height',
            'variant_1_name', 'variant_1_price', 'variant_1_stock_quantity', 'variant_1_weight', 'variant_1_length', 'variant_1_width', 'variant_1_height', 'variant_1_exp_date',
            'variant_2_name', 'variant_2_price', 'variant_2_stock_quantity', 'variant_2_weight', 'variant_2_length', 'variant_2_width', 'variant_2_height', 'variant_2_exp_date',
            'variant_3_name', 'variant_3_price', 'variant_3_stock_quantity', 'variant_3_weight', 'variant_3_length', 'variant_3_width', 'variant_3_height', 'variant_3_exp_date'
        ];
    }
}

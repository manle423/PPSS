<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Models\Coupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    //xem danh sach coupon
    public function list(){
        $coupons=Coupon::paginate(10);
        if($coupons==null) return view('admin.coupons.list');
        return view('admin.coupons.list',compact('coupons'));
    }

    public function create(Request $request){
        return view('admin.coupons.create');
    }
    
    public function store(Request $request){
        $request->validate([
            'code' => 'required|string|max:255',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'required|numeric|min:0',
            'max_discount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|boolean',
        ]);
        $status = $request->input('status', 1);
        try{
        $existing=Coupon::where('code',$request->code)->whereNull('deleted_at')->first();
     if($existing){
        throw new \Exception('This coupon already exists.');
     }
        DB::beginTransaction();
       Coupon::create([
      'code' => $request->code,
      'discount_value' => $request->discount_value,
      'max_discount' => $request->max_discount,
      'min_order_value' => $request->min_order_value,
      'max_order_value' => $request->max_order_value,
      'start_date' => $request->start_date,
      'end_date' => $request->end_date,
      'status' => $status,
]);
     DB::commit();
     return redirect()->route('admin.coupon.create')->with('success', 'Coupon created successfully.');
    }catch(\Exception $e){
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
    
}
     public function detail($id){
       $coupon=Coupon::findOrFail($id);
       return view('admin.coupons.show',compact('coupon'));
     }

     public function edit($id){
        $coupon=Coupon::findOrFail($id);
        return view('admin.coupons.edit',compact('coupon'));
     }

     public function update(Request $request, $id)
     {
         $request->validate([
             'code' => 'required|string|max:255',
             'discount_value' => 'required|numeric|min:0',
             'min_order_value' => 'required|numeric|min:0',
             'max_discount' => 'required|numeric|min:0',
             'start_date' => 'required|date',
             'end_date' => 'required|date|after:start_date',
             'status' => 'required|boolean',
         ]);
     
         try {
           
             $coupon = Coupon::findOrFail($id);
     
             $coupon->update([
                 'code' => $request->code,
                 'discount_value' => $request->discount_value,
                 'max_discount' => $request->max_discount,
                 'min_order_value' => $request->min_order_value,
                 'max_order_value' => $request->max_order_value,
                 'start_date' => $request->start_date,
                 'end_date' => $request->end_date,
                 'status' => $request->input('status', 1),
             ]);
     
         
             return redirect()->route('admin.coupon.list')->with('success', 'Coupon updated successfully.');
             
         } catch (\Exception $e) {
           
             return redirect()->back()->withErrors(['error' => 'Something went wrong.'])->withInput();
         }
     }

     public function delete($id){
        $coupon=Coupon::findOrFail($id);
        $coupon->delete();
        return view('admin.coupons.list')->with('success', 'Coupon deleted successfully.');
       
     }
     
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Coupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\CouponsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CouponTemplateExport;

class AdminCouponController extends Controller
{
    //xem danh sach coupon
    public function list(Request $request)
    {
        $this->updateCouponStatus();

        $query = Coupon::query();

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('code', 'like', "%{$search}%");
        }

        // Filter
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $coupons = $query->paginate(10);
        return view('admin.coupons.list', compact('coupons'));
    }

    public function create(Request $request)
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
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
        $status = $request->input('status', 1);
        try {
            $existing = Coupon::where('code', $request->code)->whereNull('deleted_at')->first();
            if ($existing) {
                throw new \Exception('This coupon already exists.');
            }
            DB::beginTransaction();
            Coupon::create([
                'code' => $request->code,
                'discount_value' => $request->discount_value,
                'max_discount' => $request->max_discount,
                'min_order_value' => $request->min_order_value,

                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $status,
            ]);
            DB::commit();
            return redirect()->route('admin.coupon.create')->with('success', 'Coupon created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function detail($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
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

                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->input('status', 1),
            ]);

            $this->updateCouponStatus();
            return redirect()->route('admin.coupon.list')->with('success', 'Coupon updated successfully.');
        } catch (\Exception $e) {

            return redirect()->back()->withErrors(['error' => 'Something went wrong.'])->withInput();
        }
    }

    public function delete($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect()->route('admin.coupon.list')->with('success', 'Coupon deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $import = new CouponsImport();
            Excel::import($import, $request->file('file'));
            
            $duplicates = $import->getDuplicates();
            $successMessage = 'Coupons imported successfully.';
            
            if (!empty($duplicates)) {
                $successMessage .= ' The following codes were skipped due to duplication: ' . implode(', ', $duplicates);
            }
            
            return redirect()->route('admin.coupon.list')->with('success', $successMessage);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->back()->with('error', 'Error importing coupons: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing coupons: ' . $e->getMessage());
        }
    }

    private function updateCouponStatus()
    {

        $coupons = Coupon::where('end_date', '<=', now())
            ->where('status', 1)
            ->get();


        foreach ($coupons as $coupon) {
            $coupon->update(['status' => 0]);
        }
    }

    public function exportTemplate()
    {
        return Excel::download(new CouponTemplateExport, 'coupons_template.xlsx');
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids');
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No coupons selected.');
        }

        switch ($action) {
            case 'activate':
                Coupon::whereIn('id', $ids)->update(['status' => 1]);
                $message = 'Selected coupons activated successfully.';
                break;
            case 'deactivate':
                Coupon::whereIn('id', $ids)->update(['status' => 0]);
                $message = 'Selected coupons deactivated successfully.';
                break;
            case 'delete':
                Coupon::whereIn('id', $ids)->delete();
                $message = 'Selected coupons deleted successfully.';
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action.');
        }

        return redirect()->back()->with('success', $message);
    }
}

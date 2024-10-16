<?php

namespace App\Http\Controllers\Admin;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function list(){
        $users=User::paginate(10);
        return view('admin.customers.list',compact('users'));
    }

    public function edit($id){
        $user=User::findOrFail($id);
        return view('admin.customers.edit',compact('user'));
    }
    
    public function detail($id){
     $user=User::findOrFail($id);
        return view('admin.customers.show',compact('user'));
    }
    public function delete($id){
        $customer=User::findOrFail($id);
        $customer->delete();
        return redirect()->route('admin.customers.list')->with('success', 'Customer deleted successfully.');
    }
    public function orders($id){
        // Lấy order theo user id
        $orders = Order::where('user_id', $id)->with('user')->paginate(10);
    
        // Kiểm tra xem có đơn hàng hay không
        $username = $orders->isNotEmpty() ? $orders->first()->user->username : 'Unknown';
    
        return view('admin.customers.order-list', compact('orders', 'username'));
    }
    

   
}

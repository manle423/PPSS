<?php

namespace App\Http\Controllers\Admin;

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
    public function update(Request $request,$id){

    }
    public function detail($id){
     $user=User::findOrFail($id);
        return view('admin.customers.show',compact('user'));
    }
    public function destroy($id){
        $customer=User::findOrFail($id);
        $customer->detele();
        return redirect()->route('admin.customers.list')->with('success', 'Customer deleted successfully.');
    }
    public function orders($id){
        return view('admin.customers.order-list');
    }
}

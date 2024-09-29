<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function viewProfile()
    {
        $user = Auth::user();
        $addresses = $user->addresses->sortByDesc('is_default');
        $provinces = Province::with('districts')->get();
        return view('user.profile', compact('user', 'addresses', 'provinces'));
    }

    public function addAddress(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:15',
                'address_line_1' => 'required|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'district_id' => 'required|exists:districts,id',
                'province_id' => 'required|exists:provinces,id',
                'is_default' => 'nullable',
            ]);

            $user = Auth::user();
            $addressData = $request->all();
            $addressData['is_default'] = $request->has('is_default') ? true : false;

            // Check if is_default is true, set all other addresses to false
            if ($addressData['is_default']) {
                Address::where('user_id', $user->id)->update(['is_default' => false]);
            }

            $address = new Address($addressData);
            $user->addresses()->save($address);

            return redirect()->route('user.profile')->with('success', 'Address added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('user.profile')->with('error', 'Error adding address.');
        }
    }

    public function deleteAddress($id)
    {
        try {
            $address = Address::findOrFail($id);
            $address->delete();

            return redirect()->route('user.profile')->with('success', 'Address deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('user.profile')->with('error', 'Error deleting address.');
        }
    }

    public function editAddress(Request $request, $id)
    {
        try {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:15',
                'address_line_1' => 'required|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'district_id' => 'required|exists:districts,id',
                'province_id' => 'required|exists:provinces,id',
                'is_default' => 'nullable',
            ]);

            $address = Address::findOrFail($id);
            $addressData = $request->all();
            $addressData['is_default'] = $request->has('is_default') ? true : false;

            // Check if is_default is true, set all other addresses to false
            if ($addressData['is_default']) {
                Address::where('user_id', $address->user_id)->update(['is_default' => false]);
            }

            $address->update($addressData);

            return redirect()->route('user.profile')->with('success', 'Address updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('user.profile')->with('error', 'Error updating address.');
        }
    }

    public function getAddress($id)
    {
        $address = Address::findOrFail($id);
        return response()->json($address);
    }

    public function updateUserInfo(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'string|max:255',
                'username' => 'string|max:255',
                'phone_number' => 'string|max:15',
                'address' => 'string|max:255',
            ]);

            $user = Auth::user();
            $user->update($request->only('full_name', 'username', 'phone_number', 'address'));

            return redirect()->route('user.profile')->with('success', 'User information updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('user.profile')->with('error', 'Error updating user information.');
        }
    }
}
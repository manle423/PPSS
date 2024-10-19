<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Province;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public static function encryptAddressData($addressData)
    {
        $addressData['district_id'] = Crypt::encryptString($addressData['district_id']);
        $addressData['province_id'] = Crypt::encryptString($addressData['province_id']);
        $addressData['ward_id'] = Crypt::encryptString($addressData['ward_id']);
        $addressData['address_line_1'] = Crypt::encryptString($addressData['address_line_1']);
        $addressData['address_line_2'] = Crypt::encryptString($addressData['address_line_2']);
        return $addressData;
    }

    public static function decryptAddressData($addressData)
    {
        $addressData['district_id'] = Crypt::decryptString($addressData['district_id']);
        $addressData['province_id'] = Crypt::decryptString($addressData['province_id']);
        $addressData['ward_id'] = Crypt::decryptString($addressData['ward_id']);
        $addressData['address_line_1'] = Crypt::decryptString($addressData['address_line_1']);
        $addressData['address_line_2'] = Crypt::decryptString($addressData['address_line_2']);
        return $addressData;
    }

    public static function encryptAddress($address)
    {
        $address->ward_id = Crypt::encryptString($address->ward_id);
        $address->province_id = Crypt::encryptString($address->province_id);
        $address->district_id = Crypt::encryptString($address->district_id);
        $address->address_line_1 = Crypt::encryptString($address->address_line_1);
        $address->address_line_2 = Crypt::encryptString($address->address_line_2);
        return $address;
    }

    public static function decryptAddress($address)
    {
        $address->ward_id = Crypt::decryptString($address->ward_id);
        $address->province_id = Crypt::decryptString($address->province_id);
        $address->district_id = Crypt::decryptString($address->district_id);
        $address->address_line_1 = Crypt::decryptString($address->address_line_1);
        $address->address_line_2 = Crypt::decryptString($address->address_line_2);
        return $address;
    }

    public function viewProfile()
    {
        $user = Auth::user()->load('defaultAddress');
        $addresses = $user->addresses->load('province.districts.wards', 'district.wards', 'ward')->sortByDesc('is_default');
        $provinces = Province::with('districts.wards')->orderBy('name', 'asc')->get();
        // Decrypt the address
        foreach ($addresses as $address) {
            $address = $this->decryptAddress($address);
        }
        //dd($addresses);
        return view('user.profile', compact('user', 'addresses', 'provinces'));
    }

    public function addAddress(Request $request)
    {
        try {

            $validatedData = $this->profileService->validateAddressData($request);

            $user = Auth::user();
            $addressData = $validatedData;


            $existingAddressCount = $this->profileService->getExistingAddressCount();

            if ($existingAddressCount === 0 || $request->has('is_default')) {
                $addressData['is_default'] = true;
                $this->profileService->resetOtherDefaultAddresses();
            } else {
                $addressData['is_default'] = false;
            }

            // Encrypt the address data
            $addressData = $this->encryptAddressData($addressData);
            //dd($addressData);

            $address = $user->addresses()->create($addressData);

            if ($addressData['is_default']) {
                $this->profileService->updateUserDefaultAddress($address->id);
            }


            $message = $this->profileService->getAddressAddedMessage($existingAddressCount, $addressData['is_default']);

            return redirect()->route('user.profile')->with('success', $message);
        } catch (ValidationException $e) {
            return redirect()->route('user.profile')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->route('user.profile')->withErrors(['error' => 'Error adding address: ' . $e->getMessage()])->withInput();
        }
    }

    public function deleteAddress($id)
    {
        try {
            $user = Auth::user();
            $address = Address::findOrFail($id);

            if ($address->user_id !== $user->id) {
                return redirect()->route('user.profile')->withErrors(['error' => 'Unauthorized action.']);
            }

            if (!$this->profileService->canDeleteAddress($address)) {
                return redirect()->route('user.profile')->withErrors(['error' => 'This is your default address and cannot be deleted.']);
            }

            if ($user->default_address_id == $id) {
                $newDefaultAddress = $this->profileService->findNewDefaultAddress($id);
                $this->profileService->updateDefaultAddress($newDefaultAddress);
            }

            $address->delete();

            $message = 'Address deleted successfully. ' .
                (isset($newDefaultAddress) ? 'A new default address has been set.' : 'No default address remaining.');

            return redirect()->route('user.profile')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('user.profile')->withErrors(['error' => 'Error deleting address: ' . $e->getMessage()]);
        }
    }

    public function editAddress(Request $request, $id)
    {
        try {
            $validatedData = $this->profileService->validateAddressData($request);

            $user = Auth::user();
            $address = Address::findOrFail($id);

            if ($address->user_id !== $user->id) {
                return redirect()->route('user.profile')->withErrors(['error' => 'Unauthorized action.']);
            }

            $addressData = $validatedData;
            $addressCount = $this->profileService->getExistingAddressCount();

            $addressData['is_default'] = $this->profileService->determineDefaultStatus($addressCount, $request->has('is_default'));

            if ($addressData['is_default']) {
                $this->profileService->resetOtherDefaultAddresses($id);
                $this->profileService->updateUserDefaultAddress($id);
            } elseif ($user->default_address_id == $id) {
                $newDefaultAddress = $this->profileService->findNewDefaultAddress($id);
                $this->profileService->updateDefaultAddress($newDefaultAddress);
            }

            // Encrypt the address
            $addressData = $this->encryptAddressData($addressData);
            $address->update($addressData);

            $message = $this->profileService->getAddressUpdatedMessage($addressCount, $addressData['is_default'], $user->default_address_id == $id, isset($newDefaultAddress));

            return redirect()->route('user.profile')->with('success', $message);
        } catch (ValidationException $e) {
            return redirect()->route('user.profile')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->route('user.profile')->withErrors(['error' => 'Error updating address: ' . $e->getMessage()])->withInput();
        }
    }

    public function getAddress($id)
    {
        try {
            $address = Address::findOrFail($id);
            // Decrypt the address
            $this->decryptAddress($address);
            return response()->json($address);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error retrieving address: ' . $e->getMessage()], 400);
        }
    }

    public function updateUserInfo(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'full_name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
                'phone_number' => 'required|string|max:15',
            ]);

            $user = Auth::user();
            $user->update($validatedData);

            return redirect()->route('user.profile')->with('success', 'User information updated successfully.');
        } catch (ValidationException $e) {
            return redirect()->route('user.profile')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->route('user.profile')->withErrors(['error' => 'Error updating user information: ' . $e->getMessage()])->withInput();
        }
    }
}

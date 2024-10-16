<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Province;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function viewProfile()
    {
        $user = Auth::user()->load('defaultAddress');
        $addresses = $user->addresses->load('province.districts.wards', 'district.wards', 'ward')->sortByDesc('is_default');
        $provinces = Province::with('districts.wards')->orderBy('name', 'asc')->get();
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

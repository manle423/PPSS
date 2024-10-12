<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class ProfileService
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function validateAddressData(Request $request)
    {
        return $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'province_id' => 'required|exists:provinces,id',
            'ward_id' => 'required|exists:wards,id',
            'is_default' => 'nullable',
        ]);
    }

    public function getExistingAddressCount()
    {
        $user = $this->auth->user();
        if (!$user) {
            throw new \InvalidArgumentException('User is not authenticated');
        }
        return $user->addresses()->whereNull('deleted_at')->count();
    }

    public function resetOtherDefaultAddresses($excludeId = null)
    {
        $user = $this->auth->user();
        $query = Address::where('user_id', $user->id);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $query->whereNull('deleted_at')->update(['is_default' => false]);
    }

    public function updateUserDefaultAddress($addressId)
    {
        $user = $this->auth->user();
        $user->update(['default_address_id' => $addressId]);
    }

    public function findNewDefaultAddress($excludeId)
    {
        $user = $this->auth->user();
        return $user->addresses()
            ->where('id', '!=', $excludeId)
            ->whereNull('deleted_at')
            ->first();
    }

    public function updateDefaultAddress($newDefaultAddress)
    {
        if ($newDefaultAddress) {
            $this->updateUserDefaultAddress($newDefaultAddress->id);
            $newDefaultAddress->update(['is_default' => true]);
        } else {
            $this->updateUserDefaultAddress(null);
        }
    }

    public function determineDefaultStatus($addressCount, $isDefaultRequested)
    {
        return $addressCount === 1 || $isDefaultRequested;
    }

    public function getAddressAddedMessage($existingAddressCount, $isDefault)
    {
        $message = 'Address added successfully.';
        if ($existingAddressCount === 0) {
            $message .= ' This is your first address, so it has been set as default.';
        } elseif ($isDefault) {
            $message .= ' This address has been set as default.';
        }
        return $message;
    }

    public function getAddressUpdatedMessage($addressCount, $isDefault, $wasDefault, $newDefaultSet)
    {
        $message = 'Address updated successfully.';
        if ($addressCount === 1) {
            $message .= ' This is your only address, so it remains as default.';
        } elseif ($isDefault) {
            $message .= ' This address is now set as default.';
        } elseif ($wasDefault && $newDefaultSet) {
            $message .= ' A new default address has been set.';
        }
        return $message;
    }

    public function canDeleteAddress(Address $address)
    {
        $user = $this->auth->user();
        $addressCount = $this->getExistingAddressCount();
        return $addressCount > 1 || $address->id !== $user->default_address_id;
    }
}

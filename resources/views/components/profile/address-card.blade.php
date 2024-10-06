<link href="{{ asset('assets/vendor/css/profile.css') }}" rel="stylesheet">
<div class="card mb-3" id="address-card-{{ $address->id }}">
    {{-- @dd($address); --}}
    <div class="card-body">
        <div class="address-view">
            <p><strong>Full Name:</strong> {{ $address->full_name }}</p>
            <p><strong>Phone Number:</strong> {{ $address->phone_number }}</p>
            <p><strong>Address:</strong> {{ $address->address_line_1 }}, {{ $address->address_line_2 ?? '' }}, {{ $address->district->name }}, {{ $address->province->name }}</p>
            <p><strong>Is Default:</strong> {{ $address->is_default ? 'Yes' : 'No' }}</p>
            <button class="btn btn-secondary btn-edit-address" data-id="{{ $address->id }}">Edit</button>
            <form action="{{ route('user.delete-address', $address->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
        <div class="address-edit" style="display: none;">
            <form action="{{ route('user.edit-address', $address->id) }}" method="POST">
                @csrf
                @method('POST')
                <div class="form-group">
                    <label for="full_name_{{ $address->id }}">Full Name</label>
                    <input type="text" class="form-control" id="full_name_{{ $address->id }}" name="full_name" value="{{ $address->full_name }}" required>
                </div>
                <div class="form-group">
                    <label for="phone_number_{{ $address->id }}">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number_{{ $address->id }}" name="phone_number" value="{{ $address->phone_number }}" required>
                </div>
                <div class="form-group">
                    <label for="province_{{ $address->id }}">Province</label>
                    <select class="form-control" id="province_{{ $address->id }}" name="province_id" required>
                        <option value="">Select Province</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ $address->province_id == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="district_{{ $address->id }}">District</label>
                    <select class="form-control" id="district_{{ $address->id }}" name="district_id" required>
                        <option value="">Select District</option>
                        @foreach($address->province->districts as $district)
                            <option value="{{ $district->id }}" {{ $address->district_id == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="address_line_1_{{ $address->id }}">Address Line 1</label>
                    <input type="text" class="form-control" id="address_line_1_{{ $address->id }}" name="address_line_1" value="{{ $address->address_line_1 }}" required>
                </div>
                <div class="form-group">
                    <label for="address_line_2_{{ $address->id }}">Address Line 2</label>
                    <input type="text" class="form-control" id="address_line_2_{{ $address->id }}" name="address_line_2" value="{{ $address->address_line_2 }}">
                </div>
                <div class="form-group">
                    <label for="is_default_{{ $address->id }}">Is Default</label>
                    <input type="checkbox" id="is_default_{{ $address->id }}" name="is_default" {{ $address->is_default ? 'checked' : '' }}>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:10px;">Save</button>
                <button type="button" class="btn btn-secondary btn-cancel-edit"style="margin-top:10px;" data-id="{{ $address->id }}">Cancel</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinces = @json($provinces);
        const addressId = {{$address->id}};
        const districtSelect = document.getElementById('district_' + addressId);
        const provinceSelect = document.getElementById('province_' + addressId);

        provinceSelect.addEventListener('change', function() {
            const selectedProvinceId = this.value;
            districtSelect.innerHTML = '<option value="">Select District</option>';
            if (selectedProvinceId) {
                const selectedProvince = provinces.find(province => province.id == selectedProvinceId);
                selectedProvince.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
            }
        });
    });
</script>
<form action="{{ route('user.add-address') }}" method="POST" id="address-form">
    @csrf
    <input type="hidden" name="_method" value="POST" id="address-form-method">
    <input type="hidden" name="address_id" id="address-id">
    <div class="form-group">
        <label for="full_name" style="font-weight: bold";>Full Name</label>
        <input type="text" class="form-control" id="full_name" name="full_name" required>
    </div>
    <div class="form-group">
        <label for="phone_number" style="font-weight: bold;">Phone Number</label>
        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
    </div>
    <div class="form-group">
        <label for="province" style="font-weight: bold;">Province</label>
        <select class="form-control" id="province_id" name="province_id" required>
            <option value="">Select Province</option>
            @foreach($provinces as $province)
                <option value="{{ $province->id }}">{{ $province->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="district" style="font-weight: bold;">District</label>
        <select class="form-control" id="district_id" name="district_id" required>
            <option value="">Select District</option>
        </select>
    </div>
    <div class="form-group">
        <label for="address_line_1" style="font-weight: bold;">Address Line 1</label>
        <input type="text" class="form-control" id="address_line_1" name="address_line_1" required>
    </div>
    <div class="form-group">
        <label for="address_line_2" style="font-weight: bold;">Address Line 2</label>
        <input type="text" class="form-control" id="address_line_2" name="address_line_2">
    </div>
    <div class="form-group">
        <label for="is_default" style="font-weight: bold;">Is Default</label>
        <input type="checkbox" id="is_default" name="is_default">
    </div>
    <button type="submit" class="btn btn-primary">Save Address</button>
    <button type="button" id="cancel-address-form" class="btn btn-secondary">Cancel</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinces = @json($provinces);
        const districtSelect = document.getElementById('district_id');
        const provinceSelect = document.getElementById('province_id');

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
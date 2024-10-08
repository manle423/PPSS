<input type="hidden" name="_method" value="POST" id="address-form-method">
<input type="hidden" name="address_id" id="address-id">
<div class="form-group">
    <label for="new_full_name" style="font-weight: bold";>Full Name</label>
    <input type="text" class="form-control" id="new_full_name" name="full_name">
</div>
<div class="form-group">
    <label for="new_phone_number" style="font-weight: bold;">Phone Number</label>
    <input type="text" class="form-control" id="new_phone_number" name="phone_number">
</div>
<div class="form-group">
    <label for="new_province_id" style="font-weight: bold;">Province</label>
    <select class="form-control" id="new_province_id" name="province_id">
        <option value="">Select Province</option>
        @foreach ($provinces as $province)
            <option value="{{ $province->id }}">{{ $province->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="new_district_id" style="font-weight: bold;">District</label>
    <select class="form-control" id="new_district_id" name="district_id">
        <option value="">Select District</option>
    </select>
</div>
<div class="form-group">
    <label for="new_address_line_1" style="font-weight: bold;">Address Line 1</label>
    <input type="text" class="form-control" id="new_address_line_1" name="address_line_1">
</div>
<div class="form-group">
    <label for="new_address_line_2" style="font-weight: bold;">Address Line 2</label>
    <input type="text" class="form-control" id="new_address_line_2" name="address_line_2">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinces = @json($provinces);
        const districtSelect = document.getElementById('new_district_id');
        const provinceSelect = document.getElementById('new_province_id');

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

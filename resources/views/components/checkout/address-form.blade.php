@props(['provinces'])
<input type="hidden" name="_method" value="POST" id="address-form-method">
<input type="hidden" name="address_id" id="address-id">
<div class="form-group">
    <label for="new_full_name" style="font-weight: bold;">Full Name</label>
    <input type="text" class="form-control" id="new_full_name" name="full_name" value="{{ old('full_name') }}" required>
</div>
<div class="form-group">
    <label for="new_phone_number" style="font-weight: bold;">Phone Number</label>
    <input type="text" class="form-control" id="new_phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
</div>
<div class="form-group">
    <label for="new_province_id" style="font-weight: bold;">Province</label>
    <select class="form-control" id="new_province_id" name="province_id" required>
        <option value="">Select Province</option>
    </select>
</div>
<div class="form-group">
    <label for="new_district_id" style="font-weight: bold;">District</label>
    <select class="form-control" id="new_district_id" name="district_id" required>
        <option value="">Select District</option>
    </select>
</div>
<div class="form-group">
    <label for="new_ward_id" style="font-weight: bold;">Ward</label>
    <select class="form-control" id="new_ward_id" name="ward_id" required>
        <option value="">Select Ward</option>
    </select>
</div>
<div class="form-group">
    <label for="new_address_line_1" style="font-weight: bold;">Address Line 1</label>
    <input type="text" class="form-control" id="new_address_line_1" name="address_line_1" value="{{ old('address_line_1') }}" required>
</div>
<div class="form-group">
    <label for="new_address_line_2" style="font-weight: bold;">Address Line 2</label>
    <input type="text" class="form-control" id="new_address_line_2" name="address_line_2" value="{{ old('address_line_2') }}">
</div>

<script>
    function fetchAndPopulate(url, selectElement, selectedValue) {
        console.log('Fetching data from:', url);
        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log('Received data:', data);
                selectElement.innerHTML = '<option value="">Select</option>';
                if (data.code === 200 && Array.isArray(data.data)) {
                    data.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.ProvinceID || item.DistrictID || item.WardCode;
                        option.textContent = item.ProvinceName || item.DistrictName || item.WardName;
                        if (item.ProvinceID == selectedValue || item.DistrictID == selectedValue || item.WardCode == selectedValue) {
                            option.selected = true;
                        }
                        selectElement.appendChild(option);
                    });
                    console.log('Options populated for', selectElement.name, 'Count:', data.data.length);
                } else {
                    console.error('Failed to load data or invalid data structure');
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM content loaded, initializing selects');
        const provinceSelect = document.getElementById('new_province_id');
        const districtSelect = document.getElementById('new_district_id');
        const wardSelect = document.getElementById('new_ward_id');

        fetchAndPopulate('/api/provinces', provinceSelect, '');

        provinceSelect.addEventListener('change', function() {
            const selectedProvinceId = this.value;
            console.log(`Province changed to: ${selectedProvinceId}`);
            districtSelect.innerHTML = '<option value="">Select District</option>';
            wardSelect.innerHTML = '<option value="">Select Ward</option>';
            if (selectedProvinceId) {
                fetch(`/api/districts/${selectedProvinceId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.code === 200 && Array.isArray(data.data)) {
                            data.data.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.DistrictID;
                                option.textContent = district.DistrictName;
                                districtSelect.appendChild(option);
                            });
                        }
                    });
            }
        });

        districtSelect.addEventListener('change', function() {
            const selectedDistrictId = this.value;
            console.log(`District changed to: ${selectedDistrictId}`);
            wardSelect.innerHTML = '<option value="">Select Ward</option>';
            if (selectedDistrictId) {
                fetch(`/api/wards/${selectedDistrictId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.code === 200 && Array.isArray(data.data)) {
                            data.data.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.WardCode;
                                option.textContent = ward.WardName;
                                wardSelect.appendChild(option);
                            });
                        }
                    });
            }
        });

        console.log('Event listeners added to province and district selects');
    });
</script>

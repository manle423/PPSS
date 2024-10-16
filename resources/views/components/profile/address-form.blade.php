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
        </select>
    </div>
    <div class="form-group">
        <label for="district" style="font-weight: bold;">District</label>
        <select class="form-control" id="district_id" name="district_id" required>
            <option value="">Select District</option>
        </select>
    </div>
    <div class="form-group">
        <label for="ward" style="font-weight: bold;">Ward</label>
        <select class="form-control" id="ward_id" name="ward_id" required>
            <option value="">Select Ward</option>
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
    function fetchAndPopulate(url, selectElement, selectedValue) {
        console.log(`Fetching data from: ${url}`);
        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log(`Data received for ${selectElement.id}:`, data);
                selectElement.innerHTML = '<option value="">Select</option>';
                if (Array.isArray(data)) {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        if (item.id == selectedValue) {
                            option.selected = true;
                        }
                        selectElement.appendChild(option);
                    });
                } else if (data.code === 200 && Array.isArray(data.data)) {
                    data.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.ProvinceID || item.DistrictID || item.WardCode;
                        option.textContent = item.ProvinceName || item.DistrictName || item.WardName;
                        if (option.value == selectedValue) {
                            option.selected = true;
                        }
                        selectElement.appendChild(option);
                    });
                }
                console.log(`${selectElement.id} populated with ${selectElement.options.length - 1} options`);
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    function initializeAddressForm(provinceSelect, districtSelect, wardSelect, provinceId, districtId, wardId) {
        console.log('Initializing address form');
        fetchAndPopulate('/api/provinces', provinceSelect, provinceId);

        provinceSelect.addEventListener('change', function() {
            const selectedProvinceId = this.value;
            console.log(`Province changed to: ${selectedProvinceId}`);
            if (selectedProvinceId) {
                fetchAndPopulate(`/api/districts/${selectedProvinceId}`, districtSelect, districtId);
            } else {
                districtSelect.innerHTML = '<option value="">Select District</option>';
                wardSelect.innerHTML = '<option value="">Select Ward</option>';
                console.log('District and Ward selects reset');
            }
        });

        districtSelect.addEventListener('change', function() {
            const selectedDistrictId = this.value;
            console.log(`District changed to: ${selectedDistrictId}`);
            if (selectedDistrictId) {
                fetchAndPopulate(`/api/wards/${selectedDistrictId}`, wardSelect, wardId);
            } else {
                wardSelect.innerHTML = '<option value="">Select Ward</option>';
                console.log('Ward select reset');
            }
        });

        if (provinceId) {
            console.log(`Initial province ID: ${provinceId}, triggering change event`);
            provinceSelect.dispatchEvent(new Event('change'));
        } else {
            console.log('No province ID found, fetching default provinces');
            fetchAndPopulate('/api/provinces', provinceSelect, '');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM content loaded, initializing selects');
        const provinceSelect = document.getElementById('province_id');
        const districtSelect = document.getElementById('district_id');
        const wardSelect = document.getElementById('ward_id');

        fetch('/api/provinces')
            .then(response => response.json())
            .then(data => {
                console.log('Provinces data received:', data);
                if (data.code === 200 && Array.isArray(data.data)) {
                    data.data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.ProvinceID;
                        option.textContent = province.ProvinceName;
                        provinceSelect.appendChild(option);
                    });
                    console.log(`Province select populated with ${data.data.length} options`);
                } else {
                    console.error('Failed to load provinces:', data);
                    alert('Failed to load provinces.');
                }
            })
            .catch(error => console.error('Error fetching provinces:', error));

        provinceSelect.addEventListener('change', function() {
            const selectedProvinceId = this.value;
            console.log(`Province changed to: ${selectedProvinceId}`);
            districtSelect.innerHTML = '<option value="">Select District</option>';
            wardSelect.innerHTML = '<option value="">Select Ward</option>';
            if (selectedProvinceId) {
                fetch(`/api/districts/${selectedProvinceId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Districts data received:', data);
                        if (data.code === 200 && Array.isArray(data.data)) {
                            data.data.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.DistrictID;
                                option.textContent = district.DistrictName;
                                districtSelect.appendChild(option);
                            });
                            console.log(`District select populated with ${data.data.length} options`);
                        } else {
                            console.error('Failed to load districts:', data);
                            alert('Failed to load districts.');
                        }
                    })
                    .catch(error => console.error('Error fetching districts:', error));
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
                        console.log('Wards data received:', data);
                        if (data.code === 200 && Array.isArray(data.data)) {
                            data.data.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.WardCode;
                                option.textContent = ward.WardName;
                                wardSelect.appendChild(option);
                            });
                            console.log(`Ward select populated with ${data.data.length} options`);
                        } else {
                            console.error('Failed to load wards:', data);
                            alert('Failed to load wards.');
                        }
                    })
                    .catch(error => console.error('Error fetching wards:', error));
            }
        });

        console.log('Event listeners added to province and district selects');
    });
</script>

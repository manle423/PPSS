<link href="{{ asset('assets/vendor/css/profile.css') }}" rel="stylesheet">
<div class="card mb-3" id="address-card-{{ $address->id }}">
    {{-- @dd($address); --}}
    <div class="card-body">
        <div class="address-view">
            <p><strong>Full Name:</strong> {{ $address->full_name }}</p>
            <p><strong>Phone Number:</strong> {{ $address->phone_number }}</p>
            {{-- <p><strong>Address:</strong> {{ $address->address_line_1 ?? ''}}, {{ $address->address_line_2 ?? '' }}, {{ $address->ward->name ?? ''}}, {{ $address->district->name ?? ''}}, {{ $address->province->name ?? 'N/A'}}</p> --}}
            <p><strong>Address:</strong> 
                {{ $address->address_line_1 }}@if($address->address_line_2), {{ $address->address_line_2 }}@endif, <span class="ward-name"></span>, <span class="district-name"></span>, <span class="province-name"></span>
            </p>
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
                    <input type="text" class="form-control" id="full_name_{{ $address->id }}" name="full_name"
                        value="{{ $address->full_name }}" required>
                </div>
                <div class="form-group">
                    <label for="phone_number_{{ $address->id }}">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number_{{ $address->id }}"
                        name="phone_number" value="{{ $address->phone_number }}" required>
                </div>
                <div class="form-group">
                    <label for="province_{{ $address->id }}">Province</label>
                    <select class="form-control" id="province_{{ $address->id }}" name="province_id" required
                        data-original-value="{{ $address->province_id }}">
                        <option value="">Select Province</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="district_{{ $address->id }}">District</label>
                    <select class="form-control" id="district_{{ $address->id }}" name="district_id" required
                        data-original-value="{{ $address->district_id }}">
                        <option value="">Select District</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ward_{{ $address->id }}">Ward</label>
                    <select class="form-control" id="ward_{{ $address->id }}" name="ward_id" required
                        data-original-value="{{ $address->ward_id }}">
                        <option value="">Select Ward</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="address_line_1_{{ $address->id }}">Address Line 1</label>
                    <input type="text" class="form-control" id="address_line_1_{{ $address->id }}"
                        name="address_line_1" value="{{ $address->address_line_1 }}" required>
                </div>
                <div class="form-group">
                    <label for="address_line_2_{{ $address->id }}">Address Line 2</label>
                    <input type="text" class="form-control" id="address_line_2_{{ $address->id }}"
                        name="address_line_2" value="{{ $address->address_line_2 }}">
                </div>
                <div class="form-group">
                    <label for="is_default_{{ $address->id }}">Is Default</label>
                    <input type="checkbox" id="is_default_{{ $address->id }}" name="is_default"
                        {{ $address->is_default ? 'checked' : '' }}>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:10px;">Save</button>
                <button type="button" class="btn btn-secondary btn-cancel-edit"style="margin-top:10px;"
                    data-id="{{ $address->id }}">Cancel</button>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addressCards = document.querySelectorAll('.card');

        addressCards.forEach(function(card) {
            const provinceId = card.querySelector('[name="province_id"]').getAttribute('data-original-value');
            const districtId = card.querySelector('[name="district_id"]').getAttribute('data-original-value');
            const wardId = card.querySelector('[name="ward_id"]').getAttribute('data-original-value');

            const provinceName = card.querySelector('.province-name');
            const districtName = card.querySelector('.district-name');
            const wardName = card.querySelector('.ward-name');

            // Fetch province name
            fetch(`/api/province/${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    provinceName.textContent = data.name;
                })
                .catch(error => console.error('Error fetching province name:', error));

            // Fetch district name
            fetch(`/api/district/${districtId}`)
                .then(response => response.json())
                .then(data => {
                    districtName.textContent = data.name;
                })
                .catch(error => console.error('Error fetching district name:', error));

            // Fetch ward name
            fetch(`/api/ward/${wardId}?district_id=${districtId}`)
                .then(response => response.json())
                .then(data => {
                    wardName.textContent = data.name;
                })
                .catch(error => console.error('Error fetching ward name:', error));
        });
    });

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

    document.querySelectorAll('.btn-edit-address').forEach(function(button) {
        button.addEventListener('click', function() {
            var addressId = this.getAttribute('data-id');
            console.log('Editing address with ID:', addressId);
            var card = document.getElementById('address-card-' + addressId);
            card.querySelector('.address-view').style.display = 'none';
            card.querySelector('.address-edit').style.display = 'block';

            var provinceSelect = card.querySelector('[name="province_id"]');
            var districtSelect = card.querySelector('[name="district_id"]');
            var wardSelect = card.querySelector('[name="ward_id"]');

            var originalProvinceId = provinceSelect.getAttribute('data-original-value');
            var originalDistrictId = districtSelect.getAttribute('data-original-value');
            var originalWardId = wardSelect.getAttribute('data-original-value');

            console.log('Original values:', { province: originalProvinceId, district: originalDistrictId, ward: originalWardId });

            // Only fetch and populate if not already populated
            if (provinceSelect.options.length <= 1) {
                fetchAndPopulate('/api/provinces', provinceSelect, originalProvinceId);
            }

            if (districtSelect.options.length <= 1) {
                fetch(`/api/districts/${originalProvinceId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.code === 200 && Array.isArray(data.data)) {
                            districtSelect.innerHTML = '<option value="">Select District</option>';
                            data.data.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.DistrictID;
                                option.textContent = district.DistrictName;
                                if (district.DistrictID == originalDistrictId) {
                                    option.selected = true;
                                }
                                districtSelect.appendChild(option);
                            });
                        }
                    });
            }

            if (wardSelect.options.length <= 1) {
                fetch(`/api/wards/${originalDistrictId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.code === 200 && Array.isArray(data.data)) {
                            wardSelect.innerHTML = '<option value="">Select Ward</option>';
                            data.data.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.WardCode;
                                option.textContent = ward.WardName;
                                if (ward.WardCode == originalWardId) {
                                    option.selected = true;
                                }
                                wardSelect.appendChild(option);
                            });
                        }
                    });
            }

            // Add change event listeners only once
            if (!provinceSelect.hasListener) {
                provinceSelect.addEventListener('change', function() {
                    const selectedProvinceId = this.value;
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
                provinceSelect.hasListener = true;
            }

            if (!districtSelect.hasListener) {
                districtSelect.addEventListener('change', function() {
                    const selectedDistrictId = this.value;
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
                districtSelect.hasListener = true;
            }
        });
    });
</script>

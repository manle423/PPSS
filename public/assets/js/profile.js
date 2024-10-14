document.getElementById('show-address-form').addEventListener('click', function() {
    var formContainer = document.getElementById('address-form-container');
    formContainer.style.display = 'block';
    document.getElementById('address-form').reset();
    document.getElementById('address-form-method').value = 'POST';
    document.getElementById('address-form').action = addAddressRoute;
});

document.getElementById('address-form-container').addEventListener('click', function(event) {
    if (event.target.id === 'cancel-address-form') {
        var formContainer = document.getElementById('address-form-container');
        formContainer.style.display = 'none';
    }
});

document.querySelectorAll('.btn-edit-address').forEach(function(button) {
    button.addEventListener('click', function() {
        var addressId = this.getAttribute('data-id');
        var card = document.getElementById('address-card-' + addressId);
        card.querySelector('.address-view').style.display = 'none';
        card.querySelector('.address-edit').style.display = 'block';

        var provinceSelect = card.querySelector('[name="province_id"]');
        var districtSelect = card.querySelector('[name="district_id"]');
        var wardSelect = card.querySelector('[name="ward_id"]');
        
        if (provinceSelect && districtSelect && wardSelect) {
            var originalProvinceId = provinceSelect.value;
            var originalDistrictId = districtSelect.getAttribute('data-original-value');
            var originalWardId = wardSelect.getAttribute('data-original-value');

            // Trigger change event on province select to load districts
            provinceSelect.dispatchEvent(new Event('change'));
            
            // Use a recursive function to ensure correct order of operations
            function setDistrictAndWard() {
                if (districtSelect.options.length > 1) {
                    districtSelect.value = originalDistrictId;
                    districtSelect.dispatchEvent(new Event('change'));

                    function setWard() {
                        if (wardSelect.options.length > 1) {
                            wardSelect.value = originalWardId;
                        } else {
                            setTimeout(setWard, 50);
                        }
                    }
                    setWard();
                } else {
                    setTimeout(setDistrictAndWard, 50);
                }
            }
            setDistrictAndWard();
        }
    });
});

document.querySelectorAll('.btn-cancel-edit').forEach(function(button) {
    button.addEventListener('click', function() {
        var addressId = this.getAttribute('data-id');
        var card = document.getElementById('address-card-' + addressId);
        card.querySelector('.address-view').style.display = 'block';
        card.querySelector('.address-edit').style.display = 'none';
    });
});

document.querySelector('.btn-edit-user-info').addEventListener('click', function() {
    document.querySelector('.user-info-view').style.display = 'none';
    document.querySelector('.user-info-edit').style.display = 'block';
});

document.querySelector('.btn-cancel-edit-user-info').addEventListener('click', function() {
    document.querySelector('.user-info-view').style.display = 'block';
    document.querySelector('.user-info-edit').style.display = 'none';
});
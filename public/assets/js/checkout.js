document.addEventListener('DOMContentLoaded', function() {
    const newAddressCheckbox = document.getElementById('new_address');
    const newAddressForm = document.getElementById('new_address_form');
    const addressSelect = document.getElementById('address_id');
    const form = document.getElementById('checkout-form');
    const selectedAddressIdInput = document.getElementById('selected_address_id');
    const couponForm = document.getElementById('coupon-form');

    function toggleNewAddressForm() {
        if (!newAddressCheckbox) return;
        
        const isNewAddress = newAddressCheckbox.checked;
        if (newAddressForm) {
            newAddressForm.style.display = isNewAddress ? 'block' : 'none';
        }
        if (addressSelect) {
            addressSelect.disabled = isNewAddress;
        }

        const requiredFields = newAddressForm ? newAddressForm.querySelectorAll('input[id^="new_"], select[id^="new_"]') : [];
        requiredFields.forEach(field => {
            if (isNewAddress) {
                field.setAttribute('required', '');
            } else {
                field.removeAttribute('required');
            }
        });
    }

    if (newAddressCheckbox) {
        newAddressCheckbox.addEventListener('change', toggleNewAddressForm);
        toggleNewAddressForm();
    }

    if (addressSelect) {
        addressSelect.addEventListener('change', function() {
            selectedAddressIdInput.value = this.value;
        });
    }

    form.addEventListener('submit', function(event) {
        if (newAddressCheckbox && newAddressCheckbox.checked) {
            if (addressSelect) {
                addressSelect.disabled = true;
            }
            selectedAddressIdInput.value = ''; // Clear selected address ID when using a new address
        } else if (addressSelect) {
            if (addressSelect.value === '') {
                event.preventDefault();
                alert('Please select an address or create a new one.');
                return;
            }
            addressSelect.disabled = false;
            selectedAddressIdInput.value = addressSelect.value;
        }
        
        const newAddressFields = newAddressForm.querySelectorAll('input, select');
        newAddressFields.forEach(field => {
            const hiddenInput = couponForm.querySelector(`input[name="new_${field.name}"]`);
            if (hiddenInput) {
                hiddenInput.value = field.value;
            }
        });

        console.log('Form submitted with address_id:', selectedAddressIdInput.value);
    });

    couponForm.addEventListener('submit', function(event) {
        const newAddressFields = newAddressForm.querySelectorAll('input, select');
        newAddressFields.forEach(field => {
            const hiddenInput = couponForm.querySelector(`input[name="new_${field.name}"]`);
            if (hiddenInput) {
                hiddenInput.value = field.value;
            }
        });

        if (!newAddressCheckbox || !newAddressCheckbox.checked) {
            couponForm.querySelector('input[name="address_id"]').value = addressSelect.value;
        } else {
            couponForm.querySelector('input[name="address_id"]').value = '';
        }
    });
});

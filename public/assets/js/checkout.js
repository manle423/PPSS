document.addEventListener('DOMContentLoaded', function() {
    const newAddressCheckbox = document.getElementById('new_address');
    const newAddressForm = document.getElementById('new_address_form');
    const addressSelect = document.getElementById('address_id');
    const form = document.getElementById('checkout-form');
    const selectedAddressIdInput = document.getElementById('selected_address_id');

    function toggleNewAddressForm() {
        if (!newAddressCheckbox) return; // Exit if checkbox doesn't exist (no saved addresses)
        
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
        toggleNewAddressForm(); // Call this initially to set the correct state
    }

    if (addressSelect) {
        addressSelect.addEventListener('change', function() {
            selectedAddressIdInput.value = this.value;
        });
    }

    form.addEventListener('submit', function(event) {
        if (newAddressCheckbox && newAddressCheckbox.checked) {
            // If using a new address, ensure address_id is not sent
            if (addressSelect) {
                addressSelect.disabled = true;
            }
        } else if (addressSelect) {
            // If using an existing address, ensure it's selected
            if (addressSelect.value === '') {
                event.preventDefault();
                alert('Please select an address or create a new one.');
                return;
            }
            // Enable the select to ensure its value is sent
            addressSelect.disabled = false;
        }
        
        if (!newAddressCheckbox || !newAddressCheckbox.checked) {
            selectedAddressIdInput.value = addressSelect.value;
        } else {
            selectedAddressIdInput.value = '';
        }

        console.log('Form submitted with address_id:', selectedAddressIdInput.value);
    });
});


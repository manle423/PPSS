document.addEventListener("DOMContentLoaded", function () {
    const newAddressCheckbox = document.getElementById("new_address");
    const newAddressForm = document.getElementById("new_address_form");
    const addressSelect = document.getElementById("address_id");
    const form = document.getElementById("checkout-form");
    const selectedAddressIdInput = document.getElementById(
        "selected_address_id"
    );
    const couponForm = document.getElementById("coupon-form");
    const applyAddressButton = document.getElementById("applyAddressButton");
    const isGuest = !addressSelect; // Determine if the user is a guest

    function toggleNewAddressForm() {
        if (!newAddressCheckbox) return;

        const isNewAddress = newAddressCheckbox.checked;
        if (newAddressForm) {
            newAddressForm.style.display = isNewAddress ? "block" : "none";
        }
        if (addressSelect) {
            addressSelect.disabled = isNewAddress;
        }

        const requiredFields = newAddressForm
            ? newAddressForm.querySelectorAll(
                  'input[id^="new_"], select[id^="new_"]'
              )
            : [];
        requiredFields.forEach((field) => {
            if (isNewAddress && field.id !== "new_address_line_2") {
                field.setAttribute("required", "");
            } else {
                field.removeAttribute("required");
            }
        });
    }

    if (newAddressCheckbox) {
        newAddressCheckbox.addEventListener("change", toggleNewAddressForm);
        toggleNewAddressForm();
    }

    if (addressSelect) {
        addressSelect.addEventListener("change", function () {
            selectedAddressIdInput.value = this.value;
        });
    }

    if (applyAddressButton) {
        applyAddressButton.addEventListener("click", function () {
            let toDistrictId, toWardCode;

            if (isGuest || (newAddressCheckbox && newAddressCheckbox.checked)) {
                // Handle new address for guest or registered user with new address
                let newAddressFields = [];
                if (newAddressForm) {
                    newAddressFields = newAddressForm.querySelectorAll('input[id^="new_"], select[id^="new_"]');
                } else {
                    // If newAddressForm doesn't exist, try to find fields directly in the document
                    newAddressFields = document.querySelectorAll('input[id^="new_"], select[id^="new_"]');
                }

                let isValid = true;
                newAddressFields.forEach((field) => {
                    if (!field.value && field.id !== "new_address_line_2") {
                        isValid = false;
                        alert(
                            `Please fill out the ${field.id
                                .replace("new_", "")
                                .replace("_", " ")} field.`
                        );
                    }
                });

                if (!isValid) return;

                // Update hidden inputs for new address
                newAddressFields.forEach((field) => {
                    const hiddenInput = couponForm ? couponForm.querySelector(
                        `input[name="new_${field.name}"]`
                    ) : null;
                    if (hiddenInput) {
                        hiddenInput.value = field.value;
                    }
                });

                if (selectedAddressIdInput) {
                    selectedAddressIdInput.value = "";
                }

                toDistrictId = parseInt(document.getElementById('new_district_id').value, 10);
                toWardCode = document.getElementById('new_ward_id').value;
            } else {
                // Handle existing address for registered user
                if (addressSelect.value === "") {
                    alert("Please select an address.");
                    return;
                }
                if (selectedAddressIdInput) {
                    selectedAddressIdInput.value = addressSelect.value;
                }

                const selectedAddress = addressSelect.options[addressSelect.selectedIndex];
                toDistrictId = parseInt(selectedAddress.getAttribute('data-district-id'), 10);
                toWardCode = selectedAddress.getAttribute('data-ward-code');
            }

            // Ensure toWardCode is a string
            toWardCode = toWardCode.toString();

            // Call API to calculate shipping fee
            fetch("/checkout/calculate-shipping-fee", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: JSON.stringify({
                    to_district_id: toDistrictId,
                    to_ward_code: toWardCode,
                    weight: 1000,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    const shippingFee = data.data.shipping_fee;
                    const subtotal = data.data.subtotal;
                    const finalPrice = data.data.total;

                    const subtotalElement = document.getElementById("subtotal");
                    const shippingFeeElement = document.getElementById("shippingFee");
                    const totalAmountElement = document.getElementById("totalAmount");

                    if (subtotalElement) subtotalElement.textContent = formatNumber(subtotal) + " đ";
                    if (shippingFeeElement) shippingFeeElement.textContent = formatNumber(shippingFee) + " đ";
                    if (totalAmountElement) totalAmountElement.textContent = formatNumber(finalPrice) + " đ";
                    
                    // Update hidden input for total amount
                    const totalAmountInput = document.querySelector('input[name="total_amount"]');
                    if (totalAmountInput) totalAmountInput.value = finalPrice.toFixed(2);
                } else {
                    alert("Failed to calculate shipping fee: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while calculating the shipping fee.");
            });

            console.log("Address applied:", isGuest ? "Guest Address" : (selectedAddressIdInput ? selectedAddressIdInput.value : "Unknown"));

            // Close the modal
            const shippingModal = document.getElementById('shippingModal');
            if (shippingModal) {
                const bootstrapModal = bootstrap.Modal.getInstance(shippingModal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }
            }
        });
    }

    form.addEventListener("submit", function (event) {
        if (newAddressCheckbox && newAddressCheckbox.checked) {
            if (addressSelect) {
                addressSelect.disabled = true;
            }
            selectedAddressIdInput.value = ""; // Clear selected address ID when using a new address
        } else if (addressSelect) {
            if (addressSelect.value === "") {
                event.preventDefault();
                alert("Please select an address or create a new one.");
                return;
            }
            addressSelect.disabled = false;
            selectedAddressIdInput.value = addressSelect.value;
        }

        const newAddressFields = newAddressForm.querySelectorAll(
            'input[id^="new_"], select[id^="new_"]'
        );
        newAddressFields.forEach((field) => {
            const hiddenInput = couponForm.querySelector(
                `input[name="new_${field.name}"]`
            );
            if (hiddenInput) {
                hiddenInput.value = field.value;
            }
        });

        console.log(
            "Form submitted with address_id:",
            selectedAddressIdInput.value
        );
    });

    couponForm.addEventListener("submit", function (event) {
        const newAddressFields = newAddressForm.querySelectorAll(
            'input[id^="new_"], select[id^="new_"]'
        );
        newAddressFields.forEach((field) => {
            const hiddenInput = couponForm.querySelector(
                `input[name="new_${field.name}"]`
            );
            if (hiddenInput) {
                hiddenInput.value = field.value;
            }
        });

        if (!newAddressCheckbox || !newAddressCheckbox.checked) {
            couponForm.querySelector('input[name="address_id"]').value =
                addressSelect.value;
        } else {
            couponForm.querySelector('input[name="address_id"]').value = "";
        }
    });
});

function formatNumber(number) {
    return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

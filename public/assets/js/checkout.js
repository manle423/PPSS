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
            if (newAddressCheckbox.checked) {
                // Handle new address
                const newAddressFields = newAddressForm.querySelectorAll(
                    'input[id^="new_"], select[id^="new_"]'
                );
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
                    const hiddenInput = couponForm.querySelector(
                        `input[name="new_${field.name}"]`
                    );
                    if (hiddenInput) {
                        hiddenInput.value = field.value;
                    }
                });

                selectedAddressIdInput.value = "";
            } else {
                // Handle existing address
                if (addressSelect.value === "") {
                    alert("Please select an address.");
                    return;
                }
                selectedAddressIdInput.value = addressSelect.value;
            }

            // Lấy district_id và ward_code từ form
            let toDistrictId, toWardCode;
            if (newAddressCheckbox.checked) {
                toDistrictId = parseInt(document.getElementById('new_district_id').value, 10);
                toWardCode = document.getElementById('new_ward_id').value;
            } else {
                const selectedAddress = addressSelect.options[addressSelect.selectedIndex];
                console.log(selectedAddress)
                toDistrictId = parseInt(selectedAddress.getAttribute('data-district-id'), 10);
                toWardCode = selectedAddress.getAttribute('data-ward-code');
            }

            // Đảm bảo toWardCode là chuỗi
            toWardCode = toWardCode.toString();

            // Gọi API để tính phí vận chuyển
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
                    
                    // Cập nhật giá trị input hidden cho tổng số tiền
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

            // Trigger any additional logic to calculate shipping fee here
            console.log(
                "Address applied with ID:",
                selectedAddressIdInput.value
            );
            // Close the modal
            $("#shippingModal").modal("hide");
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
    return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

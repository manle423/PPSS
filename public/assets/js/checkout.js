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

                selectedAddressIdInput.value = ""; // Clear selected address ID when using a new address
            } else {
                // Handle existing address
                if (addressSelect.value === "") {
                    alert("Please select an address.");
                    return;
                }
                selectedAddressIdInput.value = addressSelect.value;
            }

            // Call server to calculate shipping fee
            fetch(
                "https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee",
                {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Token: window.GHNConfig.token,
                        ShopId: window.GHNConfig.shopId,
                    },
                    body: JSON.stringify({
                        from_district_id: 1454,
                        from_ward_code: "21211",
                        service_id: 53320,
                        service_type_id: null,
                        to_district_id: 1452,
                        to_ward_code: "21012",
                        height: 50,
                        length: 20,
                        weight: 200,
                        width: 20,
                        insurance_value: 10000,
                        cod_failed_amount: 2000,
                        coupon: null,
                    }),
                }
            )
                .then((response) => response.json())
                .then((data) => {
                    console.log(window.GHNConfig);
                    console.log(data); // Log the response for debugging
                    if (data.code === 200) {
                        document.getElementById("shippingFee").textContent =
                            data.data.total + " đ";
                    } else {
                        alert("Failed to calculate shipping fee.");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert(
                        "An error occurred while calculating the shipping fee."
                    );
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

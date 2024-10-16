document.addEventListener('DOMContentLoaded', function() {
    const addVariantButton = document.getElementById("add-variant-button");
    const variantsContainer = document.getElementById("variants-container");
    const variantTemplate = document.getElementById("variant-template");
    alert('click');
    if (addVariantButton && variantsContainer && variantTemplate) {
        addVariantButton.addEventListener("click", function () {
            const variantGroups = variantsContainer.getElementsByClassName("variant-group");
            const newIndex = variantGroups.length;
            const newVariantGroup = variantTemplate.content.cloneNode(true).firstElementChild;
            
            newVariantGroup.querySelectorAll("input, select").forEach((input) => {
                const name = input.getAttribute("name");
                if (name) {
                    input.setAttribute(
                        "name",
                        name.replace(/__INDEX__/g, newIndex)
                    );
                }
            });

            newVariantGroup
                .querySelector(".remove-variant-button")
                .addEventListener("click", function () {
                    newVariantGroup.remove();
                });

            variantsContainer.appendChild(newVariantGroup);
        });

        // Add event listeners to existing remove buttons
        document.querySelectorAll(".remove-variant-button").forEach((button) => {
            button.addEventListener("click", function () {
                const variantGroup = button.closest(".variant-group");
                const variantId = variantGroup.querySelector('input[name$="[id]"]')?.value;
                
                if (variantId) {
                    if (confirm('Are you sure you want to delete this variant?')) {
                        fetch(`/admin/products/variants/${variantId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                variantGroup.remove();
                            } else {
                                alert('Failed to delete variant');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the variant');
                        });
                    }
                } else {
                    variantGroup.remove();
                }
            });
        });
    }
});

function removeEmptyVariants() {
    const variantGroups = document.querySelectorAll('.variant-group');
    variantGroups.forEach((group) => {
        const inputs = group.querySelectorAll('input:not([type="file"])');
        let isEmpty = true;
        inputs.forEach((input) => {
            if (input.value.trim() !== '') {
                isEmpty = false;
            }
        });
        if (isEmpty) {
            group.remove();
        }
    });
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            let preview = document.getElementById('product-image-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'product-image-preview';
                preview.style.maxWidth = '200px';
                preview.style.maxHeight = '200px';
                input.parentNode.insertBefore(preview, input);
            }
            preview.src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeEmptyVariants() {
    const variantGroups = document.querySelectorAll('.variant-group:not(#variant-template)');
    variantGroups.forEach((group) => {
        const inputs = group.querySelectorAll('input:not([type="file"])');
        let isEmpty = true;
        inputs.forEach((input) => {
            if (input.value.trim() !== '') {
                isEmpty = false;
            }
        });
        if (isEmpty) {
            group.remove();
        }
    });
}
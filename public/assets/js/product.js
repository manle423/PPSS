document
    .getElementById("add-variant-button")
    .addEventListener("click", function () {
        const container = document.getElementById("variants-container");
        const variantGroups = container.getElementsByClassName("variant-group");
        const newIndex = variantGroups.length;
        const newVariantGroup = variantGroups[0].cloneNode(true);
        newVariantGroup.style.display = "block";

        newVariantGroup.querySelectorAll("input").forEach((input) => {
            const name = input.getAttribute("name");
            input.setAttribute(
                "name",
                name.replace(/\[\d+\]/, `[${newIndex}]`)
            );
            input.value = "";
        });

        newVariantGroup
            .querySelector(".remove-variant-button")
            .addEventListener("click", function () {
                newVariantGroup.remove();
            });

        container.appendChild(newVariantGroup);
    });

document.querySelectorAll(".remove-variant-button").forEach((button) => {
    button.addEventListener("click", function () {
        button.closest(".variant-group").remove();
    });
});

function removeEmptyVariants() {
    const container = document.getElementById("variants-container");
    const variantGroups = container.getElementsByClassName("variant-group");

    Array.from(variantGroups).forEach((group) => {
        const inputs = group.querySelectorAll("input");
        let isEmpty = true;

        inputs.forEach((input) => {
            if (input.value.trim() !== "") {
                isEmpty = false;
            }
        });

        if (isEmpty) {
            group.remove();
        }
    });
}

// Preview uploaded image
export function initImagePreview(inputSelector, previewSelector, removeBtnSelector = null) {
    const imageInput = document.querySelector(inputSelector);
    const preview = document.querySelector(previewSelector);
    const removeBtn = removeBtnSelector ? document.querySelector(removeBtnSelector) : null;

    if (!imageInput || !preview) return;

    imageInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (removeBtn) removeBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.classList.add('hidden');
            if (removeBtn) removeBtn.classList.add('hidden');
        }
    });

    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            imageInput.value = '';
            preview.src = '';
            preview.classList.add('hidden');
            removeBtn.classList.add('hidden');
        });
    }
}

// Enable cancel button only when any input has data
export function initCancelButton(formSelector, cancelBtnSelector) {
    const form = document.querySelector(formSelector);
    const cancelBtn = document.querySelector(cancelBtnSelector);

    if (!form || !cancelBtn) return;

    const inputs = form.querySelectorAll("input[type='text'], input[type='email'], input[type='password'], input[type='file']");

    const checkInputs = () => {
        let hasValue = false;
        inputs.forEach(input => {
            if (input.value.trim() !== "") hasValue = true;
        });

        cancelBtn.classList.toggle("opacity-50", !hasValue);
        cancelBtn.classList.toggle("pointer-events-none", !hasValue);
    };

    inputs.forEach(input => {
        input.addEventListener("input", checkInputs);
        input.addEventListener("change", checkInputs);
    });

    checkInputs();
}

// Disable update button if no form changes are made
export function initUpdateButtonDisable(formSelector, buttonSelector) {
    const form = document.querySelector(formSelector);
    const button = document.querySelector(buttonSelector);
    if (!form || !button) return;

    const originalValues = {};
    const inputs = form.querySelectorAll("input[name]:not([type='hidden'])");

    inputs.forEach(input => {
        originalValues[input.name] = input.value;
    });

    function checkChanges() {
        let changed = false;

        inputs.forEach(input => {
            if (input.type === 'file') {
                if (input.files.length > 0) changed = true;
            } else if (input.value !== originalValues[input.name]) {
                changed = true;
            }
        });

        button.disabled = !changed;
        button.classList.toggle('opacity-50', !changed);
        button.classList.toggle('pointer-events-none', !changed);
    }

    inputs.forEach(input => {
        input.addEventListener('input', checkChanges);
        input.addEventListener('change', checkChanges);
    });

    checkChanges(); // run on load
}


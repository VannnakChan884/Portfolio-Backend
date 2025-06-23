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
    const fields = form.querySelectorAll("input[name]:not([type='hidden']), select[name], textarea[name]");

    fields.forEach(field => {
        if (field.type === 'file') return;
        originalValues[field.name] = field.value;
    });

    function checkChanges() {
        let changed = false;

        fields.forEach(field => {
            if (field.type === 'file') {
                if (field.files.length > 0) changed = true;
            } else if (field.value !== originalValues[field.name]) {
                changed = true;
            }
        });

        button.disabled = !changed;
        button.classList.toggle('opacity-50', !changed);
        button.classList.toggle('pointer-events-none', !changed);
    }

    fields.forEach(field => {
        field.addEventListener('input', checkChanges);
        field.addEventListener('change', checkChanges); // especially important for <select>
    });

    checkChanges(); // Run once on load
}


// Show Image Preview on File Upload
document.getElementById('user_profile').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    const uploadBox = document.getElementById('uploadBox');
    const fileActionBtn = document.getElementById('fileActionBtn');

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
            uploadBox.classList.add('hidden');
            fileActionBtn.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

// Reset/Remove Image Preview
document.getElementById('removeImageBtn').addEventListener('click', function () {
    const previewContainer = document.getElementById('previewContainer');
    const uploadBox = document.getElementById('uploadBox');
    const fileActionBtn = document.getElementById('fileActionBtn');
    const fileInput = document.getElementById('user_profile');
    const imagePreview = document.getElementById('imagePreview');

    fileInput.value = '';
    imagePreview.src = '';
    previewContainer.classList.add('hidden');
    uploadBox.classList.remove('hidden');
    fileActionBtn.classList.add('hidden');
});

//  Auto-Hide message After 3 Seconds
function autoHideMessage(id) {
    const el = document.getElementById(id);
    if (el) {
        setTimeout(() => {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }, 3000);
    }
}

autoHideMessage('successMessage');
autoHideMessage('errorMessage');

// Drag & Drop Support
const dropArea = document.getElementById('uploadBox');
const fileInput = document.getElementById('user_profile');

if (dropArea && fileInput) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.add('bg-blue-50', 'border-blue-500');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.remove('bg-blue-50', 'border-blue-500');
        });
    });

    dropArea.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type.startsWith('image/')) {
            fileInput.files = files;

            const reader = new FileReader();
            reader.onload = (event) => {
                const preview = document.getElementById('imagePreview');
                const previewContainer = document.getElementById('previewContainer');
                const fileActionBtn = document.getElementById('fileActionBtn');

                preview.src = event.target.result;
                previewContainer.classList.remove('hidden');
                dropArea.classList.add('hidden');
                fileActionBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(files[0]);
        }
    });
}

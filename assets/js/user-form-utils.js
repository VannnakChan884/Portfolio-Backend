export function handleUserFormAjax(formSelector, endpoint) {
    const form = document.querySelector(formSelector);
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = form.querySelector('button[name="add_user"], button[name="update_user"]');
        const formData = new FormData(form);

        if (submitBtn && submitBtn.name === 'add_user') {
            formData.append('action', 'add_user');
        } else if (submitBtn && submitBtn.name === 'update_user') {
            formData.append('action', 'update_user');
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showSuccess(result.message || 'User saved successfully.');

                // Close modal after success
                const modal = document.getElementById('addUserModal');
                if (modal) {
                    modal.classList.remove('opacity-100');
                    modal.classList.add('opacity-0');
                    setTimeout(() => {
                        modal.classList.add('invisible');
                    }, 300);
                }

                // ✅ Clean the URL and reload page
                const cleanUrl = window.location.origin + window.location.pathname;
                setTimeout(() => {
                    window.location.href = cleanUrl;
                }, 1000); // slight delay for user to see message
            } else {
                showError(result.message || 'Something went wrong.');
            }

        } catch (err) {
            console.error('Error submitting form:', err);
            showError('Failed to send request.');
        }

        submitBtn.disabled = false;
        submitBtn.textContent = submitBtn.name === 'update_user' ? 'Update User' : 'Add User';
    });
}

export function populateEditForm(user) {
    const nameField = document.getElementById("edit-name");
    const emailField = document.getElementById("edit-email");
    const roleField = document.getElementById("edit-role");

    if (nameField) nameField.value = user.name;
    if (emailField) emailField.value = user.email;
    if (roleField) {
        roleField.value = user.role;
        if (user.is_default_admin === "1" || user.is_default_admin === 1) {
            roleField.setAttribute("disabled", "disabled");
            roleField.title = "Default admin role cannot be changed";
        } else {
            roleField.removeAttribute("disabled");
            roleField.removeAttribute("title");
        }
    }
}

// Utility toast
function showSuccess(message) {
    const toast = document.getElementById('toastSuccess');
    if (toast) {
        toast.textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 6000);
    } else {
        alert(message);
    }
}

function showError(message) {
    let errorBox = document.getElementById('errorMessage');
    if (!errorBox) {
        errorBox = document.createElement('div');
        errorBox.id = 'errorMessage';
        errorBox.className = 'bg-red-500 text-white p-3 rounded mb-4';
        form.prepend(errorBox);
    }
    errorBox.textContent = message;
}
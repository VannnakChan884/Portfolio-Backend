import { toast } from './toast-utils.js';
export function setupDeleteModal({
    modalId = '#deleteConfirmModal',
    confirmBtnId = '#confirmDeleteBtn',
    cancelBtnId = '#cancelDeleteBtn',
    deleteBtnSelector = '[data-delete-id]',
    endpoint = 'user-handler.php' // New centralized handler
}) {
    const modal = document.querySelector(modalId);
    const confirmBtn = document.querySelector(confirmBtnId);
    const cancelBtn = document.querySelector(cancelBtnId);

    let deleteTargetId = null;
    let deleteRow = null;

    document.querySelectorAll(deleteBtnSelector).forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            deleteTargetId = button.getAttribute('data-delete-id');
            deleteRow = button.closest('tr');
            modal.classList.remove('hidden');
        });
    });

    cancelBtn?.addEventListener('click', () => {
        modal.classList.add('hidden');
        deleteTargetId = null;
    });

    confirmBtn?.addEventListener('click', async () => {
        if (!deleteTargetId) return;

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'delete_user',
                    id: deleteTargetId
                })
            });

            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                const data = await response.json();

                if (data.success) {
                    deleteRow.classList.add('opacity-0', 'transition', 'duration-300');
                    setTimeout(() => deleteRow.remove(), 200);
                    toast(data.message || 'User deleted successfully.', data.status || 'success');
                } else {
                    toast(data.message || 'Failed to delete user.', data.status || 'error');
                }
            } else {
                const text = await response.text();
                throw new Error("Not JSON: " + text);
            }
        } catch (err) {
            console.error("Delete request error:", err);
            toast('Server error: ' + err.message, 'error');
        }

        modal.classList.add('hidden');
        deleteTargetId = null;
    });
}

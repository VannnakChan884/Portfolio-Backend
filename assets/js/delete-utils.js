export function setupDeleteModal({
    modalId = '#deleteConfirmModal',
    confirmBtnId = '#confirmDeleteBtn',
    cancelBtnId = '#cancelDeleteBtn',
    toastId = '#toastSuccess',
    deleteBtnSelector = '[data-delete-id]',
    endpoint = 'delete.php?id='
}) {
    const modal = document.querySelector(modalId);
    const confirmBtn = document.querySelector(confirmBtnId);
    const cancelBtn = document.querySelector(cancelBtnId);
    const toast = document.querySelector(toastId);

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

    confirmBtn?.addEventListener('click', () => {
        if (!deleteTargetId) return;

        fetch(`${endpoint}${deleteTargetId}`, {
            method: 'GET',
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    deleteRow.classList.add('opacity-0', 'transition', 'duration-300');
                    setTimeout(() => deleteRow.remove(), 200);

                    toast?.classList.remove('hidden');
                    setTimeout(() => toast?.classList.add('hidden'), 3000);
                } else {
                    alert('Failed to delete item.');
                }
            }).catch(() => alert('Server error.'));

        modal.classList.add('hidden');
        deleteTargetId = null;
    });
}

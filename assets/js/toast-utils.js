export function toast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-7 right-7 z-50 px-4 py-2 rounded shadow text-white text-sm transition-all duration-300`;

    switch (type) {
        case 'success':
            toast.classList.add('bg-green-600');
            break;
        case 'info':
            toast.classList.add('bg-blue-600');
            break;
        case 'danger':
        case 'error':
            toast.classList.add('bg-red-600');
            break;
        default:
            toast.classList.add('bg-gray-700');
            break;
    }


    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition', 'duration-500');
        setTimeout(() => toast.remove(), 500);
    }, 4000);

    console.log("✅ toast-utils.js loaded");
}


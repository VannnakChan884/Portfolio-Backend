export function toast(message, type = "info") {
  const toast = document.createElement("div");
  toast.className = `
    fixed bottom-6 left-1/2 transform -translate-x-1/2
    z-50 px-5 py-3 rounded-lg shadow-lg flex items-center gap-3
    text-white text-sm font-medium transition-all duration-500 animate-fadein
  `;

  // Icon
  const icon = document.createElement("i");
  icon.className = "text-white";

  switch (type) {
    case "success":
      toast.classList.add("bg-green-600");
      icon.classList.add("fas", "fa-check-circle");
      break;
    case "info":
      toast.classList.add("bg-blue-600");
      icon.classList.add("fas", "fa-info-circle");
      break;
    case "error":
    case "danger":
      toast.classList.add("bg-red-600");
      icon.classList.add("fas", "fa-exclamation-circle");
      break;
    default:
      toast.classList.add("bg-gray-700");
      icon.classList.add("fas", "fa-bell");
  }

  const msg = document.createElement("span");
  msg.textContent = message;

  // Close button
  const closeBtn = document.createElement("button");
  closeBtn.innerHTML = "&times;";
  closeBtn.className = "ml-auto text-xl font-bold focus:outline-none";
  closeBtn.onclick = () => toast.remove();

  toast.appendChild(icon);
  toast.appendChild(msg);
  toast.appendChild(closeBtn);
  document.body.appendChild(toast);

  // Auto-dismiss
  const dismissTimer = setTimeout(() => {
    toast.classList.add("opacity-0");
    setTimeout(() => toast.remove(), 500);
  }, 4000);

  // Cancel auto-dismiss if user closes manually
  closeBtn.onclick = () => {
    clearTimeout(dismissTimer);
    toast.remove();
  };
}

// Fade-in animation style
const style = document.createElement("style");
style.textContent = `
  @keyframes fadein {
    from { opacity: 0; transform: translate(-50%, 20px); }
    to { opacity: 1; transform: translate(-50%, 0); }
  }
  .animate-fadein {
    animation: fadein 0.4s ease-out;
  }
`;
document.head.appendChild(style);

console.log("✅ toast-utils.js loaded with close button");

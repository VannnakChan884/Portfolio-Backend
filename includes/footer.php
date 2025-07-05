<script>
    // Dark mode toggle
    const toggleBtn = document.getElementById('dark-mode-toggle');
    const htmlEl = document.documentElement;

    if (localStorage.getItem('theme') === 'dark' ||
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlEl.classList.add('dark');
    }

    function updateToggleIcon() {
        toggleBtn.textContent = htmlEl.classList.contains('dark') ? '☀️' : '🌙';
    }

    updateToggleIcon();

    toggleBtn.addEventListener('click', () => {
        htmlEl.classList.toggle('dark');
        localStorage.setItem('theme', htmlEl.classList.contains('dark') ? 'dark' : 'light');
        updateToggleIcon();
    });

    // toggle sidebar width
    const sidebar = document.getElementById('sidebar');
    const sidebarToggleBtn = document.getElementById('toggleSidebar');
    const menuTextItems = document.querySelectorAll('.menu-text');
    const menuTitle = document.querySelector('.menu-title');

    // Load previous state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) collapseSidebar();

    sidebarToggleBtn.addEventListener('click', () => {
        if (sidebar.classList.contains('w-16')) {
            expandSidebar();
            localStorage.setItem('sidebarCollapsed', 'false');
        } else {
            collapseSidebar();
            localStorage.setItem('sidebarCollapsed', 'true');
        }
    });

    function collapseSidebar() {
        sidebar.classList.remove('w-1/3', 'xl:w-1/6', 'lg:w-1/4');
        sidebar.classList.add('w-16');
        menuTextItems.forEach(text => text.classList.add('hidden'));
        if (menuTitle) menuTitle.classList.add('hidden');

        // Show collapsed footer, hide expanded
        document.querySelector('.collapsed-footer')?.classList.remove('hidden');
        document.querySelector('.menu-text.text-xs')?.classList.add('hidden');
    }

    function expandSidebar() {
        sidebar.classList.remove('w-16');
        sidebar.classList.add('w-1/3', 'xl:w-1/6', 'lg:w-1/4');
        menuTextItems.forEach(text => text.classList.remove('hidden'));
        if (menuTitle) menuTitle.classList.remove('hidden');

        // Hide collapsed footer, show expanded
        document.querySelector('.collapsed-footer')?.classList.add('hidden');
        document.querySelector('.menu-text.text-xs')?.classList.remove('hidden');
    }

    // Topbar script
    // Get message to show on dropdown list
    function fetchMessages() {
        fetch('fetch_messages.php')
            .then(response => response.json())
            .then(data => {
                const messageList = document.getElementById('messageList');
                if (!messageList) return;

                messageList.innerHTML = '';

                if (data.length === 0) {
                    messageList.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">No messages</div>';
                    return;
                }

                data.forEach(msg => {
                    const msgItem = document.createElement('div');
                    msgItem.className = 'mx-4 px-4 py-2 border-b border-dashed last:border-0 hover:bg-gray-50 dark:border-gray-500 dark:hover:bg-gray-800';

                    msgItem.innerHTML = `
                        <div class="font-semibold text-sm">${msg.name}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">${msg.subject}</div>
                        <div class="text-xs text-gray-400">${formatTime(msg.sent_at)}</div>
                    `;

                    messageList.appendChild(msgItem);
                });
            });
    }

    // Format time like "Jul 03, 15:20"
    function formatTime(dateString) {
        const options = { month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleString('en-US', options);
    }

    // Fetch every 10 seconds
    setInterval(fetchMessages, 10000);

    // Fetch on page load
    fetchMessages();

    // Get unread message count
    function fetchUnreadCount() {
        fetch('fetch_unread.php')
            .then(response => response.text())
            .then(count => {
                document.getElementById('unread-badge').textContent = count;
            });
    }

    setInterval(fetchUnreadCount, 10000);
    fetchUnreadCount();

    // === Profile Image Dropdown ===
    const profileBtn = document.getElementById('profileImageDropdownBtn');
    const profileDropdown = document.getElementById('profileImageDropdown');

    // === Topbar Profile Dropdown ===
    const topbarBtn = document.getElementById('topbarProfileDropdownBtn');
    const topbarDropdown = document.getElementById('topbarProfileDropdown');

    // === Message Dropdown ===
    const messageBtn = document.getElementById('messageToggle');
    const messageDropdown = document.getElementById('messageDropdown');

    const fileInput = document.getElementById('profileImageInput');
    const preview = document.getElementById('profilePreview');
    const removeBtn = document.getElementById('removePhotoBtn');

    // Utility: close all dropdowns
    function closeAllDropdowns() {
        profileDropdown?.classList.add('hidden');
        topbarDropdown?.classList.add('hidden');
        messageDropdown?.classList.add('hidden');
    }

    // Profile image toggle
    profileBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        const wasHidden = profileDropdown.classList.contains('hidden');
        closeAllDropdowns();
        if (wasHidden) profileDropdown.classList.remove('hidden');
    });

    // Topbar profile toggle
    topbarBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        const wasHidden = topbarDropdown.classList.contains('hidden');
        closeAllDropdowns();
        if (wasHidden) topbarDropdown.classList.remove('hidden');
    });

    // Message toggle
    messageBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        const wasHidden = messageDropdown.classList.contains('hidden');
        closeAllDropdowns();
        if (wasHidden) messageDropdown.classList.remove('hidden');
    });

    // Outside click closes all
    document.addEventListener('click', (e) => {
        if (
            !profileDropdown?.contains(e.target) && !profileBtn?.contains(e.target) &&
            !topbarDropdown?.contains(e.target) && !topbarBtn?.contains(e.target) &&
            !messageDropdown?.contains(e.target) && !messageBtn?.contains(e.target)
        ) {
            closeAllDropdowns();
        }
    });

    // Live image preview
    fileInput?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
        }
    });

    // Remove profile image
    removeBtn?.addEventListener('click', () => {
        if (confirm("Are you sure you want to reset your current avatar?")) {
            preview.src = 'assets/uploads/default.png';
            fileInput.value = '';
            document.getElementById('removeProfilePhoto').value = '1'; // Mark for backend
            profileDropdown?.classList.add('hidden');
        }
    });
</script>

</body>

</html>
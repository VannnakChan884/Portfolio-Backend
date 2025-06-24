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
        }

        function expandSidebar() {
            sidebar.classList.remove('w-16');
            sidebar.classList.add('w-1/3', 'xl:w-1/6', 'lg:w-1/4');
            menuTextItems.forEach(text => text.classList.remove('hidden'));
            if (menuTitle) menuTitle.classList.remove('hidden');
        }
    </script>
</body>
</html>
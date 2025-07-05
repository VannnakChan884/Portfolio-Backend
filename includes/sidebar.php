<?php
if (!isset($userName)) $userName = 'Admin';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside id="sidebar" class="sticky top-0 h-screen bg-white dark:bg-gray-800 shadow-xl transition-all duration-300 ease-in-out overflow-hidden w-1/3 xl:w-1/6 lg:w-1/4">
    <!-- Sidebar content (nav + menu) -->
    <nav>
        <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 py-4 px-4">
            <h2 class="menu-title text-2xl font-bold capitalize">Portfolio - <?= htmlspecialchars($userName) ?></h2>
            <button id="toggleSidebar" class="flex items-center justify-center w-10 h-10 hover:bg-gray-300/50 p-2 text-xl rounded-lg" title="Toggle Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <ul class="mt-4">
            <li>
                <a href="dashboard.php"
                   class="menu-item flex items-center gap-2 py-1 px-4 hover:bg-gray-200 dark:hover:bg-gray-700 block <?= $currentPage === 'dashboard.php' ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' ?>">
                   <div class="flex items-center justify-center w-10 h-10"><i class="fas fa-tachometer-alt"></i> </div> 
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="home.php"
                   class="menu-item flex items-center gap-2 py-1 px-4 hover:bg-gray-200 dark:hover:bg-gray-700 block <?= $currentPage === 'home.php' ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' ?>">
                    <div class="flex items-center justify-center w-10 h-10"><i class="fas fa-home"></i> </div>
                    <span class="menu-text">Manage Home</span>
                </a>
            </li>
            <li>
                <a href="about.php"
                   class="menu-item flex items-center gap-2 py-1 px-4 hover:bg-gray-200 dark:hover:bg-gray-700 block <?= $currentPage === 'about.php' ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' ?>">
                    <div class="flex items-center justify-center w-10 h-10"><i class="fas fa-graduation-cap"></i> </div> 
                    <span class="menu-text">Manage About</span>
                </a>
            </li>
            <li>
                <a href="projects.php"
                   class="menu-item flex items-center gap-2 py-1 px-4 hover:bg-gray-200 dark:hover:bg-gray-700 block <?= $currentPage === 'projects.php' ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' ?>">
                    <div class="flex items-center justify-center w-10 h-10"><i class="fas fa-project-diagram"></i> </div>
                    <span class="menu-text">Manage Projects</span>
                </a>
            </li>
            <li>
                <a href="messages.php"
                   class="menu-item flex items-center gap-2 py-1 px-4 hover:bg-gray-200 dark:hover:bg-gray-700 block <?= $currentPage === 'messages.php' ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' ?>">
                    <div class="flex items-center justify-center w-10 h-10"><i class="fas fa-envelope"></i> </div>
                    <span class="menu-text">Manage Messages</span>
                </a>
            </li>
            <li>
                <a href="users.php"
                    class="menu-item flex items-center gap-2 py-1 px-4 hover:bg-gray-200 dark:hover:bg-gray-700 block <?= $currentPage === 'users.php' ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' ?>">
                    <div class="flex items-center justify-center w-10 h-10"><i class="fas fa-user"></i> </div>
                    <span class="menu-text">Manage Users</span>
                </a>
            </li>
            <li>
                <a href="settings.php"
                    class="menu-item flex items-center gap-2 py-1 px-4 hover:bg-gray-200 dark:hover:bg-gray-700 block <?= $currentPage === 'settings.php' ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' ?>">
                    <div class="flex items-center justify-center w-10 h-10"><i class="fa-solid fa-gear"></i> </div>
                    <span class="menu-text">Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar footer -->
    <footer class="py-3 border-t border-gray-200 dark:border-gray-700">
        <!-- Collapsed version (icon only) -->
        <span class="collapsed-footer hidden flex justify-center text-xs text-gray-500 dark:text-gray-400">© <?= date('Y') ?></span>

        <!-- Expanded version (full text) -->
        <span class="menu-text px-4 text-xs text-gray-500 dark:text-gray-400 capitalize">© <?= date('Y') ?> <?= htmlspecialchars($userName) ?>, Portfolio.</span>
    </footer>
</aside>

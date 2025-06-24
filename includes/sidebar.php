<?php
if (!isset($userName)) $userName = 'Admin';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside id="sidebar" class="sticky top-0 h-screen bg-white dark:bg-gray-800 shadow-xl transition-all duration-300 ease-in-out overflow-hidden w-1/3 xl:w-1/6 lg:w-1/4">
    <nav>
        <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 py-4 px-4">
            <h2 class="menu-title text-2xl font-bold capitalize">Portfolio - <?= htmlspecialchars($userName) ?></h2>
            <button id="toggleSidebar" class="flex items-center justify-center w-10 h-10 hover:bg-gray-300/50 p-2 text-xl hover:text-green-500 rounded-lg" title="Toggle Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <ul class="mt-4 space-y-1">
            <li>
                <a href="dashboard.php"
                   class="menu-item flex items-center gap-2 py-2 px-4 hover:bg-green-300 block <?= $currentPage === 'dashboard.php' ? 'bg-green-200 font-semibold' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="home.php"
                   class="menu-item flex items-center gap-2 py-2 px-4 hover:bg-green-300 block <?= $currentPage === 'home.php' ? 'bg-green-200 font-semibold' : '' ?>">
                    <i class="fas fa-home"></i> 
                    <span class="menu-text">Manage Home</span>
                </a>
            </li>
            <li>
                <a href="about.php"
                   class="menu-item flex items-center gap-2 py-2 px-4 hover:bg-green-300 block <?= $currentPage === 'about.php' ? 'bg-green-200 font-semibold' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> 
                    <span class="menu-text">Manage About</span>
                </a>
            </li>
            <li>
                <a href="skills.php"
                   class="menu-item flex items-center gap-2 py-2 px-4 hover:bg-green-300 block <?= $currentPage === 'skills.php' ? 'bg-green-200 font-semibold' : '' ?>">
                    <i class="fas fa-brain"></i> 
                    <span class="menu-text">Manage Skills</span>
                </a>
            </li>
            <li>
                <a href="projects.php"
                   class="menu-item flex items-center gap-2 py-2 px-4 hover:bg-green-300 block <?= $currentPage === 'projects.php' ? 'bg-green-200 font-semibold' : '' ?>">
                    <i class="fas fa-project-diagram"></i> 
                    <span class="menu-text">Manage Projects</span>
                </a>
            </li>
            <li>
                <a href="messages.php"
                   class="menu-item flex items-center gap-2 py-2 px-4 hover:bg-green-300 block <?= $currentPage === 'messages.php' ? 'bg-green-200 font-semibold' : '' ?>">
                    <i class="fas fa-envelope"></i> 
                    <span class="menu-text">Manage Messages</span>
                </a>
            </li>
            <li>
                <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                    <a href="users.php"
                    class="menu-item flex items-center gap-2 py-2 px-4 hover:bg-green-300 block <?= $currentPage === 'users.php' ? 'bg-green-200 font-semibold' : '' ?>">
                        <i class="fas fa-users"></i> 
                        <span class="menu-text">Manage Users</span>
                    </a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
</aside>

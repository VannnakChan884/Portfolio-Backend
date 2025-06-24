<header class="sticky top-0 flex justify-between items-center px-6 py-4 mb-4 rounded bg-white dark:bg-gray-800 shadow">
    <h1 class="text-2xl font-bold capitalize">Welcome - <?= htmlspecialchars($userName) ?> 👋</h1>
    <div class="flex gap-3 items-center">
        <button class="w-10 h-10 p-2 rounded-lg dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700">
            <i class="fa-solid fa-bell"></i>
        </button>
        <button id="dark-mode-toggle" aria-label="Toggle Dark Mode"
            class="w-10 h-10 p-2 rounded-lg dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700">
            🌙
        </button>
        <div class="relative group">
            <button class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center">
                <?php if ($userProfile): ?>
                    <img src="<?= htmlspecialchars($userProfile) ?>" alt="Profile" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="text-sm">N/A</span>
                <?php endif; ?>
            </button>
            <div class="absolute -bottom-10 right-0 w-40 bg-white dark:bg-gray-800 text-red-500 dark:text-gray-200 rounded shadow-lg z-10 opacity-0 group-hover:opacity-100 invisible group-hover:visible transition">
                <a href="logout.php" class="block px-6 py-2 hover:bg-red-400 hover:text-white rounded">
                    <i class="fa-solid fa-power-off"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</header>

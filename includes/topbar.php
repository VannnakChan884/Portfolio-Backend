<header class="sticky top-0 flex justify-between items-center px-6 py-4 mb-4 rounded bg-white dark:bg-gray-800 shadow">
    <h1 class="text-2xl font-bold capitalize">Welcome - <?= htmlspecialchars($siteTitle) ?> 👋</h1>

    <div class="flex gap-3 items-center">

        <!-- Envelope icon -->
        <div class="relative ml-4">
            <!-- Envelope Icon Button -->
            <button id="messageToggle"
                class="relative flex items-center justify-center w-10 h-10 p-2 rounded-lg dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                <i class="fas fa-envelope text-xl text-gray-700"></i>
                <span id="unread-badge"
                    class="absolute -top-1 -right-1 w-4 h-4 flex items-center justify-center bg-red-500 text-white text-[10px] font-bold animate-bounce p-1.5 rounded-full leading-none shadow">0</span>
            </button>

            <!-- Dropdown -->
            <div id="messageDropdown"
                class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-700 shadow-lg rounded-lg border dark:border-gray-500 hidden z-50">
                <div class="p-4 font-semibold border-b dark:border-gray-500">New Messages</div>
                <div id="messageList" class="max-h-64 overflow-y-auto">
                    <!-- JS will populate here -->
                </div>
                <a href="messages.php" class="block text-center py-2 text-blue-600 dark:text-gray-200 hover:underline border-t dark:border-gray-500">View
                    All</a>
            </div>
        </div>


        <!-- Dark Mode Toggle -->
        <button id="dark-mode-toggle" aria-label="Toggle Dark Mode"
            class="w-10 h-10 p-2 rounded-lg dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-700">
            🌙
        </button>

        <!-- Profile Dropdown -->
        <div class="relative group">
            <button id="topbarProfileDropdownBtn" class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center focus:outline-none">
                <?php if ($userProfile): ?>
                    <img src="<?= htmlspecialchars($userProfile) ?>" alt="Profile" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="text-sm text-white bg-gray-500 w-full h-full flex items-center justify-center">N/A</span>
                <?php endif; ?>
            </button>

            <!-- Dropdown Menu -->
            <div id="topbarProfileDropdown" class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg hidden z-50 border dark:border-gray-600">
                <!-- Profile Header -->
                <div class="flex items-center gap-3 p-4 dark:border-gray-600">
                    <img src="<?= htmlspecialchars($userProfile ?: 'assets/uploads/default.png') ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <div class="font-bold text-sm"><?= htmlspecialchars($siteTitle) ?></div>
                        <div class="text-xs text-gray-500">@<?= htmlspecialchars($username) ?></div>
                        <?php if (!empty($isDefaultAdmin)): ?>
                            <div class="text-xs text-red-500 font-semibold">Default Admin</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Menu Items -->
                <ul class="text-sm text-gray-700 dark:text-gray-200 px-4 py-2">
                    <li class="py-2 border-y">
                        <a href="profile.php" class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                            <i class="fas fa-user mr-2 w-4"></i> Your Profile
                        </a>
                    </li>
                    <li class="py-2 border-b">
                        <a href="settings.php" class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                            <i class="fas fa-cog mr-2 w-4"></i> Settings
                        </a>
                    </li>
                    <li class="py-2">
                        <a href="logout.php" class="flex items-center px-4 py-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-600 dark:hover:text-white rounded-lg">
                            <i class="fa-solid fa-right-from-bracket mr-2 w-4"></i> Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

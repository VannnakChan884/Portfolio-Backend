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
                    <?php if (count($messages) > 0): ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="px-4 py-2 border-b hover:bg-gray-50 dark:border-gray-500 dark:hover:bg-gray-800">
                                <div class="font-semibold text-sm"><?= htmlspecialchars($msg['name']) ?></div>
                                <div class="text-sm text-gray-600 dark:text-gray-300"><?= htmlspecialchars($msg['subject']) ?></div>
                                <div class="text-xs text-gray-400"><?= date('M d, H:i', strtotime($msg['sent_at'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="px-4 py-2 text-gray-500 text-sm">No messages</div>
                    <?php endif; ?>
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
            <button class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center">
                <?php if ($userProfile): ?>
                    <img src="<?= htmlspecialchars($userProfile) ?>" alt="Profile" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="text-sm">N/A</span>
                <?php endif; ?>
            </button>
            <div
                class="absolute -bottom-10 right-0 w-40 bg-white dark:bg-gray-800 text-red-500 dark:text-gray-200 rounded shadow-lg z-10 opacity-0 group-hover:opacity-100 invisible group-hover:visible transition">
                <a href="logout.php" class="block px-6 py-2 hover:bg-red-400 hover:text-white rounded">
                    <i class="fa-solid fa-power-off"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</header>
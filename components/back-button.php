<?php
    // Set defaults if not provided
    $link = $link ?? 'dashboard.php';
    $label = $label ?? 'Back';
?>
<a href="<?= htmlspecialchars($link) ?>" class="px-4 py-1 mt-4 hover:bg-gray-300/50 text-base text-gray-600 dark:text-gray-300 rounded inline-flex items-center transition">
    <i class="fas fa-arrow-left mr-1"></i>
    <span><?= htmlspecialchars($label); ?></span>
</a>

<?php
    // Set defaults if not provided
    $link = $link ?? 'dashboard.php';
    $label = $label ?? 'Back';
?>
<a href="<?= htmlspecialchars($link) ?>" class="px-4 py-2 bg-gray-300/50 hover:bg-gray-300 text-xl text-gray-600 rounded inline-flex items-center mb-4">
    <i class="fas fa-arrow-left mr-1"></i>
    <?= htmlspecialchars($label) ?>
</a>

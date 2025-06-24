<?php include 'includes/header.php'; ?>
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php  include 'includes/topbar.php'; ?>
        <div class="max-w-full mx-auto bg-white p-6 rounded shadow">
            <?php include 'components/back-button.php'; ?>

            <div class="my-6">
                <h2 class="text-2xl font-bold">Edit About Section</h2>
            </div>

            <div class="mt-6 text-sm">
                <p class="mb-2 font-medium">Switch Language:</p>
                <div class="space-x-2">
                    <a href="?lang=en" class="text-blue-600 hover:underline">English</a>
                    <a href="?lang=kh" class="text-blue-600 hover:underline">Khmer</a>
                    <a href="?lang=zh" class="text-blue-600 hover:underline">Chinese</a>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include 'includes/footer.php'; ?>
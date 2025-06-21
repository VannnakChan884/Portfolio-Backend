<?php include 'includes/header.php'; ?>
   <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <?php 
                include 'includes/topbar.php'; 
                include 'includes/contents.php';
            ?>

        </main>
    </div>
<?php include 'includes/footer.php'; ?>
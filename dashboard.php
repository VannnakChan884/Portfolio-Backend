<?php include 'includes/header.php'; ?>
<?php
    // Fetch counts
    $totalSkills = $conn->query("SELECT COUNT(*) as total FROM skills")->fetch_assoc()['total'];
    $totalProjects = $conn->query("SELECT COUNT(*) as total FROM projects")->fetch_assoc()['total'];
    $totalMessages = $conn->query("SELECT COUNT(*) as total FROM messages")->fetch_assoc()['total'];
    $totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

    // Skill chart data (default: English)
    $lang = 'en';
    $stmt = $conn->prepare("SELECT title, percentage FROM skills WHERE lang = ? ORDER BY id ASC");
    $stmt->bind_param("s", $lang);
    $stmt->execute();
    $result = $stmt->get_result();

    $skillLabels = [];
    $skillPercentages = [];

    while ($row = $result->fetch_assoc()) {
        $skillLabels[] = $row['title'];
        $skillPercentages[] = (int)$row['percentage'];
    }
?>
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
    
    <!-- Chart + Dark Mode Script -->
    <script>
        // Skill chart
        const ctx = document.getElementById('skillChart').getContext('2d');
        const skillLabels = <?= json_encode($skillLabels) ?>;
        const skillData = <?= json_encode($skillPercentages) ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: skillLabels,
                datasets: [{
                    label: 'Skill Proficiency (%)',
                    data: skillData,
                    backgroundColor: 'rgba(37, 99, 235, 0.7)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                }
            }
        });
    </script>
<?php include 'includes/footer.php'; ?>
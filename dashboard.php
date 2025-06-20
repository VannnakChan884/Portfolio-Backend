<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: auth/login.php");
    exit();
}
require_once 'includes/db.php';

// Fetch counts
$totalSkills = $conn->query("SELECT COUNT(*) as total FROM skills")->fetch_assoc()['total'];
$totalProjects = $conn->query("SELECT COUNT(*) as total FROM projects")->fetch_assoc()['total'];
$totalMessages = $conn->query("SELECT COUNT(*) as total FROM messages")->fetch_assoc()['total'];

// Fetch skill data for chart (default lang = 'en')
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
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="relative w-1/3 xl:w-1/6 lg:w-1/4 h-screen bg-white dark:bg-gray-800 shadow-xl">
            <nav>
                <div class="bg-gray-100 dark:bg-gray-700 p-3 text-center">
                    <h2 class="text-md font-bold uppercase">Portfolio Admin</h2>
                </div>
                <ul>
                    <li><a href="about.php" class="menu-item py-2 px-4 hover:bg-green-300 block rounded-xl">Manage About</a></li>
                    <li><a href="skills.php" class="menu-item py-2 px-4 hover:bg-green-300 block rounded-xl">Manage Skills</a></li>
                    <li><a href="projects.php" class="menu-item py-2 px-4 hover:bg-green-300 block rounded-xl">Manage Projects</a></li>
                    <li><a href="messages.php" class="menu-item py-2 px-4 hover:bg-green-300 block rounded-xl">View Messages</a></li>
                    <li><a href="users.php" class="menu-item py-2 px-4 hover:bg-green-300 block rounded-xl">View Users</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <header class="flex justify-between items-center p-4">
                <h1 class="text-2xl font-bold">Dashboard</h1>
                <div class="flex gap-4 items-center">
                    <!-- Dark mode toggle -->
                    <button id="dark-mode-toggle" aria-label="Toggle Dark Mode"
                        class="p-2 rounded border border-gray-400 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">
                        🌙
                    </button>
                    <a href="logout.php"
                       class="w-8 h-8 flex items-center justify-center border-2 border-red-400 hover:border-white bg-white hover:bg-red-400 text-red-500 hover:text-white p-1 rounded-full"
                       title="Logout">
                        <i class="fa-solid fa-power-off"></i>
                    </a>
                </div>
            </header>

            <section>
                <h3 class="text-xl font-semibold mb-4">Overview</h3>
                <ul class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">
                    <li class="bg-white dark:bg-gray-800 shadow-md p-4 rounded-xl">
                        <h2 class="text-lg font-semibold">Total Skills</h2>
                        <p class="text-3xl font-bold mt-2"><?= $totalSkills ?></p>
                    </li>
                    <li class="bg-white dark:bg-gray-800 shadow-md p-4 rounded-xl">
                        <h2 class="text-lg font-semibold">Total Projects</h2>
                        <p class="text-3xl font-bold mt-2"><?= $totalProjects ?></p>
                    </li>
                    <li class="bg-white dark:bg-gray-800 shadow-md p-4 rounded-xl">
                        <h2 class="text-lg font-semibold">Total Messages</h2>
                        <p class="text-3xl font-bold mt-2"><?= $totalMessages ?></p>
                    </li>
                </ul>

                <!-- Skill Chart -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md max-w-4xl mx-auto">
                    <h3 class="text-xl font-semibold mb-4">Skills Chart (<?= strtoupper($lang) ?>)</h3>
                    <canvas id="skillChart" class="w-full h-64"></canvas>
                </div>
            </section>
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

        // Dark mode toggle
        const toggleBtn = document.getElementById('dark-mode-toggle');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlEl.classList.add('dark');
        }

        function updateToggleIcon() {
            toggleBtn.textContent = htmlEl.classList.contains('dark') ? '☀️' : '🌙';
        }

        updateToggleIcon();

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('theme', htmlEl.classList.contains('dark') ? 'dark' : 'light');
            updateToggleIcon();
        });
    </script>
</body>
</html>

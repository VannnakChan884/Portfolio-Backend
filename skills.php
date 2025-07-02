<?php include 'includes/header.php'; ?>
<?php
    $lang = $_GET['lang'] ?? 'en';
    $search = $_GET['search'] ?? '';

    // Pagination variables
    $itemsPerPage = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $itemsPerPage;

    // Handle Create
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $home_id = intval($_POST['home_id']);
        $title = trim($_POST['name']);
        $percentage = intval($_POST['level']);

        if(!empty($_POST['skill_id'])){
            // Update skill
            $skill_id = intval($_POST['skill_id']);
            $stmt = $conn->prepare("UPDATE skills SET name = ?, level = ?, updated_at=NOW() WHERE id = ? AND home_id = ?");
            $stmt->bind_param("siii", $title, $percentage, $skill_id, $home_id);
            $success = $stmt->execute();
            if ($success) {
                $_SESSION['success'] = "Skill updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update skill.";
            }
        } else {
            // Add new skill
            $stmt = $conn->prepare("INSERT INTO skills (home_id, name, level, lang) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis",$home_id, $title, $percentage, $lang);
            $success = $stmt->execute();
            
            if ($success) {
                $_SESSION['success'] = "Skill added successfully!";
            } else {
                $_SESSION['error'] = "Failed to add skill.";
            }
        }
        header("Location: skills.php?home_id=$home_id");
        exit;
    }

    // Handle Delete skill
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        if ($conn->query("DELETE FROM skills WHERE id = $id")) {
            $_SESSION['success'] = "Skill deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete skill.";
        }
        header("Location: skills.php?home_id=$home_id");
        exit;
    }

    $home_id = isset($_GET['home_id']) ? intval($_GET['home_id']) : 0;
    $home = $conn->query("SELECT * FROM home WHERE id = $home_id")->fetch_assoc();

    $skills = $conn->query("SELECT * FROM skills WHERE home_id = $home_id ORDER BY id DESC");

    // Load skill data for editing
    $editSkill = null;
    if (isset($_GET['edit'])) {
        $edit_id = intval($_GET['edit']);
        $stmt = $conn->prepare("SELECT * FROM skills WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $editSkill = $result->fetch_assoc();
        }
    }

    // Count total skills for pagination
    if ($search) {
        $searchParam = "%$search%";
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM skills WHERE lang = ? AND name LIKE ?");
        $countStmt->bind_param("ss", $lang, $searchParam);
    } else {
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM skills WHERE lang = ?");
        $countStmt->bind_param("s", $lang);
    }
    $countStmt->execute();
    $countStmt->bind_result($totalSkills);
    $countStmt->fetch();
    $countStmt->close();

    $totalPages = ceil($totalSkills / $itemsPerPage);

    // Fetch paginated skills
    if ($search) {
        $stmt = $conn->prepare("SELECT * FROM skills WHERE lang = ? AND name LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ssii", $lang, $searchParam, $itemsPerPage, $offset);
    } else {
        $stmt = $conn->prepare("SELECT * FROM skills WHERE lang = ? ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("sii", $lang, $itemsPerPage, $offset);
    }
    $stmt->execute();
    $skills = $stmt->get_result();
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <?php  include 'includes/topbar.php'; ?>
        <div class="max-w-full mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow">
        
            <!-- Skill Form -->
            <form method="post" class="grid grid-cols-1 gap-4 mb-6 max-w-3xl">
                <h2 class="text-2xl font-bold">Manage Skills for:
                    <span class="text-blue-600"><?= htmlspecialchars($home['name']) ?></span>
                    (<?= strtoupper(htmlspecialchars($lang)) ?>)
                </h2>
                <!-- Notifications -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-500 text-white p-3 rounded mb-4">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                <?php unset($_SESSION['success']); endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-500 text-white p-3 rounded mb-4">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                <?php unset($_SESSION['error']); endif; ?>

                <input type="hidden" name="home_id" value="<?= htmlspecialchars((string)$home_id) ?>">
                <input type="hidden" name="lang" value="<?= htmlspecialchars($lang) ?>">
                <?php if ($editSkill): ?>
                    <input type="hidden" name="skill_id" value="<?= $editSkill['id'] ?>">
                <?php endif; ?>

                <label class="flex flex-col">
                    <span class="text-gray-700 dark:text-gray-300 after:ml-0.5 after:text-red-500 after:content-['*']">Skill</span>
                    <input class="dark:bg-gray-700 border dark:border-gray-600 p-2 rounded" type="text" name="name" placeholder="Skill Title" required value="<?= htmlspecialchars($editSkill['name'] ?? '') ?>">
                </label>
                <label class="flex flex-col">
                    <span class="text-gray-700 dark:text-gray-300 after:ml-0.5 after:text-red-500 after:content-['*']">Level</span>
                    <input class="dark:bg-gray-700 border dark:border-gray-600 p-2 rounded" type="number" name="level" placeholder="0-100%" min="1" max="100" required value="<?= htmlspecialchars($editSkill['level'] ?? '') ?>">
                </label>
                
                <button name="<?= $editSkill ? 'update_skill' : 'add_skill' ?>" type="submit"
                        class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                    <?= $editSkill ? 'Update Skill' : 'Add Skill' ?>
                </button>
            </form>

            <!-- Search Bar -->
            <form method="get" class="mb-4 flex flex-col sm:flex-row sm:items-center">
                <input type="hidden" name="lang" value="<?= htmlspecialchars($lang) ?>">
                <input class="dark:bg-gray-700 border dark:border-gray-600 p-2 rounded-l w-full max-w-sm mb-2 sm:mb-0" type="text" name="search" placeholder="Search skills..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="bg-gray-200/50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 py-2 px-4 rounded-r border-y border-r dark:border-gray-600 hover:bg-gray-200/70 dark:hover:bg-gray-600">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <!-- Skills Table -->
            <table class="w-full border border-collape">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-2 border dark:border-gray-600">Title</th>
                    <th class="p-2 border dark:border-gray-600">Percentage</th>
                    <th class="p-2 border dark:border-gray-600">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $skills->fetch_assoc()): ?>
                    <tr class="text-center border-b <?= $editSkill && $editSkill['id'] == $row['id'] ? 'bg-yellow-100' : '' ?>">
                        <td class="p-2 border dark:border-gray-600"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="p-2 border dark:border-gray-600">
                            <?php
                                $percent = intval($row['level']);
                                $colorClass = 'bg-blue-500';
                                if ($percent >= 80) {
                                    $colorClass = 'bg-green-500';
                                } elseif ($percent >= 50) {
                                    $colorClass = 'bg-yellow-400';
                                } elseif ($percent >= 30) {
                                    $colorClass = 'bg-orange-400';
                                } else {
                                    $colorClass = 'bg-red-500';
                                }

                                $textInside = $percent >= 20;
                            ?>

                            <div class="relative w-full bg-gray-200 rounded-full h-5 overflow-hidden">
                                <div
                                    class="progress-bar h-5 text-white text-xs flex items-center <?= $textInside ? 'justify-center' : '' ?> <?= $colorClass ?> rounded-full"
                                    style="width: 0%"
                                    data-percent="<?= $percent ?>">
                                    <?= $textInside ? $percent . '%' : '' ?>
                                </div>
                                <?php if (!$textInside): ?>
                                    <div class="absolute left-[calc(<?= $percent ?>%+4px)] top-0 h-5 flex items-center text-xs text-gray-700">
                                        <?= $percent ?>%
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="flex p-2 border dark:border-gray-600">
                            <a href="skills.php?edit=<?= $row['id'] ?>&home_id=<?= $home_id ?>" class="inline-block text-sm px-2 py-1 mr-2 rounded bg-orange-100 text-orange-600">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="?delete=<?= $row['id'] ?>&home_id=<?= $home_id ?>" onclick="return confirm('Are you sure? You want to delete this skill?')" class="inline-block text-sm px-2 py-1 rounded bg-red-100 text-red-600">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-4 flex justify-center space-x-2">
                <?php if ($page > 1): ?>
                    <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>"
                    class="px-3 py-1 border rounded hover:bg-gray-200">&laquo; Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>"
                    class="px-3 py-1 border rounded <?= $i === $page ? 'bg-blue-600 text-white' : 'hover:bg-gray-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?lang=<?= $lang ?>&search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>"
                    class="px-3 py-1 border rounded hover:bg-gray-200">Next &raquo;</a>
                <?php endif; ?>
            </div>
            
            <div class="mt-6">
                <?php 
                    $link = 'home.php';
                    include "components/back-button.php"; 
                ?>
            </div>
        </div>
    </main>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const bars = document.querySelectorAll('.progress-bar');
        bars.forEach(bar => {
            const percent = bar.dataset.percent;
            setTimeout(() => {
                bar.style.transition = 'width 1s ease-out';
                bar.style.width = percent + '%';
            }, 100);
        });
    });
</script>
<?php include 'includes/footer.php'; ?>
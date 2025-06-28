<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

// Input defaults
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = intval($_GET['per_page'] ?? 10);

// Sorting validation
$allowedSortFields = ['username', 'email', 'full_name', 'role', 'created_at', 'updated_at'];
$sort = in_array($_GET['sort'] ?? '', $allowedSortFields) ? $_GET['sort'] : 'created_at';
$order = ($_GET['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

// Filtering
$searchTerm = "%$search%";

// Count total
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM users WHERE username LIKE ? OR email LIKE ? OR full_name LIKE ?");
$stmtCount->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmtCount->execute();
$stmtCount->bind_result($totalRows);
$stmtCount->fetch();
$stmtCount->close();

$totalPages = ceil($totalRows / $perPage);
$offset = ($page - 1) * $perPage;

// Fetch paginated data
$sql = "SELECT id, username, email, full_name, user_profile, role, created_at, updated_at 
        FROM users 
        WHERE username LIKE ? OR email LIKE ? OR full_name LIKE ?
        ORDER BY $sort $order 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $offset, $perPage);
$stmt->execute();
$result = $stmt->get_result();

$rowsHtml = '';
while ($user = $result->fetch_assoc()) {
    $profile = htmlspecialchars($user['user_profile'] ?: 'assets/uploads/default.png');
    $rowsHtml .= '<tr>';
    $rowsHtml .= '<td class="p-2 border dark:border-gray-600">' . htmlspecialchars($user['username']) . '</td>';
    $rowsHtml .= '<td class="p-2 border dark:border-gray-600">' . htmlspecialchars($user['email']) . '</td>';
    $rowsHtml .= '<td class="p-2 border dark:border-gray-600">' . htmlspecialchars($user['full_name']) . '</td>';
    $rowsHtml .= '<td class="p-2 border dark:border-gray-600 text-center"><img src="' . $profile . '" class="w-10 h-10 rounded-full mx-auto object-cover"></td>';
    $rowsHtml .= '<td class="p-2 border dark:border-gray-600 text-center">';
    if (empty($user['role'])) {
        $rowsHtml .= '
            <form method="POST" class="flex items-center gap-2">
                <input type="hidden" name="user_id" value="' . $user['id'] . '">
                <select name="assign_role" class="border p-1 rounded">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit" name="assign_role_submit"
                    class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Assign</button>
            </form>';
    } else {
        $rowsHtml .= '<span class="text-sm px-2 py-1 rounded bg-blue-100 text-blue-600">' . htmlspecialchars($user['role']) . '</span>';
    }
    $rowsHtml .= '</td>';
    $rowsHtml .= '<td class="p-2 border dark:border-gray-600">' . $user['created_at'] . '</td>';
    $rowsHtml .= '<td class="p-2 border dark:border-gray-600">' . ($user['updated_at'] ?? '—') . '</td>';
    $rowsHtml .= '<td class="p-2 border dark:border-gray-600 text-center">
        <a href="users.php?edit=' . $user['id'] .
        '&username=' . urlencode(trim($user['username'])) .
        '&email=' . urlencode(trim($user['email'])) .
        '&full_name=' . urlencode(trim($user['full_name'])) .
        '&user_profile=' . urlencode(trim($user['user_profile'] ?? '')) .
        '&role=' . urlencode(trim($user['role'] ?? '')) . '" 
        class="inline-block text-sm px-2 py-1 mr-2 rounded bg-orange-100 text-orange-600">
        <i class="fa-solid fa-user-pen"></i>
        </a>
        <button data-delete-id="' . $user['id'] . '" class="text-sm px-2 py-1 rounded bg-red-100 text-red-600">
            <i class="fa-solid fa-trash"></i>
        </button>
    </td>';
    $rowsHtml .= '</tr>';
}
$stmt->close();

// Build pagination links
$paginationHtml = '';
if ($totalPages > 1) {
    if ($page > 1) {
        $paginationHtml .= '<button class="px-3 py-1 border rounded" data-page="' . ($page - 1) . '">&laquo; Prev</button>';
    }

    for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = $i === $page ? 'bg-blue-600 text-white' : 'hover:bg-gray-200';
        $paginationHtml .= '<button class="px-3 py-1 border rounded ' . $activeClass . '" data-page="' . $i . '">' . $i . '</button>';
    }

    if ($page < $totalPages) {
        $paginationHtml .= '<button class="px-3 py-1 border rounded" data-page="' . ($page + 1) . '">Next &raquo;</button>';
    }
}

// Return JSON
echo json_encode([
    'rows' => $rowsHtml ?: '<tr><td colspan="8" class="text-center p-4">No users found.</td></tr>',
    'pagination' => $paginationHtml
]);

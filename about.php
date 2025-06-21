<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage About - <?= strtoupper($lang) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-6">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex flex-row gap-x-8 items-center mb-4">
            <?php
                $link = 'dashboard.php';
                $label = 'Back to Dashboard';
                include 'components/back-button.php';
            ?>
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
</body>
</html>
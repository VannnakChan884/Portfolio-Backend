<section>
    <h3 class="text-xl font-semibold my-4">Overview</h3>
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
        <li class="bg-white dark:bg-gray-800 shadow-md p-4 rounded-xl">
            <h2 class="text-lg font-semibold">Total Users</h2>
            <p class="text-3xl font-bold mt-2"><?= $totalUsers ?></p>
        </li>
    </ul>

    <!-- Skill Chart -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md max-w-4xl">
        <h3 class="text-xl font-semibold mb-4">Skills Chart (<?= strtoupper($lang) ?>)</h3>
        <canvas id="skillChart" class="w-full h-64"></canvas>
    </div>
</section>
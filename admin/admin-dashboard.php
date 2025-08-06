<?php
require_once '../admin/admin-dashboard-handler.php';
require_once '../functions/auth.php';
requireAdmin();

// Pagination settings
$itemsPerPage = 10; // Change this number as needed
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Count total logs
$totalStmt = $pdo->query("SELECT COUNT(*) FROM Logs");
$totalLogs = $totalStmt->fetchColumn();
$totalPages = ceil($totalLogs / $itemsPerPage);

// Fetch paginated logs
$logQuery = "
    SELECT 
        l.Description,
        l.Timestamp,
        CASE
            WHEN l.Table_Affected = 'Admin' THEN a.admin_name
            WHEN l.Table_Affected = 'Veterinarian' THEN v.vet_name
            ELSE 'Unknown'
        END AS name
    FROM Logs l
    LEFT JOIN Admin a ON l.Table_Affected = 'Admin' AND l.User_ID = a.admin_id
    LEFT JOIN Veterinarian v ON l.Table_Affected = 'Veterinarian' AND l.User_ID = v.vet_id
    ORDER BY l.Timestamp DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($logQuery);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="../Assets/chart.js"></script>
    <link rel="stylesheet" href="../Assets/FontAwsome/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        @media (min-width: 768px) {
            .chart-container {
                height: 400px;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-green-600 text-white p-3 rounded-md shadow-lg">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 w-50 bg-gradient-to-b from-green-500 to-green-600 text-white p-4 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl lg:text-2xl lg:mt-3 font-semibold mb-6 flex items-center gap-2 lg:mt-0">
                <img src="../image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-white hover:text-gray-300 duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="mt-8 lg:mt-36">
            <a href="admin-dashboard.php" class="block text-md lg:text-md text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Dashboard
            </a>
            <a href="admin.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-user-md mr-2"></i> Veterinarians
            </a>
            <a href="records.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fa-solid fa-file-lines mr-2"> </i> Records
            </a>
            <a href="../index.php" onclick="confirmLogout(event)" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Dashboard Container -->
    <div class="ml-0 lg:ml-48 p-4 pt-16 lg:pt-4">
        <!-- Header with Welcome and Metrics -->
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-8 p-4 lg:p-8">
            <!-- Top Header Row -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-green-800">Hello, admin.</h2>
                <h2 class="text-2xl font-bold text-green-800">Dashboard</h2>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Clients Card -->
                <div class="bg-green-100 p-4 rounded-md h-full relative">
                    <a href="clients.php" class="absolute top-1 right-2 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-user mr-2 text-xl"></i> Clients</h3>
                        <p class="text-xl"><?= $clientCount ?></p>
                    </div>
                </div>

                <!-- Vets Card -->
                <div class="bg-green-100 p-6 rounded-md h-full relative">
                    <a href="vets.php" class="absolute top-2 right-2 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-xl mb-1">
                            <i class="fas fa-user-md mr-2 text-2xl"></i> Veterinarian
                        </h3>
                        <p class="text-xl"><?= $vetCount ?></p>
                    </div>
                </div>

                <!-- Pets Card -->
                <div class="bg-green-100 p-4 rounded-md relative">
                    <a href="pets.php" class="absolute top-1 right-2 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-paw mr-2 text-xl"></i> Pets</h3>
                        <p class="text-xl"><?= $petCount ?></p>
                    </div>
                </div>

                <!-- Medical Records Card -->
                <div class="bg-green-100 p-4 rounded-md relative">
                    <a href="medical_records.php" class="absolute top-1 right-2 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-file-medical mr-2 text-xl"></i> Medical Records</h3>
                        <p class="text-xl"><?= $recordCount ?></p>
                    </div>
                </div>

                <!-- Total Payments Card -->
                <div class="bg-green-100 p-5 rounded-md relative">
                    <a href="payment_methods.php" class="absolute top-1 right-2 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fa-solid fa-money-bill-wave mr-2 text-xl"></i> Total Payments</h3>
                        <p class="text-xl">₱<?= number_format($totalPayment, 2) ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Graph Section -->
        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-sm">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-green-800 mb-6">Analytics Overview</h2>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Monthly Income Box -->
                <div class="flex-1 bg-gray-50 border border-green-200 rounded-lg p-4 shadow-sm">
                    <h3 class="text-base lg:text-lg font-semibold text-green-700 mb-4">Monthly Income</h3>
                    <div class="chart-container">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>

                <!-- Most Common Medical Conditions Box -->
                <div class="flex-0.5 bg-gray-50 border border-green-200 rounded-lg p-4 shadow-sm">
                    <h3 class="text-base lg:text-lg font-semibold text-green-700 mb-4">Most Common Medical Conditions</h3>
                    <div class="chart-container">
                        <canvas id="conditionChart"></canvas>
                    </div>
                </div>
            </div>
        </main>

        <!-- Recent Activities Section -->
        <div class="bg-white p-4 lg:p-6 rounded-lg shadow-sm mt-8">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-green-800 mb-6">Recent Activities</h2>

            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-5">
                        <tr class="border-b bg-gray-200">
                            <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Name</th>
                            <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Description</th>
                            <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($logs as $index => $log): ?>
                            <?php $serial = ($offset + $index + 1); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm"><?= $serial ?></td>
                                <td class="px-4 py-2 text-sm"><?= htmlspecialchars($log['name'] ?? 'Unknown') ?></td>
                                <td class="px-4 py-2 text-sm"><?= htmlspecialchars($log['Description'] ?? '') ?></td>
                                <td class="px-4 py-2 text-sm"><?= htmlspecialchars($log['Timestamp'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-sm text-center text-gray-500">No recent activities logged.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="mt-4 flex justify-center space-x-2">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?>" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">« Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="px-3 py-1 <?= $i === $currentPage ? 'bg-green-700 text-white' : 'bg-green-100 text-green-800' ?> rounded hover:bg-green-600 hover:text-white"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">Next »</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script>
        // Monthly Income Bar Chart
        const monthlyLabels = <?= json_encode($monthlyLabels) ?>;
        const monthlyTotals = <?= json_encode($monthlyTotals) ?>;

        const incomeCtx = document.getElementById('incomeChart').getContext('2d');
        const incomeChart = new Chart(incomeCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Total Income (₱)',
                    data: monthlyTotals,
                    backgroundColor: '#4CAF50',
                    borderColor: '#388E3C',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (₱)'
                        }
                    }
                }
            }
        });

        // Most Common Medical Conditions Pie Chart
        const conditionLabels = <?= json_encode($conditionLabels) ?>;
        const conditionCounts = <?= json_encode($conditionCounts) ?>;

        const conditionCtx = document.getElementById('conditionChart').getContext('2d');
        const conditionChart = new Chart(conditionCtx, {
            type: 'pie',
            data: {
                labels: conditionLabels,
                datasets: [{
                    data: conditionCounts,
                    backgroundColor: [
                        '#43A047', '#66BB6A', '#26A69A', '#FFCA28', '#EF5350'
                    ],
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
    <script src="../js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/Pet_Track_revise-2/js/confirmLogout.js"></script>
    <script src="../js/confirmLogout.js"></script>
</body>

</html>
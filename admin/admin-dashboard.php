<?php
require_once '../admin/admin-dashboard-handler.php';
require_once '../functions/auth.php';
include "../includes/sitemap/Help/support.php";
requireAdmin();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="../image/MainIcon.png" type="image/x-icon">
    <script src="../Assets/chart.js"></script>
    <link rel="stylesheet" href="../Assets/FontAwsome/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .chart-container {
            position: relative;
            height: 180px;
            width: 100%;
        }

        @media (min-width: 640px) {
            .chart-container {
                height: 220px;
            }
        }

        @media (min-width: 768px) {
            .chart-container {
                height: 280px;
            }
        }

        @media (min-width: 1024px) {
            .chart-container {
                height: 320px;
            }
        }

        /* Table responsiveness */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 400px;
        }

        /* Dropdown menu for mobile */
        #dropdownMenu {
            width: 80vw;
            max-width: 260px;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-green-600 text-white p-3 rounded-md shadow-lg">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar (unchanged) -->
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
            <a href="#" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors" onclick="toggleModal('adminHelpModal')">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
            </a>
        </nav>

        <!-- Logout -->
        <a href="../index.php" onclick="confirmLogout(event)" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
            <i class="fas fa-sign-out-alt mr-2"></i>
            <span class="md:inline">Logout</span>
        </a>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Dashboard Container -->
    <div class="ml-0 lg:ml-48 p-3 pt-14 lg:pt-3">
        <!-- Header with Welcome and Metrics -->
        <header class="bg-white rounded-lg text-green-800 py-3 shadow-sm mb-4 p-3">
            <!-- Top Section with Dropdown -->
            <div class="flex justify-between items-center mb-3">
                <h1 class="text-base sm:text-lg lg:text-xl font-bold">Dashboard</h1>

                <!-- Profile Dropdown -->
                <div class="relative inline-block text-left">
                    <button id="profileButton" class="flex items-center justify-center w-9 h-9 bg-slate-700 border border-slate-600 rounded-full hover:bg-slate-600 text-white text-base transition-colors">
                        <i class="fas fa-user"></i>
                    </button>

                    <div id="dropdownMenu"
                        class="origin-top-right absolute right-0 mt-2 rounded-lg shadow-lg bg-slate-800 ring-1 ring-black ring-opacity-5 opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out z-50 border border-slate-700">
                        <div class="px-3 py-2 border-b border-slate-700">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-indigo-500 bg-slate-700 text-indigo-400 text-lg">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-white"><?= $vetName ?></p>
                                    <p class="text-xs text-slate-400">Veterinarian</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="profile.php" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-300 hover:bg-slate-700 hover:text-white transition-colors duration-150">
                                <i class="fas fa-edit text-indigo-400"></i>
                                <div>
                                    <div class="font-medium">Edit Profile</div>
                                    <div class="text-xs text-slate-400">Update your information</div>
                                </div>
                            </a>
                            <hr class="my-1 border-slate-700">
                            <a href="#" onclick="confirmLogout(event)" class="flex items-center gap-2 px-3 py-2 text-xs text-red-400 hover:bg-slate-700 transition-colors duration-150">
                                <i class="fas fa-sign-out-alt text-red-400"></i>
                                <div>
                                    <div class="font-medium">Logout</div>
                                    <div class="text-xs text-red-500">Sign out of your account</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Clients Card -->
                <div class="bg-green-100 p-3 rounded-md relative">
                    <a href="clients.php" class="absolute top-1 right-1 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-base sm:text-lg mb-1"><i class="fas fa-user mr-1 text-base"></i> Clients</h3>
                        <p class="text-base sm:text-lg"><?= $clientCount ?></p>
                    </div>
                </div>

                <!-- Vets Card -->
                <div class="bg-green-100 p-3 rounded-md relative">
                    <a href="admin.php" class="absolute top-1 right-1 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-base sm:text-lg mb-1"><i class="fas fa-user-md mr-1 text-base"></i> Veterinarian</h3>
                        <p class="text-base sm:text-lg"><?= $vetCount ?></p>
                    </div>
                </div>

                <!-- Pets Card -->
                <div class="bg-green-100 p-3 rounded-md relative">
                    <a href="pets.php" class="absolute top-1 right-1 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-base sm:text-lg mb-1"><i class="fas fa-paw mr-1 text-base"></i> Pets</h3>
                        <p class="text-base sm:text-lg"><?= $petCount ?></p>
                    </div>
                </div>

                <!-- Medical Records Card -->
                <div class="bg-green-100 p-3 rounded-md relative">
                    <a href="medical_records.php" class="absolute top-1 right-1 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-base sm:text-lg mb-1"><i class="fas fa-file-medical mr-1 text-base"></i> Medical Records</h3>
                        <p class="text-base sm:text-lg"><?= $recordCount ?></p>
                    </div>
                </div>

                <!-- Total Payments Card -->
                <div class="bg-green-100 p-3 rounded-md relative">
                    <a href="payment_methods.php" class="absolute top-1 right-1 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-base sm:text-lg mb-1"><i class="fa-solid fa-money-bill-wave mr-1 text-base"></i> Total Payments</h3>
                        <p class="text-base sm:text-lg">₱<?= number_format($totalPayment, 2) ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Graph Section -->
        <main class="bg-white p-3 sm:p-4 rounded-lg shadow-sm">
            <h2 class="text-base sm:text-lg font-semibold text-green-800 mb-3">Analytics Overview</h2>

            <div class="flex flex-col gap-4">
                <!-- Monthly Income Box -->
                <div class="bg-gray-50 border border-green-200 rounded-lg p-3 shadow-sm">
                    <h3 class="text-sm sm:text-base font-semibold text-green-700 mb-2">Monthly Income</h3>
                    <div class="chart-container">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>

                <!-- Most Common Medical Conditions Box -->
                <div class="bg-gray-50 border border-green-200 rounded-lg p-3 shadow-sm">
                    <h3 class="text-sm sm:text-base font-semibold text-green-700 mb-2">Most Common Medical Conditions</h3>
                    <div class="chart-container">
                        <canvas id="conditionChart"></canvas>
                    </div>
                </div>
            </div>
        </main>

        <!-- Recent Activities Section -->
        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm mt-4">
            <h2 class="text-base sm:text-lg font-semibold text-green-800 mb-3">Recent Activities</h2>

            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-5">
                        <tr class="border-b bg-gray-200">
                            <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[90px]">Name</th>
                            <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[110px]">Description</th>
                            <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[90px]">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($logs as $index => $log): ?>
                            <?php $serial = ($offset + $index + 1); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-1 text-xs"><?= $serial ?></td>
                                <td class="px-2 py-1 text-xs"><?= htmlspecialchars($log['name'] ?? 'Guest') ?></td>
                                <td class="px-2 py-1 text-xs"><?= htmlspecialchars($log['Description'] ?? '') ?></td>
                                <td class="px-2 py-1 text-xs"><?= htmlspecialchars($log['Timestamp'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="4" class="px-2 py-1 text-xs text-center text-gray-500">No recent activities logged.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="mt-3 flex justify-center space-x-1">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?>" class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">« Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="px-2 py-1 text-xs <?= $i === $currentPage ? 'bg-green-700 text-white' : 'bg-green-100 text-green-800' ?> rounded hover:bg-green-600 hover:text-white"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>" class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">Next »</a>
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
                        display: true,
                        labels: {
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (₱)',
                            font: {
                                size: 11
                            }
                        },
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 9
                            }
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
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 9
                            }
                        }
                    }
                }
            }
        });

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle('hidden');
            } else {
                console.error("Modal not found:", modalId);
            }
        }
    </script>
    <script src="../js/sidebarHandler.js"></script>
    <script src="../js/profile-dropdown.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/Pet_Track_revise-2/js/confirmLogout.js"></script>
    <script src="../js/confirmLogout.js"></script>
</body>

</html>
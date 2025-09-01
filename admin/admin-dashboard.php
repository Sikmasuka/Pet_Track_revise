<?php
require_once '../functions/auth.php';
require_once '../admin/admin-dashboard-handler.php';
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
            height: 300px;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        @media (min-width: 768px) {
            .chart-container {
                height: 400px;
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

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen text-gray-800">
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-slate-700 text-white p-3 rounded-md shadow-lg hover:bg-slate-600 transition-colors">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-[200px] bg-slate-800 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col border-r border-slate-600">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl lg:text-2xl font-semibold flex items-center gap-2">
                <img src="../image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-gray-300 hover:text-white duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-grow mt-8 lg:mt-12 space-y-0.5">
            <a href="dashboard.php" class="block text-sm text-white bg-slate-600 px-4 py-2 rounded-md hover:bg-slate-500 transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="admin.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-user-md mr-2"></i> Veterinarians
            </a>
            <a href="records.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fa-solid fa-file-lines mr-2"></i> Records
            </a>
            <a href="#" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors" onclick="toggleModal('adminHelpModal')">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
            </a>
        </nav>
        <div class="pt-4">
            <a href="#" onclick="confirmLogout(event)" class="block text-md text-gray-300 hover:text-red-400 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Dashboard Container -->
    <div class="ml-0 lg:ml-52 p-4 pt-16 lg:pt-4">
        <!-- Header with Welcome and Metrics -->
        <header class="bg-white shadow-lg rounded-lg text-gray-800 py-4 mb-8 p-4 lg:p-6 border border-slate-200">
            <!-- Top Section with Dropdown -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl lg:text-2xl font-bold">Dashboard</h1>

                <!-- Profile Dropdown -->
                <div class="relative inline-block text-left">
                    <button id="profileButton" class="flex items-center justify-center w-10 h-10 bg-gray-100 border border-gray-200 rounded-full hover:bg-gray-200 text-gray-800 text-lg transition-colors">
                        <i class="fas fa-user"></i>
                    </button>
                    <div id="dropdownMenu" class="origin-top-right absolute right-0 mt-2 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out z-50 border border-slate-200">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-12 h-12 rounded-full border-2 border-indigo-500 bg-gray-100 text-indigo-400 text-xl">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800"><?= $vetName ?></p>
                                    <p class="text-xs text-gray-500">Veterinarian</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="profile.php" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-150">
                                <i class="fas fa-edit text-indigo-400"></i>
                                <div>
                                    <div class="font-medium">Edit Profile</div>
                                    <div class="text-xs text-gray-500">Update your information</div>
                                </div>
                            </a>
                            <hr class="my-1 border-slate-200">
                            <a href="#" onclick="confirmLogout(event)" class="flex items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-gray-100 transition-colors duration-150">
                                <i class="fas fa-sign-out-alt text-red-500"></i>
                                <div>
                                    <div class="font-medium">Logout</div>
                                    <div class="text-xs text-red-600">Sign out of your account</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-6">
                <!-- Clients Card -->
                <div class="bg-white p-4 rounded-md h-full relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
                    <a href="clients.php" class="absolute top-1 right-2 text-gray-500 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-user mr-2 text-xl text-indigo-500"></i> Clients</h3>
                        <p class="text-xl"><?= $clientCount ?></p>
                    </div>
                </div>

                <!-- Vets Card -->
                <div class="bg-white p-4 rounded-md relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
                    <a href="admin.php" class="absolute top-1 right-2 text-gray-500 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-user-md mr-2 text-xl text-indigo-500"></i> Veterinarian</h3>
                        <p class="text-xl"><?= $vetCount ?></p>
                    </div>
                </div>

                <!-- Pets Card -->
                <div class="bg-white p-4 rounded-md relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
                    <a href="pets.php" class="absolute top-1 right-2 text-gray-500 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-paw mr-2 text-xl text-teal-500"></i> Pets</h3>
                        <p class="text-xl"><?= $petCount ?></p>
                    </div>
                </div>

                <!-- Medical Records Card -->
                <div class="bg-white p-4 rounded-md relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
                    <a href="medical_records.php" class="absolute top-1 right-2 text-gray-500 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-file-medical mr-2 text-xl text-blue-500"></i> Medical Records</h3>
                        <p class="text-xl"><?= $recordCount ?></p>
                    </div>
                </div>

                <!-- Total Payments Card -->
                <div class="bg-white p-4 rounded-md relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
                    <a href="payment_methods.php" class="absolute top-1 right-2 text-gray-500 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fa-solid fa-money-bill-wave mr-2 text-xl text-indigo-500"></i> Total Payments</h3>
                        <p class="text-xl">₱<?= number_format($totalPayment, 2) ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Graph Section -->
        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-lg border border-slate-200">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-800 mb-6">Analytics Overview</h2>
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="flex-1 bg-white border border-slate-200 rounded-lg p-4 shadow-lg hover:border-indigo-400 transition-colors">
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Monthly Income</h3>
                    <div class="chart-container">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>
                <div class="flex-0.5 bg-white border border-slate-200 rounded-lg p-4 shadow-lg hover:border-indigo-400 transition-colors">
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Most Common Medical Conditions</h3>
                    <div class="chart-container">
                        <canvas id="conditionChart"></canvas>
                    </div>
                </div>
            </div>
        </main>

        <!-- Recent Activities Section -->
        <div class="bg-white p-4 lg:p-6 rounded-lg shadow-lg mt-8 border border-slate-200">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-800 mb-6">Recent Activities</h2>
            <div class="table-container overflow-x-scroll lg:overflow-x-hidden">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-gray-300">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider min-w-[90px]">Name</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider min-w-[110px]">Description</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider min-w-[90px]">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php foreach ($logs as $index => $log): ?>
                            <?php $serial = ($offset + $index + 1); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm"><?= $serial ?></td>
                                <td class="px-4 py-2 text-sm"><?= htmlspecialchars($log['name'] ?? 'Guest') ?></td>
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
                        <a href="?page=<?= $currentPage - 1 ?>" class="px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded hover:bg-gray-200">« Prev</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="px-3 py-1 text-sm <?= $i === $currentPage ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-700' ?> rounded hover:bg-indigo-500 hover:text-white"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>" class="px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded hover:bg-gray-200">Next »</a>
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
                    backgroundColor: ['#3b82f6', '#6366f1', '#2dd4bf'],
                    borderColor: ['#2563eb', '#4f46e5', '#14b8a6'],
                    borderWidth: 1,
                    borderRadius: 4
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
                        grid: {
                            display: false
                        },
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
                    backgroundColor: ['#3b82f6', '#2dd4bf', '#6366f1', '#a855f7'],
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
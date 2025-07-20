<?php require_once './functions/dashboard-handler.php';     ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="Assets/chart.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
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
    <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-green-500 to-green-600 text-white p-4 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40">
        <!-- Close button for mobile -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl lg:text-3xl lg:mt-3 font-semibold mb-6 flex items-center gap-2 lg:mt-0">
                <img src="image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-white hover:text-gray-300 duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="mt-8 lg:mt-36">
            <a href="dashboard.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-tachometer-alt mr-2"></i>
                <span class="md:inline">Dashboard</span>
            </a>
            <a href="clients.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-user mr-2"></i>
                <span class="md:inline">Clients</span>
            </a>
            <a href="pets.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-paw mr-2"></i>
                <span class="md:inline">Pets</span>
            </a>
            <a href="medical_records.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-file-medical mr-2"></i>
                <span class="md:inline">Medical Records</span>
            </a>
            <a href="profile.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-id-badge mr-2"></i>
                <span class="md:inline">Profile</span>
            </a>
            <a href="payment_methods.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-credit-card mr-2"></i>
                <span class="md:inline">Payments</span>
            </a>
            <a href="archive.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fa-solid fa-box-archive"></i>
                <span class="md:inline">Archive</span>
            </a>
            <a href="#" onclick="confirmLogout(event)" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Dashboard Container -->
    <div class="ml-0 lg:ml-64 p-4 pt-16 lg:pt-4">
        <!-- Header with Welcome and Metrics -->
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-8 p-4 lg:p-8">
            <!-- Top Greeting -->
            <div class="flex justify-between flex-col sm:flex-row items-start sm:items-center gap-4">
                <h1 class="text-xl lg:text-2xl font-bold">Hello, <?= $vetName ?>.</h1>
                <h1 class="text-xl lg:text-2xl font-bold">Dashboard</h1>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                <!-- Clients Card -->
                <div class="bg-green-100 p-8 rounded-md relative">
                    <a href="clients.php" class="absolute top-2 right-2 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-user mr-2 text-2xl"></i> Clients</h3>
                        <p class="text-xl"><?= $clientCount ?></p>
                    </div>
                </div>

                <!-- Pets Card -->
                <div class="bg-green-100 p-8 rounded-md relative">
                    <a href="pets.php" class="absolute top-2 right-2 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-paw mr-2 text-2xl"></i> Pets</h3>
                        <p class="text-xl"><?= $petCount ?></p>
                    </div>
                </div>

                <!-- Medical Records Card -->
                <div class="bg-green-100 p-8 rounded-md relative">
                    <a href="medical_records.php" class="absolute top-2 right-2 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-file-medical mr-2 text-2xl"></i> Medical Records</h3>
                        <p class="text-xl"><?= $recordCount ?></p>
                    </div>
                </div>

                <!-- Total Payments Card -->
                <div class="bg-green-100 p-8 rounded-md relative">
                    <a href="payment_methods.php" class="absolute top-2 right-2 text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center">
                        <h3 class="font-bold text-xl mb-1"><i class="fa-solid fa-money-bill-wave mr-2 text-2xl"></i> Total Payments</h3>
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
                        '#43A047 ', '#66BB6A ', '#26A69A ', '#FFCA28 ', '#EF5350 '
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
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
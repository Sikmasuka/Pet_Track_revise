<?php
require_once './functions/dashboard-handler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="Assets/chart.js"></script>
    <script src="Assets/Extension.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for mobile menu toggle */
        .mobile-menu-hidden {
            transform: translateX(-100%);
        }

        .mobile-menu-visible {
            transform: translateX(0);
        }

        /* Ensure chart container is responsive */
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
            <a href="logout.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Content -->
    <div class="ml-0 lg:ml-64 p-4 lg:p-8 pt-16 lg:pt-4">
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-8 p-4 lg:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold">
                    Hello, <?= $vetName ?>.
                </h1>
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold">
                    Dashboard
                </h1>
            </div>

            <!-- Stats Grid - Responsive -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                <div class="bg-green-100 p-4 rounded-md text-center">
                    <h3 class="text-base lg:text-lg font-bold">Clients</h3>
                    <p class="text-xl lg:text-2xl"><?= $clientCount ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-md text-center">
                    <h3 class="text-base lg:text-lg font-bold">Pets</h3>
                    <p class="text-xl lg:text-2xl"><?= $petCount ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-md text-center sm:col-span-2 lg:col-span-1">
                    <h3 class="text-base lg:text-lg font-bold">Medical Records</h3>
                    <p class="text-xl lg:text-2xl"><?= $recordCount ?></p>
                </div>
            </div>
        </header>

        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-sm">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-green-800 mb-4">Most Common Medical Conditions</h2>

            <!-- Chart: Medical Conditions Bar Chart -->
            <div class="chart-container">
                <canvas id="conditionChart"></canvas>
            </div>
        </main>
    </div>


    <script>
        // Data for the medical conditions chart
        const conditionLabels = <?= json_encode($conditionLabels) ?>;
        const conditionCounts = <?= json_encode($conditionCounts) ?>;

        // Chart.js configuration
        const data = {
            labels: conditionLabels,
            datasets: [{
                label: 'Medical Condition Frequency',
                data: conditionCounts,
                backgroundColor: ['#2C6B2F', '#388E3C', '#4CAF50', '#66BB6A', '#81C784'],
                borderColor: ['#2C6B2F', '#388E3C', '#4CAF50', '#66BB6A', '#81C784'],
                borderWidth: 1
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: window.innerWidth > 768
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Count of Conditions'
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: window.innerWidth > 768 ? 45 : 90,
                            minRotation: window.innerWidth > 768 ? 0 : 45
                        }
                    }
                }
            }
        };

        // Render the chart
        const ctx = document.getElementById('conditionChart').getContext('2d');
        const chart = new Chart(ctx, config);

        // Update chart on window resize
        window.addEventListener('resize', function() {
            chart.options.plugins.legend.display = window.innerWidth > 768;
            chart.options.scales.x.ticks.maxRotation = window.innerWidth > 768 ? 45 : 90;
            chart.options.scales.x.ticks.minRotation = window.innerWidth > 768 ? 0 : 45;
            chart.update();
        });
    </script>
    <script src="./js/sidebarHandler.js"></script>
</body>

</html>
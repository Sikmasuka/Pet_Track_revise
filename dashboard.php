<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/functions/dashboard-handler.php";
require_once __DIR__ . "/functions/auth.php";
require_once __DIR__ . "/functions/logs.php";
include "includes/sitemap/Help/support.php";

requireVet();

// Pagination settings
$itemsPerPage = 10; // Match admin-dashboard.php
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Count total logs
$totalStmt = $pdo->query("SELECT COUNT(*) FROM Logs WHERE Table_Affected = 'Guest'");
$totalLogs = $totalStmt->fetchColumn();
$totalPages = ceil($totalLogs / $itemsPerPage);

// Fetch paginated logs
$logQuery = "
    SELECT 
        l.Description,
        l.Timestamp,
        ap.owner_name AS name
    FROM Logs l
    LEFT JOIN appointments ap ON l.Table_Affected = 'Guest' AND l.User_ID = 0 AND ap.id = (SELECT MAX(id) FROM appointments WHERE owner_name LIKE CONCAT('%', SUBSTRING_INDEX(l.Description, ' ', 2), '%'))
    WHERE l.Table_Affected = 'Guest'
    ORDER BY l.Timestamp DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($logQuery);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch current veterinarian data for the modal
$stmt = $pdo->prepare("SELECT * FROM veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$currentVet = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="Assets/chart.js"></script>
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
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

        /* Custom dark theme scrollbar */
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
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-[200px] bg-slate-700 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col border-r border-slate-600">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl lg:text-2xl font-semibold flex items-center gap-2">
                <img src="image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <!-- Close button (mobile only) -->
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-gray-300 hover:text-white duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-grow mt-8 lg:mt-12 space-y-0.5">
            <a href="dashboard.php" class="block text-sm text-white bg-slate-600 px-4 py-2 rounded-md hover:bg-slate-500 transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="clients.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-user mr-2"></i> Clients
            </a>
            <a href="pets.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-paw mr-2"></i> Pets
            </a>
            <a href="medical_records.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-file-medical mr-2"></i> Medical Records
            </a>
            <a href="profile.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-id-badge mr-2"></i> Profile
            </a>
            <a href="payment_methods.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-credit-card mr-2"></i> Payments
            </a>
            <a href="appointments.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-calendar-days mr-2"></i> Appointments
            </a>
            <a href="archive.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fa-solid fa-box-archive mr-2"></i> Archive
            </a>
            <a href="#" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors" onclick="toggleModal('vetHelpModal')">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
            </a>
        </nav>

        <!-- Logout -->
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
                <!-- Dashboard Title -->
                <h1 class="text-xl lg:text-2xl font-bold">Dashboard</h1>

                <!-- Profile Dropdown -->
                <div class="relative inline-block text-left">
                    <button id="profileButton" class="flex items-center justify-center w-10 h-10 bg-gray-100 border border-gray-200 rounded-full hover:bg-gray-200 text-gray-800 text-lg transition-colors">
                        <i class="fas fa-user"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="dropdownMenu"
                        class="origin-top-right absolute right-0 mt-2 w-72 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out z-50 border border-slate-200">
                        <!-- User Info Section -->
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
                        <!-- Menu Options -->
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
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
                <div class="bg-white p-5 rounded-md relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
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
                <!-- Monthly Income Box -->
                <div class="flex-1 bg-white border border-slate-200 rounded-lg p-4 shadow-lg hover:border-indigo-400 transition-colors">
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Monthly Income</h3>
                    <div class="chart-container">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>

                <!-- Most Common Medical Conditions Box -->
                <div class="flex-0.5 bg-white border border-slate-200 rounded-lg p-4 shadow-lg hover:border-indigo-400 transition-colors">
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Most Common Medical Conditions</h3>
                    <div class="chart-container">
                        <canvas id="conditionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Section -->
            <div class="mt-8 bg-white border border-slate-200 rounded-lg p-4 shadow-lg hover:border-indigo-400 transition-colors">
                <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Recent Activities</h3>
                <div class="table-container overflow-x-scroll">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php foreach ($recentActivities as $index => $activity): ?>
                                <?php $serial = ($offset + $index + 1); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm whitespace-nowrap"><?= $serial ?></td>
                                    <td class="px-4 py-2 text-sm whitespace-nowrap"><?= htmlspecialchars($activity['name'] ?? 'Unknown') ?></td>
                                    <td class="px-4 py-2 text-sm whitespace-nowrap"><?= htmlspecialchars($activity['Description']) ?></td>
                                    <td class="px-4 py-2 text-sm whitespace-nowrap"><?= date('M d, Y H:i', strtotime($activity['Timestamp'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentActivities)): ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-sm text-center text-gray-500">No recent appointment activities</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="mt-4 flex justify-center space-x-2">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 ?>" class="px-3 py-1 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">« Prev</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="px-3 py-1 <?= $i === $currentPage ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-700' ?> rounded hover:bg-indigo-500 hover:text-white"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 ?>" class="px-3 py-1 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">Next »</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-60 z-[60] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg h-[60vh] flex flex-col transform transition-all duration-300 scale-95 opacity-0 border border-slate-200" id="profileModalContent">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-white to-gray-50 text-gray-800 px-6 py-4 rounded-t-2xl flex items-center justify-between border-b border-slate-200">
                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-500 p-2 rounded-full">
                        <i class="fas fa-user-edit text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Edit Profile</h3>
                        <p class="text-gray-500 text-sm">Update your account details</p>
                    </div>
                </div>
                <button onclick="closeProfileModal()" class="text-gray-500 hover:text-gray-800 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <form method="POST" action="functions/update-profile.php" class="space-y-5">
                    <!-- Profile Image Section -->
                    <div class="flex justify-center mb-6">
                        <div class="relative group">
                            <img src="" class="w-24 h-24 rounded-full border-4 border-indigo-500 object-cover transition-transform duration-300 group-hover:scale-105" alt="Profile Picture">
                            <button type="button" class="absolute bottom-0 right-0 bg-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-indigo-600 transition-colors duration-200 opacity-0 group-hover:opacity-100">
                                <i class="fas fa-camera text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Name Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">
                            <i class="fas fa-user mr-2 text-indigo-500"></i>
                            Full Name
                        </label>
                        <input type="text" name="vet_name" value="<?= htmlspecialchars($currentVet['vet_name'] ?? '') ?>" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all bg-gray-50 text-gray-800" required>
                    </div>

                    <!-- Contact Number Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">
                            <i class="fas fa-phone mr-2 text-teal-500"></i>
                            Contact Number
                        </label>
                        <input type="tel" name="vet_contact_number" value="<?= htmlspecialchars($currentVet['vet_contact_number'] ?? '') ?>" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all bg-gray-50 text-gray-800" required>
                    </div>

                    <!-- Username Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">
                            <i class="fas fa-at mr-2 text-blue-500"></i>
                            Username
                        </label>
                        <input type="text" name="vet_username" value="<?= htmlspecialchars($currentVet['vet_username'] ?? '') ?>" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-gray-50 text-gray-800" required>
                    </div>

                    <!-- Hidden field to pass vet_id -->
                    <input type="hidden" name="vet_id" value="<?= htmlspecialchars($currentVet['vet_id'] ?? '') ?>">

                    <!-- Password Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">
                            <i class="fas fa-lock mr-2 text-purple-500"></i>
                            New Password (Optional)
                        </label>
                        <div class="relative">
                            <input type="password" id="vetPassword" name="vet_password" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-gray-50 text-gray-800 pr-12" placeholder="Leave blank to keep current">
                            <button type="button" onclick="toggleModalPassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-800 transition-colors duration-200">
                                <i class="fas fa-eye" id="modalPasswordToggle"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Only fill this if you want to change your password</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 pt-6 border-t border-slate-200">
                        <button type="button" onclick="closeProfileModal()" class="flex-1 bg-gray-100 text-gray-800 px-5 py-3 rounded-lg hover:bg-gray-200 transition-colors duration-200 shadow-sm border border-slate-200">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="flex-1 bg-indigo-500 text-white px-5 py-3 rounded-lg hover:bg-indigo-600 transition-colors duration-200 shadow-sm">
                            <i class="fas fa-save mr-2"></i>Save
                        </button>
                    </div>
                </form>
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
                    borderRadius: 4,
                    hoverBackgroundColor: ['#60a5fa', '#818cf8', '#67e8f9']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#374151'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (₱)',
                            color: '#374151'
                        },
                        grid: {
                            color: '#E2E8F0'
                        },
                        ticks: {
                            color: '#374151'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#374151'
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
                    borderColor: '#1e293b',
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
                            color: '#374151'
                        }
                    }
                }
            }
        });

        function toggleModal(modalId) {
            console.log("Toggling modal:", modalId); // Debug log
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle('hidden');
            } else {
                console.error("Modal not found:", modalId);
            }
        }
    </script>

    <script src="./js/dashboard.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
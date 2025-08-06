<?php
require_once './functions/dashboard-handler.php';
require_once './functions/auth.php';

requireVet();

// Fetch current veterinarian data for the modal
$stmt = $pdo->prepare("SELECT * FROM veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]); // Assuming you store vet_id in session
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

<body class="bg-slate-900 min-h-screen text-gray-100">

    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-slate-700 text-white p-3 rounded-md shadow-lg hover:bg-slate-600 transition-colors">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-[200px] bg-slate-800 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col border-r border-slate-700">
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
            <a href="dashboard.php" class="block text-sm text-white bg-slate-700 px-4 py-2 rounded-md hover:bg-slate-600 transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="clients.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-user mr-2"></i> Clients
            </a>
            <a href="pets.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-paw mr-2"></i> Pets
            </a>
            <a href="medical_records.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-file-medical mr-2"></i> Medical Records
            </a>
            <a href="profile.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-id-badge mr-2"></i> Profile
            </a>
            <a href="payment_methods.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-credit-card mr-2"></i> Payments
            </a>
            <a href="appointments.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-calendar-days mr-2"></i> Appointments
            </a>
            <a href="archive.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fa-solid fa-box-archive mr-2"></i> Archive
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
        <header class="bg-slate-800 rounded-lg text-white py-4 shadow-sm mb-8 p-4 lg:p-6 border border-slate-700">
            <!-- Top Section with Dropdown -->
            <div class="flex justify-between items-center mb-6">
                <!-- Dashboard Title -->
                <h1 class="text-xl lg:text-2xl font-bold">Dashboard</h1>

                <!-- Profile Dropdown -->
                <div class="relative inline-block text-left">
                    <button id="profileButton" class="flex items-center justify-center w-10 h-10 bg-slate-700 border border-slate-600 rounded-full hover:bg-slate-600 text-white text-lg transition-colors">
                        <i class="fas fa-user"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="dropdownMenu"
                        class="origin-top-right absolute right-0 mt-2 w-72 rounded-lg shadow-lg bg-slate-800 ring-1 ring-black ring-opacity-5 opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out z-50 border border-slate-700">
                        <!-- User Info Section -->
                        <div class="px-4 py-3 border-b border-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-12 h-12 rounded-full border-2 border-indigo-500 bg-slate-700 text-indigo-400 text-xl">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-white"><?= $vetName ?></p>
                                    <p class="text-xs text-slate-400">Veterinarian</p>
                                </div>
                            </div>
                        </div>
                        <!-- Menu Options -->
                        <div class="py-1">
                            <a href="profile.php" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors duration-150">
                                <i class="fas fa-edit text-indigo-400"></i>
                                <div>
                                    <div class="font-medium">Edit Profile</div>
                                    <div class="text-xs text-slate-400">Update your information</div>
                                </div>
                            </a>
                            <hr class="my-1 border-slate-700">
                            <a href="#" onclick="confirmLogout(event)" class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-slate-700 transition-colors duration-150">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                <!-- Clients Card -->
                <div class="bg-slate-700 p-4 rounded-md h-full relative border border-slate-600 hover:border-indigo-400 transition-colors">
                    <a href="clients.php" class="absolute top-1 right-2 text-slate-400 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-user mr-2 text-xl text-indigo-400"></i> Clients</h3>
                        <p class="text-xl"><?= $clientCount ?></p>
                    </div>
                </div>

                <!-- Pets Card -->
                <div class="bg-slate-700 p-4 rounded-md relative border border-slate-600 hover:border-indigo-400 transition-colors">
                    <a href="pets.php" class="absolute top-1 right-2 text-slate-400 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-paw mr-2 text-xl text-indigo-400"></i> Pets</h3>
                        <p class="text-xl"><?= $petCount ?></p>
                    </div>
                </div>

                <!-- Medical Records Card -->
                <div class="bg-slate-700 p-4 rounded-md relative border border-slate-600 hover:border-indigo-400 transition-colors">
                    <a href="medical_records.php" class="absolute top-1 right-2 text-slate-400 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-file-medical mr-2 text-xl text-indigo-400"></i> Medical Records</h3>
                        <p class="text-xl"><?= $recordCount ?></p>
                    </div>
                </div>

                <!-- Total Payments Card -->
                <div class="bg-slate-700 p-5 rounded-md relative border border-slate-600 hover:border-indigo-400 transition-colors">
                    <a href="payment_methods.php" class="absolute top-1 right-2 text-slate-400 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fa-solid fa-money-bill-wave mr-2 text-xl text-indigo-400"></i> Total Payments</h3>
                        <p class="text-xl">₱<?= number_format($totalPayment, 2) ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Graph Section -->
        <main class="bg-slate-800 p-4 lg:p-6 rounded-lg shadow-sm border border-slate-700">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-white mb-6">Analytics Overview</h2>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Monthly Income Box -->
                <div class="flex-1 bg-slate-700 border border-slate-600 rounded-lg p-4 shadow-sm hover:border-indigo-400 transition-colors">
                    <h3 class="text-base lg:text-lg font-semibold text-white mb-4">Monthly Income</h3>
                    <div class="chart-container">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>

                <!-- Most Common Medical Conditions Box -->
                <div class="flex-0.5 bg-slate-700 border border-slate-600 rounded-lg p-4 shadow-sm hover:border-indigo-400 transition-colors">
                    <h3 class="text-base lg:text-lg font-semibold text-white mb-4">Most Common Medical Conditions</h3>
                    <div class="chart-container">
                        <canvas id="conditionChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-60 z-[60] hidden flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg h-[60vh] flex flex-col transform transition-all duration-300 scale-95 opacity-0 border border-slate-700" id="profileModalContent">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-slate-700 to-slate-800 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between border-b border-slate-700">
                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-500 p-2 rounded-full">
                        <i class="fas fa-user-edit text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Edit Profile</h3>
                        <p class="text-slate-300 text-sm">Update your account details</p>
                    </div>
                </div>
                <button onclick="closeProfileModal()" class="text-slate-300 hover:text-white transition-colors duration-200">
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
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            <i class="fas fa-user mr-2 text-indigo-400"></i>
                            Full Name
                        </label>
                        <input type="text" name="vet_name" value="<?= htmlspecialchars($currentVet['vet_name'] ?? '') ?>" class="w-full px-4 py-3 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all bg-slate-700 text-white" required>
                    </div>

                    <!-- Contact Number Field -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            <i class="fas fa-phone mr-2 text-indigo-400"></i>
                            Contact Number
                        </label>
                        <input type="tel" name="vet_contact_number" value="<?= htmlspecialchars($currentVet['vet_contact_number'] ?? '') ?>" class="w-full px-4 py-3 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all bg-slate-700 text-white" required>
                    </div>

                    <!-- Username Field -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            <i class="fas fa-at mr-2 text-indigo-400"></i>
                            Username
                        </label>
                        <input type="text" name="vet_username" value="<?= htmlspecialchars($currentVet['vet_username'] ?? '') ?>" class="w-full px-4 py-3 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all bg-slate-700 text-white" required>
                    </div>

                    <!-- Hidden field to pass vet_id -->
                    <input type="hidden" name="vet_id" value="<?= htmlspecialchars($currentVet['vet_id'] ?? '') ?>">

                    <!-- Password Field -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            <i class="fas fa-lock mr-2 text-indigo-400"></i>
                            New Password (Optional)
                        </label>
                        <div class="relative">
                            <input type="password" id="vetPassword" name="vet_password" class="w-full px-4 py-3 border border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all bg-slate-700 text-white pr-12" placeholder="Leave blank to keep current">
                            <button type="button" onclick="toggleModalPassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-white transition-colors duration-200">
                                <i class="fas fa-eye" id="modalPasswordToggle"></i>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Only fill this if you want to change your password</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 pt-6 border-t border-slate-700">
                        <button type="button" onclick="closeProfileModal()" class="flex-1 bg-slate-700 text-white px-5 py-3 rounded-lg hover:bg-slate-600 transition-colors duration-200 shadow-sm border border-slate-600">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="flex-1 bg-indigo-600 text-white px-5 py-3 rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-sm">
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
                    backgroundColor: '#6366f1',
                    borderColor: '#4f46e5',
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBackgroundColor: '#818cf8'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#e2e8f0'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (₱)',
                            color: '#e2e8f0'
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8'
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
                        '#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b'
                    ],
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
                            color: '#e2e8f0'
                        }
                    }
                }
            }
        });

        // Dropdown functionality
        const button = document.getElementById('profileButton');
        const menu = document.getElementById('dropdownMenu');
        const chevron = document.getElementById('chevronIcon');

        button.addEventListener('click', () => {
            const isOpen = menu.classList.contains('opacity-100');

            if (isOpen) {
                // Close dropdown
                menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                chevron.style.transform = 'rotate(0deg)';
            } else {
                // Open dropdown
                menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                menu.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
                chevron.style.transform = 'rotate(180deg)';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                chevron.style.transform = 'rotate(0deg)';
            }
        });

        // Add smooth transitions to dropdown items
        const dropdownItems = document.querySelectorAll('#dropdownMenu a, #dropdownMenu button');
        dropdownItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.transform = 'translateX(4px)';
            });
            item.addEventListener('mouseleave', () => {
                item.style.transform = 'translateX(0)';
            });
        });

        // Simple Profile Modal Functions
        function openProfileModal() {
            // Close dropdown first
            menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
            menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            chevron.style.transform = 'rotate(0deg)';

            // Show modal
            const modal = document.getElementById('profileModal');
            const modalContent = document.getElementById('profileModalContent');

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Animate in
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeProfileModal() {
            const modal = document.getElementById('profileModal');
            const modalContent = document.getElementById('profileModalContent');

            // Animate out
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        function toggleModalPassword() {
            const passwordInput = document.getElementById('vetPassword');
            const passwordToggle = document.getElementById('modalPasswordToggle');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }

        // Close modal when clicking outside
        document.getElementById('profileModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProfileModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProfileModal();
            }
        });
    </script>

    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
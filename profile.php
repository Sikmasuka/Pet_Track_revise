<?php
require_once 'functions/profile-handler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="Assets/Extension.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex min-h-screen">

    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-green-600 text-white p-3 rounded-md shadow-lg hover:bg-green-700 transition-colors">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 w-50 bg-gradient-to-b from-green-500 to-green-600 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40">
        <!-- Close button for mobile -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl lg:text-2xl lg:mt-3 font-semibold mb-6 flex items-center gap-2 lg:mt-0">
                <img src="image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-white hover:text-gray-300 duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="mt-8 lg:mt-20">
            <a href="dashboard.php" class="block text-md lg:text-sm text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-tachometer-alt mr-2"></i>
                <span class="md:inline">Dashboard</span>
            </a>
            <a href="clients.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-user mr-2"></i>
                <span class="md:inline">Clients</span>
            </a>
            <a href="pets.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-paw mr-2"></i>
                <span class="md:inline">Pets</span>
            </a>
            <a href="medical_records.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-file-medical mr-2"></i>
                <span class="md:inline">Medical Records</span>
            </a>
            <a href="profile.php" class="block text-md lg:text-md text-white bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-id-badge mr-2"></i>
                <span class="md:inline">Profile</span>
            </a>
            <a href="payment_methods.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-credit-card mr-2"></i>
                <span class="md:inline">Payments</span>
            </a>
            <a href="appointments.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-calendar-days mr-2"></i>
                <span class="md:inline">Appointments</span>
            </a>
            <a href="archive.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fa-solid fa-box-archive mr-2"></i>
                <span class="md:inline">Archive</span>
            </a>
            <a href="#" onclick="confirmLogout(event)" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Content -->
    <div class="ml-0 lg:ml-52 p-4 pt-12 lg:pt-4 w-full">
        <!-- Header -->
        <header class="bg-white rounded-none lg:rounded-lg text-green-800 p-4 lg:py-6 p-4 mx-4 shadow-sm mb-4 lg:mb-8 px-4 lg:px-8 mt-16 lg:mt-4">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-semibold flex items-center gap-2">
                <i class="fas fa-user-circle text-green-600"></i>
                <span>My Profile</span>
            </h1>
        </header>

        <!-- Main Content Area -->
        <main class="w-full max-w-full sm:max-w-xl sm:mx-auto bg-white rounded-lg shadow-sm mb-8">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-t-lg">
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 p-3 rounded-full flex-shrink-0">
                        <i class="fas fa-user-md text-2xl"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-lg sm:text-2xl font-bold">Profile</h2>
                        <p class="text-green-100 text-sm">Manage your account information</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm w-full max-w-md sm:max-w-2xl lg:max-w-4xl mx-auto">
                <!-- Form Content -->
                <div class="p-6 sm:p-8 lg:p-10">
                    <form method="POST" class="space-y-6">
                        <div class="space-y-6">
                            <!-- Name Field -->
                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user mr-2 text-green-600"></i>
                                    Full Name
                                </label>
                                <input type="text"
                                    name="vet_name"
                                    value="<?= htmlspecialchars($vet['vet_name']) ?>"
                                    class="w-full px-4 py-4 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all"
                                    required
                                    placeholder="Enter your full name">
                            </div>

                            <!-- Contact Number Field -->
                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-2 text-green-600"></i>
                                    Contact Number
                                </label>
                                <input type="tel"
                                    name="vet_contact_number"
                                    value="<?= htmlspecialchars($vet['vet_contact_number']) ?>"
                                    class="w-full px-4 py-4 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all"
                                    required
                                    placeholder="Enter your contact number">
                            </div>

                            <!-- Username Field -->
                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-at mr-2 text-green-600"></i>
                                    Username
                                </label>
                                <input type="text"
                                    name="vet_username"
                                    value="<?= htmlspecialchars($vet['vet_username']) ?>"
                                    class="w-full px-4 py-4 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all"
                                    required
                                    placeholder="Enter your username">
                            </div>

                            <!-- Password Field -->
                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-green-600"></i>
                                    Password
                                </label>
                                <div class="relative">
                                    <input type="password"
                                        name="vet_password"
                                        id="password"
                                        class="w-full px-4 py-4 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all pr-12"
                                        placeholder="Leave blank to keep current password">
                                    <button type="button"
                                        onclick="togglePassword()"
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 p-1">
                                        <i class="fas fa-eye" id="passwordToggle"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Only fill this if you want to change your password</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-4 pt-6 border-t border-gray-200">
                            <button type="submit"
                                class="w-full bg-green-500 text-white px-6 py-4 rounded-lg hover:bg-green-600 transition-colors text-base font-medium">
                                <i class="fas fa-save mr-2"></i>
                                Save Changes
                            </button>
                            <button type="button"
                                onclick="resetForm()"
                                class="w-full bg-gray-500 text-white px-6 py-4 rounded-lg hover:bg-gray-600 transition-colors text-base font-medium">
                                <i class="fas fa-undo mr-2"></i>
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>

    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');

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

        // Reset form functionality
        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                document.querySelector('form').reset();
                // Reset password field specifically
                document.getElementById('password').value = '';
            }
        }

        // Enhanced mobile menu handling
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');

            // Open sidebar
            mobileMenuBtn?.addEventListener('click', function() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });

            // Close sidebar
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            closeSidebarBtn?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);

            // Close sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });
        });
    </script>
</body>

</html>
<?php
session_start();
require_once '../admin/record-handler.php';
include "../includes/sitemap/Help/support.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <title>Veterinarian Admin - Records</title>

    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Load SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="Assets/Extension.js"></script>
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
            <a href="admin-dashboard.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Dashboard
            </a>
            <a href="admin.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-user-md mr-2"></i> Veterinarians
            </a>
            <a href="records.php" class="block text-md lg:text-md text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
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
    <div class="ml-0 lg:ml-48 p-4 pt-16 lg:pt-4">
        <div class="flex justify-between p-4 bg-white rounded-lg shadow-sm mb-6">
            <h2 class="text-2xl font-bold text-green-700">Hello, admin.</h2>
            <h1 class="text-3xl font-bold text-green-700">Records</h1>
        </div>

        <!-- Single Column for Records -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Container 1: Medical Records -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <h3 class="text-xl font-semibold text-green-700 mb-4">All Medical Records</h3>
                <?php
                $medicalRecords = getMedicalRecords($pdo); // Call function from record-handler.php
                if (isset($error) && !empty($error)) {
                    echo "<p class='text-red-500 text-sm'>Error: $error</p>";
                } elseif (!empty($medicalRecords) && is_array($medicalRecords)) {
                ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">medical_condition</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($medicalRecords as $record): ?>
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($record['record_id'] ?? '') ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($record['medical_condition'] ?? '') ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($record['record_date'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-gray-500 text-sm">No medical records found.</p>
                <?php } ?>
            </div>

            <!-- Container 2: All Veterinarians -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <h3 class="text-xl font-semibold text-green-700 mb-4">All Veterinarians</h3>
                <?php
                $veterinarians = getVeterinarians($pdo); // Call function from record-handler.php
                if (isset($error) && !empty($error)) {
                    echo "<p class='text-red-500 text-sm'>Error: $error</p>";
                } elseif (!empty($veterinarians) && is_array($veterinarians)) {
                ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vet ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($veterinarians as $vet): ?>
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($vet['vet_id'] ?? '') ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($vet['vet_name'] ?? '') ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($vet['vet_contact_number'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-gray-500 text-sm">No veterinarians found.</p>
                <?php } ?>
            </div>

            <!-- Container 3: All Pets -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <h3 class="text-xl font-semibold text-green-700 mb-4">All Pets</h3>
                <?php
                $pets = getPets($pdo); // Call function from record-handler.php
                if (isset($error) && !empty($error)) {
                    echo "<p class='text-red-500 text-sm'>Error: $error</p>";
                } elseif (!empty($pets) && is_array($pets)) {
                ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet ID</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Species</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($pets as $pet): ?>
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($pet['pet_id'] ?? '') ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($pet['pet_name'] ?? '') ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($pet['pet_species'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-gray-500 text-sm">No pets found.</p>
                <?php } ?>
            </div>
        </div>

        <!-- Add Veterinarian Modal -->
        <div id="addModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
            <div id="modalContent" class="bg-white rounded-lg shadow-lg w-full max-w-xl overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-green-500 px-6 py-4">
                    <h3 id="petModalTitle" class="text-xl font-bold text-center text-white">
                        Add Veterinarian
                    </h3>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form method="POST" class="grid grid-cols-1 gap-4">
                        <label for="vet_name" class="font-medium">Name</label>
                        <input type="text" name="vet_name" id="vet_name" placeholder="Name" required class="p-2 border rounded-md">

                        <label for="vet_contact_number" class="font-medium">Contact Number</label>
                        <input type="text" name="vet_contact_number" id="vet_contact_number" placeholder="Contact Number" required class="p-2 border rounded-md">

                        <label for="vet_username" class="font-medium">Username</label>
                        <input type="text" name="vet_username" id="vet_username" placeholder="Username" required class="p-2 border rounded-md">

                        <label for="vet_password" class="font-medium">Password</label>
                        <input type="password" name="vet_password" id="vet_password" placeholder="Password" required class="p-2 border rounded-md">

                        <div class="flex justify-between items-center mt-4">
                            <button type="submit" name="add_vet" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                                Add Veterinarian
                            </button>
                            <button type="button" id="closeAddModal" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/confirmLogout.js"></script>

    <script>
        // Close Add Modal
        document.getElementById('closeAddModal').addEventListener('click', function() {
            document.getElementById('addModal').classList.add('hidden');
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
</body>

</html>
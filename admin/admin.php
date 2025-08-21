<?php
session_start();
require_once '../functions/admin-handler.php';
include "../includes/sitemap/Help/support.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <title>Veterinarian Admin</title>

    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Load SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="Assets/Extension.js"></script>
    <style>
        /* Table responsiveness */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 400px;
        }

        /* Modal responsiveness */
        #addModal,
        #editModal {
            padding: 0.5rem;
        }

        #modalContent,
        #editModal>div {
            max-width: 90vw;
            max-height: 85vh;
        }

        @media (min-width: 640px) {

            #modalContent,
            #editModal>div {
                max-width: 80vw;
            }
        }

        @media (min-width: 768px) {

            #modalContent,
            #editModal>div {
                max-width: 600px;
            }
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
            <a href="admin-dashboard.php" class="block text-md lg:text-md text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Dashboard
            </a>
            <a href="admin.php" class="block text-md lg:text-md text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
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
        <h1 class="text-xl sm:text-2xl font-bold text-green-700 mb-4">Veterinarian Accounts</h1>

        <!-- Add New Veterinarian Button -->
        <button id="openAddModal" class="bg-green-500 text-white px-3 py-2 text-sm sm:text-base rounded-md hover:bg-green-600 mb-6">
            Add New Veterinarian
        </button>

        <!-- Add Veterinarian Modal -->
        <div id="addModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
            <div id="modalContent" class="bg-white rounded-lg shadow-lg w-full overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-green-500 px-4 py-3">
                    <h3 id="petModalTitle" class="text-lg sm:text-xl font-bold text-center text-white">
                        Add Veterinarian
                    </h3>
                </div>

                <!-- Modal Body -->
                <div class="p-4">
                    <form method="POST" id="addVetForm" class="grid grid-cols-1 gap-3">
                        <label for="vet_name" class="font-medium text-sm sm:text-base">Name</label>
                        <input type="text" name="vet_name" id="vet_name" placeholder="Name" required class="p-2 border rounded-md text-sm sm:text-base">

                        <label for="vet_contact_number" class="font-medium text-sm sm:text-base">Contact Number</label>
                        <input type="text" name="vet_contact_number" id="vet_contact_number" placeholder="Contact Number" required class="p-2 border rounded-md text-sm sm:text-base">

                        <label for="vet_username" class="font-medium text-sm sm:text-base">Username</label>
                        <input type="text" name="vet_username" id="vet_username" placeholder="Username" required class="p-2 border rounded-md text-sm sm:text-base">

                        <label for="vet_password" class="font-medium text-sm sm:text-base">Password</label>
                        <input type="password" name="vet_password" id="vet_password" placeholder="Password" required class="p-2 border rounded-md text-sm sm:text-base">
                        <div class="strenght-password"></div>

                        <div class="flex justify-between items-center mt-3 gap-3">
                            <button type="submit" name="add_vet" class="bg-green-500 text-white px-3 py-2 text-sm sm:text-base rounded-md hover:bg-green-600">
                                Add Veterinarian
                            </button>
                            <button type="button" id="closeAddModal" class="bg-red-500 text-white px-3 py-2 text-sm sm:text-base rounded-md hover:bg-red-600">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white p-4 sm:p-5 rounded-lg shadow-md table-container">
            <h2 class="text-lg sm:text-xl font-semibold mb-3">Veterinarians List</h2>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-green-100 text-green-800">
                        <th class="p-2 text-left text-xs sm:text-sm">Name</th>
                        <th class="p-2 text-left text-xs sm:text-sm">Contact</th>
                        <th class="p-2 text-left text-xs sm:text-sm">Username</th>
                        <th class="p-2 text-left text-xs sm:text-sm">Password</th>
                        <th class="p-2 text-center text-xs sm:text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vets as $vet): ?>
                        <tr class="border-b">
                            <td class="p-2 text-xs sm:text-sm"><?= htmlspecialchars($vet['vet_name']) ?></td>
                            <td class="p-2 text-xs sm:text-sm"><?= htmlspecialchars($vet['vet_contact_number']) ?></td>
                            <td class="p-2 text-xs sm:text-sm"><?= htmlspecialchars($vet['vet_username']) ?></td>
                            <td class="p-2 text-xs sm:text-sm italic text-gray-400">Hidden (encrypted)</td>
                            <td class="p-2 text-center text-xs sm:text-sm">
                                <a href="#"
                                    class="text-blue-600 hover:underline mr-2 edit-btn"
                                    data-vet-id="<?= $vet['vet_id'] ?>"
                                    data-vet-name="<?= htmlspecialchars($vet['vet_name'], ENT_QUOTES) ?>"
                                    data-vet-contact="<?= htmlspecialchars($vet['vet_contact_number'], ENT_QUOTES) ?>"
                                    data-vet-username="<?= htmlspecialchars($vet['vet_username'], ENT_QUOTES) ?>">
                                    Edit
                                </a>
                                <a href="#" onclick="confirmDelete(<?= $vet['vet_id'] ?>)" class="text-red-600 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Veterinarian Modal -->
        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg w-full overflow-y-auto">
                <!-- Modal Header -->
                <div class="w-full bg-green-500 px-4 py-3">
                    <h3 class="text-lg sm:text-xl font-bold text-center text-white">
                        Edit Veterinarian
                    </h3>
                </div>

                <!-- Modal Body -->
                <div class="p-4">
                    <form method="POST" class="grid grid-cols-1 gap-3">
                        <input type="hidden" name="vet_id" id="edit_vet_id">

                        <input type="text" name="vet_name" id="edit_vet_name" placeholder="Name" required class="p-2 border rounded-md w-full text-sm sm:text-base">
                        <input type="text" name="vet_contact_number" id="edit_vet_contact_number" placeholder="Contact Number" required class="p-2 border rounded-md w-full text-sm sm:text-base">
                        <input type="text" name="vet_username" id="edit_vet_username" placeholder="Username" required class="p-2 border rounded-md w-full text-sm sm:text-base">
                        <input type="password" name="vet_password" id="edit_vet_password" placeholder="Add password to change it" class="p-2 border rounded-md w-full text-sm sm:text-base">

                        <div class="flex justify-between gap-3 mt-3">
                            <button type="submit" name="update_vet" class="bg-green-500 text-white px-3 py-2 rounded-md hover:bg-green-600 w-full text-sm sm:text-base">
                                Update Veterinarian
                            </button>
                            <button type="button" id="closeEditModal" class="bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-600 w-full text-sm sm:text-base">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add Modal Toggle
        document.getElementById('openAddModal').addEventListener('click', function() {
            document.getElementById('addModal').classList.remove('hidden');
        });

        document.getElementById('closeAddModal').addEventListener('click', function() {
            document.getElementById('addModal').classList.add('hidden');
        });

        // Edit Modal Toggle
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Get vet data from attributes
                const vetId = this.dataset.vetId;
                const vetName = this.dataset.vetName;
                const vetContact = this.dataset.vetContact;
                const vetUsername = this.dataset.vetUsername;

                // Fill the edit form
                document.getElementById('edit_vet_id').value = vetId;
                document.getElementById('edit_vet_name').value = vetName;
                document.getElementById('edit_vet_contact_number').value = vetContact;
                document.getElementById('edit_vet_username').value = vetUsername;
                document.getElementById('edit_vet_password').value = ''; // leave blank for optional update

                // Show the modal
                document.getElementById('editModal').classList.remove('hidden');
            });
        });

        document.getElementById('closeEditModal').addEventListener('click', function() {
            document.getElementById('editModal').classList.add('hidden');
        });

        // Password validation function
        function validateVetPassword(password) {
            const hasNumber = /\d/;
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/;
            const hasUpperCase = /[A-Z]/;

            let errors = [];
            if (password.length < 8) {
                errors.push("At least 8 characters.");
            }
            if (!hasNumber.test(password)) {
                errors.push("At least one number.");
            }
            if (!hasSpecialChar.test(password)) {
                errors.push("At least one special character.");
            }
            if (!hasUpperCase.test(password)) {
                errors.push("At least one uppercase letter.");
            }

            return errors;
        }

        // Intercept Add Vet Form submit
        document.getElementById("addVetForm").addEventListener("submit", function(e) {
            const password = document.getElementById("vet_password").value;
            const errors = validateVetPassword(password);

            if (errors.length > 0) {
                e.preventDefault(); // stop form submission
                Swal.fire({
                    icon: "error",
                    title: "Weak Password",
                    html: errors.join("<br>"),
                    confirmButtonColor: "#d33"
                });
            }
        });

        // Confirm delete with SweetAlert
        function confirmDelete(vetId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this veterinarian?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the delete URL if confirmed
                    window.location.href = `?delete=${vetId}`;
                }
            });
        }

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

    <script src="../js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/confirmLogout.js"></script>
</body>

</html>
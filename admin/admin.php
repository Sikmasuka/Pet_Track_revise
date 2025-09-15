<?php
session_start();
require_once '../functions/admin-handler.php';
include "../includes/sitemap/Help/support.php";

// Fetch admin data
if (!isset($currentAdmin)) {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE admin_id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $currentAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Define $adminName
$adminName = htmlspecialchars($currentAdmin['admin_name'] ?? 'Admin');
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
    <?php include "../includes/sitemap/Help/support.php"; ?>
    <?php include '../includes/edit-profile.php'; ?>

    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-teal-700 text-white p-3 rounded-md shadow-lg hover:bg-teal-600 transition-colors">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 w-[200px] bg-gradient-to-b from-emerald-600 via-teal-700 to-emerald-800 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col border-r border-emerald-900">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h2 class="text-xl lg:text-2xl font-semibold flex items-center gap-2">
                <img src="../image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <button id="closeSidebarBtn"
                class="lg:hidden text-gray-300 hover:text-white duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-grow mt-8 lg:mt-12 space-y-0.5">
            <a href="admin-dashboard.php"
                class="block text-sm text-white hover:bg-emerald-700 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="admin.php"
                class="block text-sm text-white bg-teal-800 hover:bg-emerald-700 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-user-md mr-2"></i> Veterinarians
            </a>

            <!-- Records Dropdown -->
            <div class="space-y-0.5">
                <button id="recordsBtn"
                    class="w-full flex items-center justify-start gap-2 text-sm text-white px-4 py-2 rounded-md hover:bg-emerald-700 transition-colors">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Records</span>
                    <svg id="recordsArrow"
                        class="w-4 h-4 ml-1 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Submenu -->
                <div id="recordsMenu"
                    class="max-h-0 overflow-hidden opacity-0 transition-all duration-200 ease-in-out pl-8 space-y-1">

                    <a href="./records/pet-records.php"
                        class="flex items-center text-sm text-gray-200 hover:bg-emerald-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        <i class="fas fa-paw mr-2"></i> Pets
                    </a>

                    <a href="./records/client-records.php"
                        class="flex items-center text-sm text-gray-200 hover:bg-emerald-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        <i class="fas fa-user mr-2"></i> Clients
                    </a>

                    <a href="./records/medical-records.php"
                        class="flex items-center text-sm text-gray-200 hover:bg-emerald-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        <i class="fas fa-file-medical mr-2"></i>
                        <span class="whitespace-normal leading-snug">Medical Records</span>
                    </a>

                    <a href="./records/admin-payments.php"
                        class="flex items-center text-sm text-gray-200 hover:bg-emerald-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        <i class="fas fa-credit-card mr-2"></i> Payments Records
                    </a>
                </div>
            </div>


            <!-- Active Link Example -->
            <a href="./admin-appointments.php"
                class="block text-sm text-white hover:bg-emerald-700 px-4 py-2 rounded-md">
                <i class="fas fa-calendar-days mr-2"></i> Appointments
            </a>

            <a href="#" onclick="toggleModal('adminHelpModal')"
                class="block text-sm text-gray-200 hover:bg-emerald-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
            </a>
        </nav>

        <!-- Logout -->
        <div class="pt-4">
            <a href="../index.php" onclick="confirmLogout(event)"
                class="block text-sm text-gray-200 hover:bg-red-600 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Dashboard Container -->
    <div class="ml-0 lg:ml-52 p-4 pt-16 lg:pt-4">

        <!-- Headre -->
        <header class="bg-white shadow-lg rounded-lg text-gray-800 py-4 mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-200">
            <!-- Top Section with Dropdown -->
            <div class="flex justify-between items-center">
                <!-- Dashboard Title -->
                <h1 class="text-xl lg:text-2xl font-bold">Manage Veterinarian</h1>

                <div class="relative inline-block text-left">
                    <button id="profileButton" class="flex items-center justify-center w-10 h-10 bg-gray-100 border border-gray-200 rounded-full hover:bg-gray-200 text-gray-800 text-lg transition-colors">
                        <i class="fas fa-user"></i>
                    </button>
                    <div id="dropdownMenu" class="origin-top-right absolute right-0 mt-2 w-72 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out z-50 border border-slate-200">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-12 h-12 rounded-full border-2 border-indigo-500 bg-gray-100 text-indigo-400 text-xl">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800"><?php echo $adminName; ?></p>
                                    <p class="text-xs text-gray-500">Veterinarian</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="#" id="editProfileLink" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-800 transition-colors duration-150">
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
        </header>

        <!-- Add Veterinarian Modal -->
        <div id="addModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
            <div id="modalContent" class="bg-white rounded-lg shadow-lg w-full overflow-hidden border border-slate-200">
                <!-- Modal Header -->
                <div class="bg-indigo-500 px-4 py-3">
                    <h3 id="petModalTitle" class="text-lg lg:text-xl font-bold text-center text-white">
                        Add Veterinarian
                    </h3>
                </div>

                <!-- Modal Body -->
                <div class="p-4">
                    <form method="POST" id="addVetForm" class="grid grid-cols-1 gap-3">
                        <label for="vet_name" class="font-medium text-sm text-gray-800">Name</label>
                        <input type="text" name="vet_name" id="vet_name" placeholder="Name" required class="p-2 border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">

                        <label for="vet_contact_number" class="font-medium text-sm text-gray-800">Contact Number</label>
                        <input type="text" name="vet_contact_number" id="vet_contact_number" placeholder="Contact Number" required class="p-2 border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">

                        <label for="vet_username" class="font-medium text-sm text-gray-800">Username</label>
                        <input type="text" name="vet_username" id="vet_username" placeholder="Username" required class="p-2 border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">

                        <label for="vet_password" class="font-medium text-sm text-gray-800">Password</label>
                        <input type="password" name="vet_password" id="vet_password" placeholder="(e.g., MyPass@2025)" required class="p-2 border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <div class="strenght-password"></div>

                        <div class="flex justify-between items-center mt-3 gap-3">
                            <button type="submit" name="add_vet" class="bg-indigo-500 text-white px-4 py-2 text-sm rounded-md hover:bg-indigo-600 transition-colors">
                                Add Veterinarian
                            </button>
                            <button type="button" id="closeAddModal" class="bg-red-500 text-white px-4 py-2 text-sm rounded-md hover:bg-red-600 transition-colors">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white p-4 lg:p-6 rounded-lg shadow-lg table-container border border-slate-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-800 mb-4">Veterinarians List</h2>

                <!-- Add New Veterinarian Button -->
                <button id="openAddModal" class="bg-indigo-500 text-white px-4 py-2 text-sm rounded-md hover:bg-indigo-600 mb-8 transition-colors">
                    Add New Veterinarian
                </button>
            </div>

            <table class="w-full table-auto divide-y divide-slate-200">
                <thead>
                    <tr class="bg-gray-300 text-gray-700">
                        <th class="p-4 text-left text-xs uppercase tracking-wider">Name</th>
                        <th class="p-4 text-left text-xs uppercase tracking-wider">Contact</th>
                        <th class="p-4 text-left text-xs uppercase tracking-wider">Username</th>
                        <th class="p-4 text-left text-xs uppercase tracking-wider">Password</th>
                        <th class="p-4 text-center text-xs uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($vets as $vet): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 text-sm"><?= htmlspecialchars($vet['vet_name']) ?></td>
                            <td class="p-4 text-sm"><?= htmlspecialchars($vet['vet_contact_number']) ?></td>
                            <td class="p-4 text-sm"><?= htmlspecialchars($vet['vet_username']) ?></td>
                            <td class="p-4 text-sm italic text-gray-500">Hidden (encrypted)</td>
                            <td class="p-4 text-center text-sm">
                                <a href="#"
                                    class="text-indigo-500 hover:text-indigo-700 mr-2 edit-btn transition-colors"
                                    data-vet-id="<?= $vet['vet_id'] ?>"
                                    data-vet-name="<?= htmlspecialchars($vet['vet_name'], ENT_QUOTES) ?>"
                                    data-vet-contact="<?= htmlspecialchars($vet['vet_contact_number'], ENT_QUOTES) ?>"
                                    data-vet-username="<?= htmlspecialchars($vet['vet_username'], ENT_QUOTES) ?>">
                                    Edit
                                </a>
                                <a href="#" onclick="confirmDelete(<?= $vet['vet_id'] ?>)" class="text-red-500 hover:text-red-700 transition-colors">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Veterinarian Modal -->
        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg w-full overflow-y-auto border border-slate-200">
                <!-- Modal Header -->
                <div class="w-full bg-indigo-500 px-4 py-3">
                    <h3 class="text-lg lg:text-xl font-bold text-center text-white">
                        Edit Veterinarian
                    </h3>
                </div>

                <!-- Modal Body -->
                <div class="p-4">
                    <form method="POST" class="grid grid-cols-1 gap-3">
                        <input type="hidden" name="vet_id" id="edit_vet_id">

                        <input type="text" name="vet_name" id="edit_vet_name" placeholder="Name" required class="p-2 border border-slate-200 rounded-md w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <input type="text" name="vet_contact_number" id="edit_vet_contact_number" placeholder="Contact Number" required class="p-2 border border-slate-200 rounded-md w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <input type="text" name="vet_username" id="edit_vet_username" placeholder="Username" required class="p-2 border border-slate-200 rounded-md w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <input type="password" name="vet_password" id="edit_vet_password" placeholder="Add password to change it" class="p-2 border border-slate-200 rounded-md w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">

                        <div class="flex justify-between gap-3 mt-3">
                            <button type="submit" name="update_vet" class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 w-full text-sm transition-colors">
                                Update Veterinarian
                            </button>
                            <button type="button" id="closeEditModal" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 w-full text-sm transition-colors">
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

        const recordsBtn = document.getElementById('recordsBtn');
        const recordsMenu = document.getElementById('recordsMenu');
        const recordsArrow = document.getElementById('recordsArrow');

        recordsBtn.addEventListener('click', () => {
            if (recordsMenu.classList.contains('max-h-0')) {
                recordsMenu.classList.remove('max-h-0', 'opacity-0');
                recordsMenu.classList.add('max-h-96', 'opacity-100');
            } else {
                recordsMenu.classList.remove('max-h-96', 'opacity-100');
                recordsMenu.classList.add('max-h-0', 'opacity-0');
            }
            recordsArrow.classList.toggle('rotate-180');
        });

        // Prevent submenu links from toggling the dropdown
        const submenuLinks = document.querySelectorAll('#recordsMenu a');
        submenuLinks.forEach(link => {
            link.addEventListener('click', (event) => {
                event.stopPropagation(); // Prevent click from bubbling up to recordsBtn
            });
        });
    </script>

    <script src="../js/dashboard.js"></script>
    <script src="../js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/edit-profile.js"></script>
    <script src="../js/profile-dropdown.js"></script>
    <script src="../js/confirmLogout.js"></script>
</body>

</html>
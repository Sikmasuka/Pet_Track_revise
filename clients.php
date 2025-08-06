<?php
require_once './functions/clients-handler.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients</title>
    <script src="Assets/Extension.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .mobile-menu-hidden {
            transform: translateX(-100%);
        }

        .mobile-menu-visible {
            transform: translateX(0);
        }

        .table-container {
            overflow-x: auto;
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
            <a href="dashboard.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="clients.php" class="block text-sm text-white bg-slate-700 px-4 py-2 rounded-md">
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

    <!-- Main Content -->
    <div class="ml-0 lg:ml-52 p-4 pt-16 lg:pt-4">
        <header class="bg-slate-800 rounded-lg text-white py-4 shadow-sm mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-700">
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
        </header>

        <main class="bg-slate-800 p-4 lg:p-6 rounded-lg shadow-sm border border-slate-700">
            <?php if (isset($error)): ?>
                <div class="bg-red-900/50 border-l-4 border-red-500 text-red-200 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-white mb-4">Clients</h2>

            <?php if (count($clients) > 0): ?>
                <div class="table-container">
                    <table class="min-w-full divide-y divide-slate-700">
                        <thead class="bg-slate-700 sticky top-0 z-5">
                            <tr class="border-b border-slate-600">
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Name</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Address</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Contact Number</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-800 divide-y divide-slate-700">
                            <?php foreach ($clients as $client): ?>
                                <tr class="hover:bg-slate-700/50 transition-colors">
                                    <td class="px-4 py-2 text-sm text-slate-200"><?= htmlspecialchars($client['client_name']) ?></td>
                                    <td class="px-4 py-2 text-sm text-slate-300"><?= htmlspecialchars($client['client_address']) ?></td>
                                    <td class="px-4 py-2 text-sm text-slate-300"><?= htmlspecialchars($client['client_contact_number']) ?></td>
                                    <td class="px-4 py-2 text-sm">
                                        <a href="?edit_client_id=<?= (int)$client['client_id'] ?>" class="text-indigo-400 hover:text-indigo-300 hover:underline">Edit</a> |
                                        <a href="#" onclick="confirmDelete(<?= (int)$client['client_id'] ?>)" class="text-red-400 hover:text-red-300 hover:underline">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-slate-400 text-sm sm:text-base">No clients added yet.</p>
            <?php endif; ?>

            <button onclick="showClientModal('add')" class="mt-6 bg-indigo-600 text-white px-4 py-2 font-semibold rounded-md hover:bg-indigo-700 transition-colors text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>Add New Client
            </button>
        </main>
    </div>

    <!-- Add/Edit Client & Pet Modal -->
    <div id="clientModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-slate-800 rounded-lg shadow-lg w-11/12 max-w-3xl max-h-[70vh] overflow-hidden flex flex-col border border-slate-700">
            <div class="w-full bg-slate-700 rounded-t-lg text-white border-b border-slate-600">
                <h3 id="modalTitle" class="text-lg font-bold text-center py-2">Add New Client & Pet</h3>
            </div>
            <form id="clientForm" method="POST" class="p-4 overflow-y-auto">
                <input type="hidden" name="client_id" id="client_id">
                <input type="hidden" name="pet_id" id="pet_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Client Information -->
                    <div>
                        <h4 class="text-sm font-bold text-slate-300 mb-2">Client Information</h4>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Client Name</label>
                            <input type="text" name="client_name" id="clientName" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Address</label>
                            <input type="text" name="client_address" id="clientAddress" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Contact Number</label>
                            <input type="tel" name="client_contact_number" id="clientContactNumber" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required pattern="[0-9]{10,}">
                        </div>
                    </div>

                    <!-- Pet Information -->
                    <div>
                        <h4 class="text-sm font-bold text-slate-300 mb-2">Pet Information</h4>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Pet Name</label>
                            <input type="text" name="pet_name" id="petName" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Species</label>
                            <select name="pet_species" id="petSpecies" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                                <option value="">Select</option>
                                <option value="Dog">Dog</option>
                                <option value="Cat">Cat</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Pet Sex</label>
                            <select name="pet_sex" id="petSex" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Pet Breed</label>
                            <input type="text" name="pet_breed" id="petBreed" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Pet Weight (kg)</label>
                            <input type="number" name="pet_weight" id="petWeight" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Birth Date</label>
                            <input type="date" name="pet_birth_date" id="petBirthDate" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between mt-4 pt-2 border-t border-slate-700">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors text-sm">Save</button>
                    <button type="button" onclick="hideModal()" class="text-slate-400 hover:text-white text-sm">Cancel</button>
                </div>
                <input type="hidden" name="add_client" id="formAction" value="1">
            </form>
        </div>
    </div>

    <script>
        function showClientModal(action) {
            const modal = document.getElementById('clientModal');
            const form = document.getElementById('clientForm');
            const modalTitle = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');

            form.reset();
            form.querySelector('input[name="update_client"]')?.remove();
            formAction.name = 'add_client';
            modalTitle.textContent = 'Add New Client & Pet';

            if (action === 'edit') {
                modalTitle.textContent = 'Edit Client & Pet';
                formAction.name = 'update_client';
            }

            modal.classList.remove('hidden');

            // Clean URL after showing modal
            const url = new URL(window.location.href);
            url.searchParams.delete('edit_client_id');
            window.history.replaceState({}, document.title, url);
        }

        function hideModal() {
            document.getElementById('clientModal').classList.add('hidden');
            // Clean URL when closing modal
            const url = new URL(window.location.href);
            url.searchParams.delete('edit_client_id');
            window.history.replaceState({}, document.title, url);
        }

        function confirmDelete(clientId) {
            if (typeof Swal === 'undefined') {
                // Fallback if SweetAlert2 fails to load
                if (confirm('Are you sure you want to delete this client and their associated pets?')) {
                    window.location.href = `?delete_client_id=${clientId}`;
                }
                return false;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will also delete all associated pets. You won\'t be able to revert this!',
                icon: 'warning',
                background: '#1e293b',
                color: '#e2e8f0',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete client and pets!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?delete_client_id=${clientId}`;
                }
            });
            return false;
        }

        // Show SweetAlert2 for success messages on page load
        <?php if (isset($_GET['message'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Success',
                        text: <?= json_encode($_GET['message']) ?>,
                        icon: 'success',
                        background: '#1e293b',
                        color: '#e2e8f0',
                        confirmButtonColor: '#6366f1',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Clean URL after showing the success message
                        const url = new URL(window.location.href);
                        url.searchParams.delete('message');
                        window.history.replaceState({}, document.title, url);
                    });
                } else {
                    // Fallback to alert if SweetAlert2 is not loaded
                    alert(<?= json_encode($_GET['message']) ?>);
                    // Clean URL
                    const url = new URL(window.location.href);
                    url.searchParams.delete('message');
                    window.history.replaceState({}, document.title, url);
                }
            });
        <?php endif; ?>

        <?php if ($clientToEdit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showClientModal('edit');
                // Set client values
                document.getElementById('client_id').value = <?= json_encode($clientToEdit['client_id'] ?? '') ?>;
                document.getElementById('clientName').value = <?= json_encode($clientToEdit['client_name'] ?? '') ?>;
                document.getElementById('clientAddress').value = <?= json_encode($clientToEdit['client_address'] ?? '') ?>;
                document.getElementById('clientContactNumber').value = <?= json_encode($clientToEdit['client_contact_number'] ?? '') ?>;
                // Set pet values if exists
                <?php if ($petToEdit): ?>
                    document.getElementById('pet_id').value = <?= json_encode($petToEdit['pet_id'] ?? '') ?>;
                    document.getElementById('petName').value = <?= json_encode($petToEdit['pet_name'] ?? '') ?>;
                    document.getElementById('petSex').value = <?= json_encode($petToEdit['pet_sex'] ?? '') ?>;
                    document.getElementById('petBreed').value = <?= json_encode($petToEdit['pet_breed'] ?? '') ?>;
                    document.getElementById('petWeight').value = <?= json_encode($petToEdit['pet_weight'] ?? '') ?>;
                    document.getElementById('petBirthDate').value = <?= json_encode($petToEdit['pet_birth_date'] ?? '') ?>;
                <?php endif; ?>
            });
        <?php endif; ?>
    </script>
    <script src="./js/profile-dropdown.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
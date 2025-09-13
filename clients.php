<?php
require_once __DIR__ . '/functions/clients-handler.php';
include "includes/sitemap/Help/support.php";

// Fetch vet data for modal
$stmt = $pdo->prepare("SELECT * FROM veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$vet = $stmt->fetch(PDO::FETCH_ASSOC);

ob_end_flush();
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

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #edf2f7;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen text-gray-800">
    <?php include('./includes/edit-profile.php'); ?>

    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-teal-700 text-white p-3 rounded-md shadow-lg hover:bg-teal-600 transition-colors">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-[200px] bg-gradient-to-b from-emerald-600 via-teal-700 to-emerald-800 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col border-r border-teal-800">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl lg:text-2xl font-semibold flex items-center gap-2">
                <img src="image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <!-- Close button (mobile only) -->
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-white hover:text-gray-200 duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-grow mt-8 lg:mt-12 space-y-0.5">
            <a href="dashboard.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="clients.php" class="block text-sm text-white bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-user mr-2"></i> Clients
            </a>
            <a href="pets.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-paw mr-2"></i> Pets
            </a>
            <a href="medical_records.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-file-medical mr-2"></i> Medical Records
            </a>
            <a href="payment_methods.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-credit-card mr-2"></i> Payments
            </a>
            <a href="appointments.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-calendar-days mr-2"></i> Appointments
            </a>
            <a href="archive.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fa-solid fa-box-archive mr-2"></i> Archive
            </a>
            <a href="#" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors" onclick="toggleModal('vetHelpModal')">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
            </a>
        </nav>

        <!-- Logout -->
        <div class="pt-4">
            <a href="logout.php" onclick="confirmLogout(event)" class="block text-md text-white hover:bg-red-600 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Content -->
    <div class="ml-0 lg:ml-52 p-4 pt-16 lg:pt-4">
        <header class="bg-white shadow-lg rounded-lg text-gray-800 py-4 mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-200">
            <!-- Top Section with Dropdown -->
            <div class="flex justify-between items-center">
                <!-- Dashboard Title -->
                <h1 class="text-xl lg:text-2xl font-bold">Manage Clients</h1>

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
                                    <p class="text-sm font-semibold text-gray-800"><?php echo $vetName; ?></p>
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

        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-lg border border-slate-200">
            <?php if (isset($error) || isset($_GET['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($error ?? $_GET['error']) ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($_GET['message']) ?></p>
                </div>
            <?php endif; ?>

            <div class="flex justify-between items-center">
                <h2 class="text-lg sm:text-xl lg:text-xl font-semibold text-gray-800 mb-4">List of Clients</h2>

                <button onclick="showClientModal('add')" class="mt-6 bg-indigo-500 text-white px-4 py-2 font-semibold rounded-md hover:bg-indigo-600 transition-colors text-sm sm:text-base">
                    <i class="fas fa-plus mr-2"></i>Add New Client
                </button>
            </div>

            <!-- Search Bar -->
            <form method="GET" class="mb-4">
                <label for="search" class="text-sm font-medium text-gray-700 mr-2">Search Clients:</label>
                <input type="text" name="search" id="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="border border-gray-300 rounded-lg px-4 py-1 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none" placeholder="Search by name, address, or contact..." onchange="this.form.submit()">
            </form>

            <?php
            // Apply search filter to clients
            $clients = $clients ?? [];
            if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
                $searchTerm = trim($_GET['search']);
                $clients = array_filter($clients, function ($client) use ($searchTerm) {
                    return stripos($client['client_name'], $searchTerm) !== false ||
                        stripos($client['client_address'], $searchTerm) !== false ||
                        stripos($client['client_contact_number'], $searchTerm) !== false;
                });
            }
            ?>

            <?php if (count($clients) > 0): ?>
                <div class="table-container">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-gray-300 sticky top-0 z-2">
                            <tr class="border-b border-slate-200">
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Name</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Address</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Contact Number</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php foreach ($clients as $client): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-2 text-sm text-gray-700"><?= htmlspecialchars($client['client_name']) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-600"><?= htmlspecialchars($client['client_address']) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-600"><?= htmlspecialchars($client['client_contact_number']) ?></td>
                                    <td class="px-4 py-2 text-sm">
                                        <a href="?edit_client_id=<?= (int)$client['client_id'] ?>" class="text-indigo-500 hover:text-indigo-400 hover:underline">Edit</a> |
                                        <a href="#" onclick="confirmDelete(<?= (int)$client['client_id'] ?>)" class="text-red-500 hover:text-red-400 hover:underline">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 text-sm sm:text-base">No clients found.</p>
            <?php endif; ?>
        </main>
    </div>

    <!-- Add/Edit Client, Pet & Medical Record Modal -->
    <div id="clientModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-4xl max-h-[80vh] overflow-hidden flex flex-col border border-teal-800">
            <div class="w-full bg-gradient-to-r from-emerald-600 to-teal-700 rounded-t-lg text-gray-800 border-b border-teal-800">
                <h3 id="modalTitle" class="text-lg font-bold text-center py-2 text-white">Add New Client, Pet & Medical Record</h3>
            </div>
            <form id="clientForm" method="POST" class="p-4 overflow-y-auto custom-scrollbar">
                <input type="hidden" name="client_id" id="client_id">
                <input type="hidden" name="pet_id" id="pet_id">
                <input type="hidden" name="record_id" id="record_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Client Information -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-700 mb-2">Client Information</h4>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Client Name</label>
                            <input type="text" name="client_name" id="clientName" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Address</label>
                            <input type="text" name="client_address" id="clientAddress" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Contact Number</label>
                            <input type="tel" name="client_contact_number" id="clientContactNumber" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" pattern="[0-9]{10,}" required>
                        </div>
                    </div>

                    <!-- Pet Information -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-700 mb-2">Pet Information</h4>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Pet Name</label>
                            <input type="text" name="pet_name" id="petName" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Species
                                <span id="speciesTooltip" class="text-xs text-gray-400 hidden">(Cannot be changed)</span>
                            </label>
                            <select name="pet_species" id="petSpecies" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Select</option>
                                <option value="Dog">Dog</option>
                                <option value="Cat">Cat</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Pet Sex
                                <span id="sexTooltip" class="text-xs text-gray-400 hidden">(Cannot be changed)</span>
                            </label>
                            <select name="pet_sex" id="petSex" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Pet Breed</label>
                            <input type="text" name="pet_breed" id="petBreed" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Pet Weight (kg)</label>
                            <input type="number" name="pet_weight" id="petWeight" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Birth Date</label>
                            <input type="date" name="pet_birth_date" id="petBirthDate" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Medical Record Information -->
                <div class="mt-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-2">Medical Record Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-3">
                                <label class="block text-xs text-gray-500 mb-1">Condition</label>
                                <textarea name="medical_condition" id="medicalCondition" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs text-gray-500 mb-1">Diagnosis</label>
                                <textarea name="medical_diagnosis" id="medicalDiagnosis" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                            </div>
                        </div>
                        <div>
                            <div class="mb-3">
                                <label class="block text-xs text-gray-500 mb-1">Symptoms</label>
                                <textarea name="medical_symptoms" id="medicalSymptoms" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs text-gray-500 mb-1">Treatment</label>
                                <textarea name="medical_treatment" id="medicalTreatment" class="w-full p-2 border border-slate-200 rounded-md text-sm bg-gray-50 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between mt-4 pt-2 border-t border-slate-200">
                    <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition-colors text-sm">Save</button>
                    <button type="button" onclick="hideModal()" class="text-gray-500 hover:text-gray-700 text-sm">Cancel</button>
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
            const speciesTooltip = document.getElementById('speciesTooltip');
            const sexTooltip = document.getElementById('sexTooltip');

            // Reset form but preserve existing values for edit mode
            form.querySelector('input[name="update_client"]')?.remove();
            // Enable dropdowns and hide tooltips by default
            document.getElementById('petSpecies').disabled = false;
            document.getElementById('petSex').disabled = false;
            speciesTooltip.classList.add('hidden');
            sexTooltip.classList.add('hidden');

            // Clear required attributes for all fields initially
            document.getElementById('petName').removeAttribute('required');
            document.getElementById('petSpecies').removeAttribute('required');
            document.getElementById('petSex').removeAttribute('required');
            document.getElementById('petBreed').removeAttribute('required');
            document.getElementById('petWeight').removeAttribute('required');
            document.getElementById('petBirthDate').removeAttribute('required');
            document.getElementById('medicalCondition').removeAttribute('required');
            document.getElementById('medicalDiagnosis').removeAttribute('required');
            document.getElementById('medicalSymptoms').removeAttribute('required');
            document.getElementById('medicalTreatment').removeAttribute('required');

            if (action === 'add') {
                form.reset(); // Clear form for adding new client
                formAction.name = 'add_client';
                modalTitle.textContent = 'Add New Client, Pet, and Medical Record';
                // Set required attributes for add mode
                document.getElementById('petName').setAttribute('required', '');
                document.getElementById('petSpecies').setAttribute('required', '');
                document.getElementById('petSex').setAttribute('required', '');
                document.getElementById('petBreed').setAttribute('required', '');
                document.getElementById('petWeight').setAttribute('required', '');
                document.getElementById('petBirthDate').setAttribute('required', '');
                document.getElementById('medicalCondition').setAttribute('required', '');
                document.getElementById('medicalDiagnosis').setAttribute('required', '');
                document.getElementById('medicalSymptoms').setAttribute('required', '');
                document.getElementById('medicalTreatment').setAttribute('required', '');
            }

            if (action === 'edit') {
                modalTitle.textContent = 'Edit Client, Pet, and Medical Record';
                formAction.name = 'update_client';
                // Debugging: Log pet_id when opening edit modal
                console.log('Opening edit modal with pet_id:', document.getElementById('pet_id').value);
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
                if (confirm('Are you sure you want to delete this client, their associated pets, and medical records?')) {
                    window.location.href = `?delete_client_id=${clientId}`;
                }
                return false;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will also delete all associated pets and medical records. You won\'t be able to revert this!',
                icon: 'warning',
                background: '#1e293b',
                color: '#e2e8f0',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete client, pets, and records!'
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

        // Populate fields for edit mode
        <?php if ($clientToEdit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Client to edit:', <?= json_encode($clientToEdit) ?>); // Debug client data
                console.log('Pet to edit:', <?= json_encode($petToEdit) ?>); // Debug pet data
                console.log('Medical record to edit:', <?= json_encode($medicalRecordToEdit) ?>); // Debug medical record data
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
                    document.getElementById('petSpecies').value = <?= json_encode($petToEdit['pet_species'] ?? '') ?>;
                    document.getElementById('petSpecies').disabled = false;
                    document.getElementById('petSex').disabled = false;
                    document.getElementById('speciesTooltip').classList.add('hidden');
                    document.getElementById('sexTooltip').classList.add('hidden');
                    // Set required attributes for pet fields when pet exists
                    document.getElementById('petName').setAttribute('required', '');
                    document.getElementById('petSpecies').setAttribute('required', '');
                    document.getElementById('petSex').setAttribute('required', '');
                    document.getElementById('petBreed').setAttribute('required', '');
                    document.getElementById('petWeight').setAttribute('required', '');
                    document.getElementById('petBirthDate').setAttribute('required', '');
                <?php else: ?>
                    // Clear pet fields if no pet exists
                    document.getElementById('pet_id').value = '';
                    document.getElementById('petName').value = '';
                    document.getElementById('petSex').value = '';
                    document.getElementById('petBreed').value = '';
                    document.getElementById('petWeight').value = '';
                    document.getElementById('petBirthDate').value = '';
                    document.getElementById('petSpecies').value = '';
                    document.getElementById('petSpecies').disabled = false;
                    document.getElementById('petSex').disabled = false;
                    document.getElementById('speciesTooltip').classList.add('hidden');
                    document.getElementById('sexTooltip').classList.add('hidden');
                <?php endif; ?>
                // Set medical record values if exists
                <?php if ($medicalRecordToEdit): ?>
                    document.getElementById('record_id').value = <?= json_encode($medicalRecordToEdit['record_id'] ?? '') ?>;
                    document.getElementById('medicalCondition').value = <?= json_encode($medicalRecordToEdit['medical_condition'] ?? '') ?>;
                    document.getElementById('medicalDiagnosis').value = <?= json_encode($medicalRecordToEdit['medical_diagnosis'] ?? '') ?>;
                    document.getElementById('medicalSymptoms').value = <?= json_encode($medicalRecordToEdit['medical_symptoms'] ?? '') ?>;
                    document.getElementById('medicalTreatment').value = <?= json_encode($medicalRecordToEdit['medical_treatment'] ?? '') ?>;
                    // Set required attributes for medical record fields when record exists
                    document.getElementById('medicalCondition').setAttribute('required', '');
                    document.getElementById('medicalDiagnosis').setAttribute('required', '');
                    document.getElementById('medicalSymptoms').setAttribute('required', '');
                    document.getElementById('medicalTreatment').setAttribute('required', '');
                <?php else: ?>
                    // Clear medical record fields if no record exists
                    document.getElementById('record_id').value = '';
                    document.getElementById('medicalCondition').value = '';
                    document.getElementById('medicalDiagnosis').value = '';
                    document.getElementById('medicalSymptoms').value = '';
                    document.getElementById('medicalTreatment').value = '';
                <?php endif; ?>
            });
        <?php endif; ?>

        function toggleModal(modalId) {
            console.log("Toggling modal:", modalId); // Debug log
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle('hidden');
            } else {
                console.error("Modal not found:", modalId);
            }
        }

        document.getElementById('clientForm').addEventListener('submit', function(event) {
            const medicalFields = ['medicalCondition', 'medicalDiagnosis', 'medicalSymptoms', 'medicalTreatment'];
            const petFields = ['petName', 'petSpecies', 'petSex', 'petBreed', 'petWeight', 'petBirthDate'];
            const petId = document.getElementById('pet_id').value.trim();
            const hasPetData = petFields.some(id => document.getElementById(id).value.trim());

            // Debugging: Log form data
            console.log('Submitting form with pet_id:', petId);
            console.log('Pet fields:', petFields.map(id => ({
                [id]: document.getElementById(id).value.trim()
            })));

            // If any pet field is filled, all pet fields must be filled
            if (hasPetData) {
                for (const id of petFields) {
                    if (!document.getElementById(id).value.trim()) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Error',
                            text: 'All pet fields are required if any pet field is filled.',
                            icon: 'error',
                            background: '#1e293b',
                            color: '#e2e8f0',
                            confirmButtonColor: '#6366f1'
                        });
                        return;
                    }
                }
            }

            // If any medical field is filled, all medical fields must be filled
            const hasMedicalData = medicalFields.some(id => document.getElementById(id).value.trim());
            if (hasMedicalData) {
                for (const id of medicalFields) {
                    if (!document.getElementById(id).value.trim()) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Error',
                            text: 'All medical record fields are required if any medical field is filled.',
                            icon: 'error',
                            background: '#1e293b',
                            color: '#e2e8f0',
                            confirmButtonColor: '#6366f1'
                        });
                        return;
                    }
                }
            }
        });
    </script>

    <!-- scripts -->
    <script src="./js/dashboard.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
    <script src="./js/edit-profile.js"></script>
</body>

</html>
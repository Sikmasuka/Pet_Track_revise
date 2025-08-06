<?php
// Start session and include database connection
session_start();
require_once __DIR__ . "/db.php";

// Check if user is logged in by verifying vet_id session
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch vet name for greeting
$stmt = $pdo->prepare("SELECT vet_name FROM Veterinarian WHERE vet_id=?");
$stmt->execute([$_SESSION['vet_id']]);
$user = $stmt->fetch();
$vetName = $user ? htmlspecialchars($user['vet_name']) : "Veterinarian not found";


/**
 * Handle adding and updating pets via POST requests
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_pet'])) {
        // Update existing pet
        $stmt = $pdo->prepare("UPDATE Pet SET pet_name=?, pet_sex=?, pet_weight=?, pet_breed=?, pet_birth_date=?, pet_species=?, client_id=? WHERE pet_id=?");
        $stmt->execute([$_POST['pet_name'], $_POST['pet_sex'], $_POST['pet_weight'], $_POST['pet_breed'], $_POST['pet_birth_date'], $_POST['pet_species'], $_POST['client_id'], $_POST['pet_id']]);
        header('Location: pets.php');
        exit;
    }
}

/**
 * Handle deleting a pet via GET request
 */
if (isset($_GET['delete_pet_id'])) {
    $stmt = $pdo->prepare("DELETE FROM Pet WHERE pet_id=?");
    $stmt->execute([$_GET['delete_pet_id']]);
    header('Location: pets.php');
    exit;
}

/**
 * Fetch pet data for editing if edit_pet_id is set
 */
$petToEdit = null;
$clientName = null;
if (isset($_GET['edit_pet_id'])) {
    $stmt = $pdo->prepare("
        SELECT Pet.*, Client.client_name 
        FROM Pet 
        JOIN Client ON Pet.client_id = Client.client_id 
        WHERE Pet.pet_id = ?
    ");
    $stmt->execute([$_GET['edit_pet_id']]);
    $petToEdit = $stmt->fetch();
    $clientName = $petToEdit ? $petToEdit['client_name'] : null;
}

/**
 * Fetch all pets joined with client names, ordered by pet name
 */
$stmt = $pdo->prepare("
    SELECT Pet.pet_id, Pet.pet_name, Pet.pet_sex, Pet.pet_weight, Pet.pet_breed, Pet.pet_birth_date, Pet.pet_species, Client.client_name 
    FROM Pet 
    JOIN Client ON Pet.client_id = Client.client_id WHERE Client.status = 1
    ORDER BY Pet.pet_name ASC
");
$stmt->execute();
$pets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pets</title>
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
            <a href="clients.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-user mr-2"></i> Clients
            </a>
            <a href="pets.php" class="block text-sm text-white bg-slate-700 px-4 py-2 rounded-md">
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
    <div class=" lg:ml-52 p-4 pt-16 lg:pt-4">
        <header class="bg-slate-800 rounded-lg text-white py-4 shadow-sm mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-700">
            <!-- Top Section with Dropdown -->
            <div class="flex justify-between items-center mb-6">

                <!-- Dashboard Title -->
                <h1 class="text-xl lg:text-2xl font-bold">Pets</h1>

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
            <!-- Pets Section -->
            <h2 class="lg:text-2xl text-xl font-bold text-white mb-4">Pets</h2>

            <?php if (count($pets) > 0): ?>
                <div class="overflow-y-auto max-h-96">
                    <table class="min-w-full divide-y divide-slate-700">
                        <thead class="bg-slate-700 sticky top-0 z-2">
                            <tr class="border-b border-slate-600">
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Pet Name</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Species</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Pet Sex</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Breed</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Weight</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Birth Date</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Client</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-800 divide-y divide-slate-700">
                            <?php foreach ($pets as $pet): ?>
                                <tr class="border-b border-slate-700 hover:bg-slate-700/50 transition-colors">
                                    <td class="px-4 py-2 text-slate-200"><?= htmlspecialchars($pet['pet_name']) ?></td>
                                    <td class="px-4 py-2 text-slate-300"><?= htmlspecialchars($pet['pet_species']) ?></td>
                                    <td class="px-4 py-2 text-slate-300"><?= htmlspecialchars($pet['pet_sex']) ?></td>
                                    <td class="px-4 py-2 text-slate-300"><?= htmlspecialchars($pet['pet_breed']) ?></td>
                                    <td class="px-4 py-2 text-slate-300"><?= htmlspecialchars($pet['pet_weight']) ?> kg</td>
                                    <td class="px-4 py-2 text-slate-300"><?= htmlspecialchars($pet['pet_birth_date']) ?></td>
                                    <td class="px-4 py-2 text-slate-300"><?= htmlspecialchars($pet['client_name']) ?></td>
                                    <td class="px-4 py-2">
                                        <a href="?edit_pet_id=<?= (int)$pet['pet_id'] ?>" class="text-indigo-400 hover:text-indigo-300 hover:underline">Edit</a> |
                                        <a href="#" onclick="confirmDelete(<?= (int)$pet['pet_id'] ?>)" class="text-red-400 hover:text-red-300 hover:underline">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-slate-400 text-sm sm:text-base">No pets added yet.</p>
            <?php endif; ?>
        </main>
    </div>

    <!-- Add/Edit Pet Modal -->
    <div id="petModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-slate-800 rounded-lg shadow-lg w-full max-w-md border border-slate-700">
            <div class="w-full bg-slate-700 rounded-t-lg text-white border-b border-slate-600">
                <h3 id="petModalTitle" class="text-lg sm:text-xl lg:text-2xl font-bold text-center text-white m-0 py-3">
                    Edit Pet
                </h3>
            </div>
            <form id="petForm" method="POST" class="p-4 sm:p-6">
                <input type="hidden" name="pet_id" id="pet_id" value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_id']) : '' ?>">
                <input type="hidden" name="update_pet" value="1">
                <input type="hidden" name="client_id" id="clientId" value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['client_id']) : '' ?>">
                <div class="mb-4">
                    <label class="block text-md font-semibold text-slate-300">Pet Name</label>
                    <input type="text" name="pet_name" id="petName" class="w-full p-2 border border-slate-700 rounded-md bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_name']) : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-slate-300">Pet Sex</label>
                    <select name="pet_sex" id="petSex" class="w-full p-2 border border-slate-700 rounded-md bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        <option value="Male" <?= isset($petToEdit) && $petToEdit['pet_sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= isset($petToEdit) && $petToEdit['pet_sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-slate-300">Pet Breed</label>
                    <input type="text" name="pet_breed" id="petBreed" class="w-full p-2 border border-slate-700 rounded-md bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_breed']) : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-slate-300">Pet Weight (kg)</label>
                    <input type="number" name="pet_weight" id="petWeight" class="w-full p-2 border border-slate-700 rounded-md bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_weight']) : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-slate-300">Birth Date</label>
                    <input type="date" name="pet_birth_date" id="petBirthDate" class="w-full p-2 border border-slate-700 rounded-md bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_birth_date']) : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-slate-300">Owner</label>
                    <input type="text" id="clientName" class="w-full p-2 border border-slate-700 rounded-md bg-slate-700/50 text-slate-300"
                        value="<?= isset($clientName) ? htmlspecialchars($clientName) : '' ?>" readonly>
                </div>
                <div class="flex justify-between pt-4 border-t border-slate-700">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">Save</button>
                    <button type="button" onclick="hidePetModal()" class="text-slate-400 hover:text-white">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Control Script -->
    <script>
        // Show pet modal
        function showPetModal() {
            document.getElementById('petModal').classList.remove('hidden');
        }

        // Hide pet modal
        function hidePetModal() {
            document.getElementById('petModal').classList.add('hidden');
            // Clear URL parameter
            const url = new URL(window.location.href);
            url.searchParams.delete('edit_pet_id');
            window.history.replaceState({}, document.title, url.pathname);
        }

        // Confirm delete function
        function confirmDelete(petId) {
            if (typeof Swal === 'undefined') {
                if (confirm('Are you sure you want to delete this pet?')) {
                    window.location.href = `?delete_pet_id=${petId}`;
                }
                return false;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                background: '#1e293b',
                color: '#e2e8f0',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?delete_pet_id=${petId}`;
                }
            });
            return false;
        }

        // Show modal if editing
        <?php if ($petToEdit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showPetModal();
            });
        <?php endif; ?>
    </script>

    <script src="./js/profile-dropdown.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
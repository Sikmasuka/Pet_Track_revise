<?php
// Start session and include database connection
session_start();
require_once 'db.php';

// Check if user is logged in by verifying vet_id session
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

/**
 * Handle adding and updating pets via POST requests
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_pet'])) {
        // Update existing pet
        $stmt = $pdo->prepare("UPDATE Pet SET pet_name=?, pet_sex=?, pet_weight=?, pet_breed=?, pet_birth_date=?, client_id=? WHERE pet_id=?");
        $stmt->execute([$_POST['pet_name'], $_POST['pet_sex'], $_POST['pet_weight'], $_POST['pet_breed'], $_POST['pet_birth_date'], $_POST['client_id'], $_POST['pet_id']]);
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
    SELECT Pet.pet_id, Pet.pet_name, Pet.pet_sex, Pet.pet_weight, Pet.pet_breed, Pet.pet_birth_date, Client.client_name 
    FROM Pet 
    JOIN Client ON Pet.client_id = Client.client_id 
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
</head>

<body class="bg-gray-100 flex">
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-green-600 text-white p-3 rounded-md shadow-lg">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-green-500 to-green-600 text-white p-4 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40">
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
            <a href="dashboard.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-tachometer-alt mr-2"></i><span class="md:inline">Dashboard</span>
            </a>
            <a href="clients.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-user mr-2"></i><span class="md:inline">Clients</span>
            </a>
            <a href="pets.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-paw mr-2"></i><span class="md:inline">Pets</span>
            </a>
            <a href="medical_records.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-file-medical mr-2"></i><span class="md:inline">Medical Records</span>
            </a>
            <a href="profile.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-id-badge mr-2"></i><span class="md:inline">Profile</span>
            </a>
            <a href="payment_methods.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-credit-card mr-2"></i><span class="md:inline">Payments</span>
            </a>
            <a href="#" onclick="confirmLogout(event)" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i><span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Content -->
    <div class="ml-0 lg:ml-64 p-4 lg:p-8 pt-16 lg:pt-4 min-h-screen w-full">
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-8 p-8">
            <div class="flex justify-between items-center">
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold">
                    Hello,
                    <?php
                    $stmt = $pdo->prepare("SELECT vet_name FROM veterinarian WHERE vet_id=?");
                    $stmt->execute([$_SESSION['vet_id']]);
                    $user = $stmt->fetch();
                    echo $user ? htmlspecialchars($user['vet_name']) : "Veterinarian not found.";
                    ?>.
                </h1>
                <h1 class="lg:text-3xl md:text-2xl text-xl font-bold">Manage Pets</h1>
            </div>
        </header>

        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-sm">
            <!-- Pets Section -->
            <h2 class="lg:text-2xl text-xl font-bold text-green-800 mb-4">Pets</h2>

            <?php if (count($pets) > 0): ?>
                <div class="overflow-y-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-50 sticky top-0 z-2">
                            <tr class="border-b bg-gray-200">
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Pet Name</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Pet Sex</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Breed</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Weight</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Birth Date</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Client</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($pets as $pet): ?>
                                <tr class="border-b hover:bg-gray-100">
                                    <td class="px-4 py-2"><?= htmlspecialchars($pet['pet_name']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($pet['pet_sex']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($pet['pet_breed']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($pet['pet_weight']) ?> kg</td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($pet['pet_birth_date']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($pet['client_name']) ?></td>
                                    <td class="px-4 py-2">
                                        <a href="?edit_pet_id=<?= $pet['pet_id'] ?>" class="text-blue-500 hover:underline">Edit</a> |
                                        <a href="?delete_pet_id=<?= $pet['pet_id'] ?>" onclick="return confirm('Delete this pet?')" class="text-red-500 hover:underline">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-700 text-sm sm:text-base">No pets added yet.</p>
            <?php endif; ?>
        </main>
    </div>

    <!-- Add/Edit Pet Modal -->
    <div id="petModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
            <div class="w-full bg-green-500 rounded-t-lg text-white">
                <h3 id="petModalTitle" class="text-lg sm:text-xl lg:text-2xl font-bold text-center text-white m-0 py-3">
                    Edit Pet
                </h3>
            </div>
            <form id="petForm" method="POST" class="p-4 sm:p-6">
                <input type="hidden" name="pet_id" id="pet_id" value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_id']) : '' ?>">
                <input type="hidden" name="update_pet" value="1">
                <input type="hidden" name="client_id" id="clientId" value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['client_id']) : '' ?>">
                <div class="mb-4">
                    <label class="block text-md font-semibold text-gray-700">Pet Name</label>
                    <input type="text" name="pet_name" id="petName" class="w-full p-2 border rounded-md"
                        value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_name']) : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-gray-700">Pet Sex</label>
                    <select name="pet_sex" id="petSex" class="w-full p-2 border rounded-md" required>
                        <option value="Male" <?= isset($petToEdit) && $petToEdit['pet_sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= isset($petToEdit) && $petToEdit['pet_sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-gray-700">Pet Breed</label>
                    <input type="text" name="pet_breed" id="petBreed" class="w-full p-2 border rounded-md"
                        value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_breed']) : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-gray-700">Pet Weight (kg)</label>
                    <input type="number" name="pet_weight" id="petWeight" class="w-full p-2 border rounded-md"
                        value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_weight']) : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-gray-700">Birth Date</label>
                    <input type="date" name="pet_birth_date" id="petBirthDate" class="w-full p-2 border rounded-md"
                        value="<?= isset($petToEdit) ? htmlspecialchars($petToEdit['pet_birth_date']) : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-md font-semibold text-gray-700">Owner</label>
                    <input type="text" id="clientName" class="w-full p-2 border rounded-md bg-gray-100"
                        value="<?= isset($clientName) ? htmlspecialchars($clientName) : '' ?>" readonly>
                </div>
                <div class="flex justify-between">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Save</button>
                    <button type="button" onclick="hidePetModal()" class="text-gray-500">Cancel</button>
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

        // Show modal if editing
        <?php if ($petToEdit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showPetModal();
            });
        <?php endif; ?>
    </script>

    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
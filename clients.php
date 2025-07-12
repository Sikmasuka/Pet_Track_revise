<?php
// Start session and include database connection
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

/**
 * Input validation function
 */
function validateInput($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Handle adding and updating clients via POST requests
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = validateInput($_POST['client_name'] ?? '');
    $client_address = validateInput($_POST['client_address'] ?? '');
    $client_contact = validateInput($_POST['client_contact_number'] ?? '');

    $pet_name = validateInput($_POST['pet_name'] ?? '');
    $pet_sex = $_POST['pet_sex'] ?? '';
    $pet_weight = $_POST['pet_weight'] ?? '';
    $pet_breed = validateInput($_POST['pet_breed'] ?? '');
    $pet_birth_date = $_POST['pet_birth_date'] ?? '';

    // Basic validation
    if (empty($client_name) || empty($client_address) || empty($client_contact)) {
        $error = "All client fields are required";
    } elseif (empty($pet_name) || empty($pet_sex) || empty($pet_weight) || empty($pet_breed) || empty($pet_birth_date)) {
        $error = "All pet fields are required";
    } else {
        try {
            if (isset($_POST['add_client'])) {
                // Insert new client
                $stmt = $pdo->prepare("INSERT INTO Client (client_name, client_address, client_contact_number) VALUES (?, ?, ?)");
                $stmt->execute([$client_name, $client_address, $client_contact]);

                // Get the last inserted client ID
                $client_id = $pdo->lastInsertId();

                // Insert new pet
                $stmt = $pdo->prepare("INSERT INTO Pet (pet_name, pet_sex, pet_weight, pet_breed, pet_birth_date, client_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$pet_name, $pet_sex, $pet_weight, $pet_breed, $pet_birth_date, $client_id]);

                header('Location: clients.php?message=Client and pet added successfully');
                exit;
            } elseif (isset($_POST['update_client'])) {
                $client_id = (int)$_POST['client_id'];
                $pet_id = (int)$_POST['pet_id'];

                // Update client
                $stmt = $pdo->prepare("UPDATE Client SET client_name=?, client_address=?, client_contact_number=? WHERE client_id=?");
                $stmt->execute([$client_name, $client_address, $client_contact, $client_id]);

                // Update pet
                $stmt = $pdo->prepare("UPDATE Pet SET pet_name=?, pet_sex=?, pet_weight=?, pet_breed=?, pet_birth_date=? WHERE pet_id=? AND client_id=?");
                $stmt->execute([$pet_name, $pet_sex, $pet_weight, $pet_breed, $pet_birth_date, $pet_id, $client_id]);

                header('Location: clients.php?message=Client and pet updated successfully');
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

/**
 * Handle deleting a client via GET request
 */
if (isset($_GET['delete_client_id']) && is_numeric($_GET['delete_client_id'])) {
    try {
        $client_id = (int)$_GET['delete_client_id'];

        // Begin transaction to ensure atomicity
        $pdo->beginTransaction();

        // Delete all pets associated with the client
        $stmt = $pdo->prepare("DELETE FROM Pet WHERE client_id = ?");
        $stmt->execute([$client_id]);

        // Delete the client
        $stmt = $pdo->prepare("DELETE FROM Client WHERE client_id = ?");
        $stmt->execute([$client_id]);

        // Commit transaction
        $pdo->commit();

        header('Location: clients.php?message=Client and associated pets deleted successfully');
        exit;
    } catch (PDOException $e) {
        // Roll back transaction on error
        $pdo->rollBack();
        $error = "Database error: Cannot delete client and pets, possibly due to associated records.";
    }
}

/**
 * Fetch client data for editing
 */
function getDataToEdit($pdo)
{
    $clientToEdit = null;
    $petToEdit = null;
    $error = null;

    if (isset($_GET['edit_client_id']) && is_numeric($_GET['edit_client_id'])) {
        try {
            // Get client info
            $stmt = $pdo->prepare("SELECT * FROM Client WHERE client_id = ?");
            $stmt->execute([(int)$_GET['edit_client_id']]);
            $clientToEdit = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get the first pet for this client
            if ($clientToEdit) {
                $stmt = $pdo->prepare("SELECT * FROM Pet WHERE client_id = ? LIMIT 1");
                $stmt->execute([(int)$_GET['edit_client_id']]);
                $petToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }

    return [
        'client' => $clientToEdit,
        'pet' => $petToEdit,
        'error' => $error
    ];
}

// Get data for editing
$editData = getDataToEdit($pdo);
$clientToEdit = $editData['client'];
$petToEdit = $editData['pet'];
$error = $error ?? $editData['error'];

/**
 * Fetch all clients
 */
try {
    $stmt = $pdo->prepare("SELECT * FROM Client ORDER BY client_name ASC");
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $clients = [];
}
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
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
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
            <a href="clients.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-user mr-2"></i><span class="md:inline">Clients</span>
            </a>
            <a href="pets.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
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
    <div class="ml-0 lg:ml-64 p-4 lg:p-8 pt-16 lg:pt-4">
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-6 lg:mb-8 p-4 lg:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold">
                    Hello,
                    <?php
                    $stmt = $pdo->prepare("SELECT vet_name FROM veterinarian WHERE vet_id=?");
                    $stmt->execute([$_SESSION['vet_id']]);
                    $user = $stmt->fetch();
                    echo $user ? htmlspecialchars($user['vet_name']) : "Veterinarian not found.";
                    ?>.
                </h1>
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold">Manage Clients</h1>
            </div>
        </header>

        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-sm">
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-green-800 mb-4">Clients</h2>

            <?php if (count($clients) > 0): ?>
                <div class="table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-5">
                            <tr class="border-b bg-gray-200">
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Name</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Address</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Contact Number</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($clients as $client): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['client_name']) ?></td>
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['client_address']) ?></td>
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['client_contact_number']) ?></td>
                                    <td class="px-4 py-2 text-sm">
                                        <a href="?edit_client_id=<?= (int)$client['client_id'] ?>" class="text-blue-500 hover:underline">Edit</a> |
                                        <a href="#" onclick="confirmDelete(<?= (int)$client['client_id'] ?>)" class="text-red-500 hover:underline">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-700 text-sm sm:text-base">No clients added yet.</p>
            <?php endif; ?>

            <button onclick="showClientModal('add')" class="mt-6 bg-green-500 text-white px-4 py-2 font-semibold rounded-md hover:bg-green-600 text-sm sm:text-base">
                Add New Client
            </button>
        </main>
    </div>

    <!-- Add/Edit Client & Pet Modal -->
    <div id="clientModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-6xl">
            <div class="w-full bg-green-500 rounded-t-lg text-white">
                <h3 id="modalTitle" class="text-lg sm:text-xl lg:text-2xl font-bold text-center py-3">Add New Client & Pet</h3>
            </div>
            <form id="clientForm" method="POST" class="p-4 sm:p-6">
                <input type="hidden" name="client_id" id="client_id">
                <input type="hidden" name="pet_id" id="pet_id">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Client Information -->
                    <div>
                        <h4 class="text-md font-bold text-gray-700 mb-2">Client Information</h4>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-700">Client Name</label>
                            <input type="text" name="client_name" id="clientName" class="w-full p-2 border rounded-md text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-700">Address</label>
                            <input type="text" name="client_address" id="clientAddress" class="w-full p-2 border rounded-md text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-700">Contact Number</label>
                            <input type="tel" name="client_contact_number" id="clientContactNumber" class="w-full p-2 border rounded-md text-sm" required pattern="[0-9]{10,}">
                        </div>
                    </div>
                    <!-- Pet Information -->
                    <div>
                        <h4 class="text-md font-bold text-gray-700 mb-2">Pet Information</h4>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">Pet Name</label>
                            <input type="text" name="pet_name" id="petName" class="w-full p-2 border rounded-md text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">Pet Sex</label>
                            <select name="pet_sex" id="petSex" class="w-full p-2 border rounded-md text-sm" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">Pet Breed</label>
                            <input type="text" name="pet_breed" id="petBreed" class="w-full p-2 border rounded-md text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">Pet Weight (kg)</label>
                            <input type="number" name="pet_weight" id="petWeight" class="w-full p-2 border rounded-md text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700">Birth Date</label>
                            <input type="date" name="pet_birth_date" id="petBirthDate" class="w-full p-2 border rounded-md text-sm" required>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="flex justify-between mt-6">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 text-sm sm:text-base">Save</button>
                    <button type="button" onclick="hideModal()" class="text-gray-500 text-sm sm:text-base">Cancel</button>
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
                        confirmButtonColor: '#22c55e',
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
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
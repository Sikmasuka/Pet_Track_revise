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
 * Handle adding and updating clients via POST requests
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_client'])) {
        $stmt = $pdo->prepare("INSERT INTO Client (client_name, client_address, client_contact_number) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['client_name'], $_POST['client_address'], $_POST['client_contact_number']]);
        header('Location: clients.php');
        exit;
    } elseif (isset($_POST['update_client'])) {
        $stmt = $pdo->prepare("UPDATE Client SET client_name=?, client_address=?, client_contact_number=? WHERE client_id=?");
        $stmt->execute([$_POST['client_name'], $_POST['client_address'], $_POST['client_contact_number'], $_POST['client_id']]);
        header('Location: clients.php');
        exit;
    }
}

/**
 * Handle deleting a client via GET request
 */
if (isset($_GET['delete_client_id'])) {
    $stmt = $pdo->prepare("DELETE FROM Client WHERE client_id=?");
    $stmt->execute([$_GET['delete_client_id']]);
    header('Location: clients.php');
    exit;
}

/**
 * Fetch client data for editing if edit_client_id is set
 */
$clientToEdit = null;
if (isset($_GET['edit_client_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM Client WHERE client_id=?");
    $stmt->execute([$_GET['edit_client_id']]);
    $clientToEdit = $stmt->fetch();
}

/**
 * Fetch all clients ordered by name
 */
$stmt = $pdo->prepare("SELECT * FROM Client ORDER BY client_name ASC");
$stmt->execute();
$clients = $stmt->fetchAll();
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
        /* Custom styles for mobile menu toggle */
        .mobile-menu-hidden {
            transform: translateX(-100%);
        }

        .mobile-menu-visible {
            transform: translateX(0);
        }

        /* Ensure table container is responsive */
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
        <!-- Close button for mobile -->
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
                <i class="fas fa-tachometer-alt mr-2"></i>
                <span class="md:inline">Dashboard</span>
            </a>
            <a href="clients.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-user mr-2"></i>
                <span class="md:inline">Clients</span>
            </a>
            <a href="pets.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-paw mr-2"></i>
                <span class="md:inline">Pets</span>
            </a>
            <a href="medical_records.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-file-medical mr-2"></i>
                <span class="md:inline">Medical Records</span>
            </a>
            <a href="profile.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-id-badge mr-2"></i>
                <span class="md:inline">Profile</span>
            </a>
            <a href="payment_methods.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-credit-card mr-2"></i>
                <span class="md:inline">Payments</span>
            </a>
            <a href="#" onclick="confirmLogout(event)" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span class="md:inline">Logout</span>
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

                    if ($user) {
                        echo htmlspecialchars($user['vet_name']);
                    } else {
                        echo "Veterinarian not found.";
                    }
                    ?>.
                </h1>
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold">
                    Manage Clients
                </h1>
            </div>
        </header>

        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-sm">
            <!-- Clients Section -->
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-green-800 mb-4">Clients</h2>

            <?php if (count($clients) > 0): ?>
                <div class="table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-5">
                            <tr class="border-b bg-gray-200">
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Name</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Address</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Contact Number</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($clients as $client): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['client_name']) ?></td>
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['client_address']) ?></td>
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['client_contact_number']) ?></td>
                                    <td class="px-4 py-2 text-sm">
                                        <a href="?edit_client_id=<?= $client['client_id'] ?>" class="text-blue-500 hover:underline">Edit</a> |
                                        <a href="?delete_client_id=<?= $client['client_id'] ?>" onclick="return confirm('Delete this client?')" class="text-red-500 hover:underline">Delete</a>
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

    <!-- Add/Edit Client Modal -->
    <div id="clientModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-[500px]">
            <div class="w-full bg-green-500 rounded-t-lg text-white">
                <h3 id="modalTitle" class="text-lg sm:text-xl lg:text-2xl font-bold text-center text-white m-0 py-3">Add New Client</h3>
            </div>

            <form id="clientForm" method="POST" class="p-4 sm:p-6">
                <input type="hidden" name="client_id" id="client_id">
                <div class="mb-4">
                    <label class="block text-xs sm:text-sm text-gray-700">Client Name</label>
                    <input type="text" name="client_name" id="clientName" class="w-full p-2 text-sm border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs sm:text-sm text-gray-700">Address</label>
                    <input type="text" name="client_address" id="clientAddress" class="w-full p-2 text-sm border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs sm:text-sm text-gray-700">Contact Number</label>
                    <input type="text" name="client_contact_number" id="clientContactNumber" class="w-full p-2 text-sm border rounded-md" required>
                </div>
                <div class="flex justify-between">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 text-sm sm:text-base">Save</button>
                    <button type="button" onclick="hideModal()" class="text-gray-500 text-sm sm:text-base">Cancel</button>
                </div>
                <input type="hidden" name="add_client" value="1">
            </form>
        </div>
    </div>

    <script>
        function showClientModal(action) {
            const modal = document.getElementById('clientModal');
            const form = document.getElementById('clientForm');
            if (action === 'add') {
                document.getElementById('modalTitle').textContent = 'Add New Client';
                form.reset();
                form.action = 'clients.php';
                form.querySelector('input[name="add_client"]').value = '1';
                form.querySelector('input[name="update_client"]')?.remove();
            }
            modal.classList.remove('hidden');
        }

        function hideModal() {
            document.getElementById('clientModal').classList.add('hidden');
        }

        <?php if ($clientToEdit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showClientModal('edit');
                document.getElementById('modalTitle').textContent = 'Edit Client';
                document.getElementById('client_id').value = '<?= $clientToEdit['client_id'] ?>';
                document.getElementById('clientName').value = '<?= htmlspecialchars($clientToEdit['client_name']) ?>';
                document.getElementById('clientAddress').value = '<?= htmlspecialchars($clientToEdit['client_address']) ?>';
                document.getElementById('clientContactNumber').value = '<?= htmlspecialchars($clientToEdit['client_contact_number']) ?>';
                const form = document.getElementById('clientForm');
                form.querySelector('input[name="add_client"]')?.remove();
                if (!form.querySelector('input[name="update_client"]')) {
                    form.innerHTML += '<input type="hidden" name="update_client" value="1">';
                }
                form.action = 'clients.php';
            });
        <?php endif; ?>
    </script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
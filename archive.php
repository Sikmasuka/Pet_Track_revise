<?php
require_once __DIR__ . "/functions/archive-handler.php";
require_once 'C:/xampp/htdocs/Pet_Track_revise-2/functions/dashboard-handler.php';

$showRestoreAlert = false;
$showDeleteAlert = false;
$alertTable = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM client c JOIN pet a ON a.pet_id = c.client_id WHERE c.status = 0");
    $stmt->execute();
    $clients = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $clients[$row['client_id']] = $row;
    }

    if (isset($_GET['action'], $_GET['id'], $_GET['table'])) {
        $id = (int)$_GET['id'];
        if ($_GET['action'] == 'restore') {
            if (restoreRecord($pdo, $id)) {
                $showRestoreAlert = true;
                $alertTable = $table;
            }
        } elseif ($_GET['action'] == 'delete') {
            if (deleteFromArchive($pdo, $id)) {
                $showDeleteAlert = true;
                $alertTable = $table;
            }
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}


// Create a client lookup array for quick matching
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archive</title>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
            <a href="clients.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
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
            <a href="archive.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fa-solid fa-box-archive"></i>
                <span class="md:inline">Archive</span>
            </a>
            <a href="#" onclick="confirmLogout(event)" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Container -->
    <div class="ml-0 lg:ml-64 p-4 lg:p-8 pt-16 lg:pt-4">
        <!-- Header -->
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-8 p-4 lg:p-8">
            <!-- Top Greeting -->
            <div class="flex justify-between flex-col sm:flex-row items-start sm:items-center gap-4">
                <h1 class="text-xl lg:text-2xl font-bold">Hello, <?= $vetName ?>.</h1>
                <h1 class="text-xl lg:text-2xl font-bold">Archive</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-sm">
            <?php if (isset($message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-green-800 mb-4">Archived Pets and Clients</h2>
                <?php if (count($clients) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="border-b bg-gray-200">
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">PET NAME</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Species</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Weight</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Breed</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Archived At</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($clients as $client):
                                    $petData = json_decode($client['data'] ?? '{}', true);
                                ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['client_name'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['pet_name'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['pet_species'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['pet_weight'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['pet_breed'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['client_address'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['client_contact_number'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($client['deleted_at'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm">
                                            <a href="?action=restore&id=<?= $client['client_id'] ?>&table=client" class="text-blue-500 hover:underline">Restore</a> |
                                            <a href="?action=delete&id=<?= $client['client_id'] ?>&table=client" class="text-red-500 hover:underline" onclick="return confirmDelete(<?= $client['client_id'] ?>, 'client')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-700 text-sm mb-4">No archived clients found.</p>
                <?php endif; ?>


            </div>
            <div>
                <h2 class="text-lg font-semibold text-green-800 mb-4">Archived Medical Records</h2>
                <?php if (count($archived['medical_records'])): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="border-b bg-gray-200">
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diagnosis</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Treatment</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Archived At</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($archived['medical_records'] as $record): $data = json_decode($record['data'], true); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($data['diagnosis'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($data['treatment'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($data['record_date'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2 text-sm"><?= htmlspecialchars($record['deleted_at']) ?></td>
                                        <td class="px-4 py-2 text-sm">
                                            <a href="?action=restore&id=<?= $record['id'] ?>&table=medical_records" class="text-blue-500 hover:underline">Restore</a> |
                                            <a href="?action=delete&id=<?= $record['id'] ?>&table=medical_records" class="text-red-500 hover:underline" onclick="return confirmDelete(<?= $record['id'] ?>, 'medical_records')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>
                    <p class="text-center text-gray-700 text-sm">No archived medical records</p>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
    <script>
        // Check for restore or delete success
        <?php if ($showRestoreAlert): ?>
            Swal.fire({
                title: 'Success!',
                text: '<?php echo $alertTable; ?> restored successfully.',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'archive.php';
                }
            });
        <?php endif; ?>

        <?php if ($showDeleteAlert): ?>
            Swal.fire({
                title: 'Success!',
                text: '<?php echo $alertTable; ?> deleted permanently.',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'archive.php';
                }
            });
        <?php endif; ?>

        function confirmDelete(id, table) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Delete archived ${table} permanently?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?action=delete&id=${id}&table=${table}`;
                }
            });
            return false;
        }
    </script>
</body>

</html>
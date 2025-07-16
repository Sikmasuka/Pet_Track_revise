<?php
require_once '../functions/admin-handler.php';
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
                <img src="../image/MainIconWhite.png" alt="Dashboard" class="w-8"> Dashboard
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-white hover:text-gray-300 duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="mt-8 lg:mt-36">
            <a href="admin.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-user-md mr-2"></i> Veterinarians
            </a>
            <a href="../index.php" onclick="confirmLogout(event)" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Dashboard Container -->
    <div class="ml-0 lg:ml-64 p-4 lg:p-8 pt-16 lg:pt-4">
        <h1 class="text-3xl font-bold text-green-700 mb-6">Veterinarian Accounts</h1>

        <!-- Add New Veterinarian Button -->
        <button id="openAddModal" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 mb-8">
            Add New Veterinarian
        </button>

        <!-- Add Veterinarian Modal -->
        <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">

            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-xl font-semibold mb-4">Add New Veterinarian</h2>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="vet_name" placeholder="Name" required class="p-2 border rounded-md">
                    <input type="text" name="vet_contact_number" placeholder="Contact Number" required class="p-2 border rounded-md">
                    <input type="text" name="vet_username" placeholder="Username" required class="p-2 border rounded-md">
                    <input type="password" name="vet_password" placeholder="Password" required class="p-2 border rounded-md">
                    <button type="submit" name="add_vet" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 col-span-full w-fit">
                        Add Veterinarian
                    </button>
                </form>
                <button id="closeAddModal" class="mt-4 bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                    Close
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Veterinarians List</h2>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-green-100 text-green-800">
                        <th class="p-2 text-left">Name</th>
                        <th class="p-2 text-left">Contact</th>
                        <th class="p-2 text-left">Username</th>
                        <th class="p-2 text-left">Password</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vets as $vet): ?>
                        <tr class="border-b">
                            <td class="p-2"><?= htmlspecialchars($vet['vet_name']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($vet['vet_contact_number']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($vet['vet_username']) ?></td>
                            <td class="p-2 italic text-gray-400">Hidden (encrypted)</td>
                            <td class="p-2 text-center">
                                <a href="#" class="text-blue-600 hover:underline mr-2 edit-btn" data-vet-id="<?= $vet['vet_id'] ?>">Edit</a>
                                <a href="?delete=<?= $vet['vet_id'] ?>" onclick="return confirm('Are you sure?')" class="text-red-600 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Veterinarian Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Edit Veterinarian</h2>
            <form method="POST" class="grid grid-cols-1 gap-4">
                <input type="hidden" name="vet_id" id="edit_vet_id">
                <input type="text" name="vet_name" id="edit_vet_name" placeholder="Name" required class="p-2 border rounded-md w-full">
                <input type="text" name="vet_contact_number" id="edit_vet_contact_number" placeholder="Contact Number" required class="p-2 border rounded-md w-full">
                <input type="text" name="vet_username" id="edit_vet_username" placeholder="Username" required class="p-2 border rounded-md w-full">
                <input type="password" name="vet_password" id="edit_vet_password" placeholder="Add password to change it" class="p-2 border rounded-md w-full">
                <button type="submit" name="update_vet" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 w-full">
                    Update Veterinarian
                </button>
            </form>
            <button id="closeEditModal" class="mt-4 bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 w-full">
                Close
            </button>
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
                const vetId = this.getAttribute('data-vet-id');
                <?php
                if ($edit_mode && $edit_vet && $edit_vet['vet_id'] == $_GET['edit']) {
                    echo "document.getElementById('edit_vet_id').value = '{$edit_vet['vet_id']}';";
                    echo "document.getElementById('edit_vet_name').value = '" . htmlspecialchars($edit_vet['vet_name']) . "';";
                    echo "document.getElementById('edit_vet_contact_number').value = '" . htmlspecialchars($edit_vet['vet_contact_number']) . "';";
                    echo "document.getElementById('edit_vet_username').value = '" . htmlspecialchars($edit_vet['vet_username']) . "';";
                    echo "document.getElementById('edit_vet_password').value = '';";
                    echo "document.getElementById('editModal').classList.remove('hidden');";
                }
                ?>
            });
        });
        document.getElementById('closeEditModal').addEventListener('click', function() {
            document.getElementById('editModal').classList.add('hidden');
        });

        // Close modals on overlay click
        document.querySelectorAll('#addModal, #editModal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
    <script src="../js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/confirmLogout.js"></script>
</body>

</html>
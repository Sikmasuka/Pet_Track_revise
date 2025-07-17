<?php
session_start();
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
            <a href="admin-dashboard.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                Dashboard
            </a>
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
        <div id="addModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
            <div id="modalContent" class="bg-white rounded-lg shadow-lg w-full max-w-xl overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-green-500 px-6 py-4">
                    <h3 id="petModalTitle" class="text-xl font-bold text-center text-white">
                        Add Veterinarian
                    </h3>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form method="POST" class="grid grid-cols-1 gap-4">
                        <label for="vet_name" class="font-medium">Name</label>
                        <input type="text" name="vet_name" id="vet_name" placeholder="Name" required class="p-2 border rounded-md">

                        <label for="vet_contact_number" class="font-medium">Contact Number</label>
                        <input type="text" name="vet_contact_number" id="vet_contact_number" placeholder="Contact Number" required class="p-2 border rounded-md">

                        <label for="vet_username" class="font-medium">Username</label>
                        <input type="text" name="vet_username" id="vet_username" placeholder="Username" required class="p-2 border rounded-md">

                        <label for="vet_password" class="font-medium">Password</label>
                        <input type="password" name="vet_password" id="vet_password" placeholder="Password" required class="p-2 border rounded-md">

                        <div class="flex justify-between items-center mt-4">
                            <button type="submit" name="add_vet" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                                Add Veterinarian
                            </button>
                            <button type="button" id="closeAddModal" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
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
                                <a href="#"
                                    class="text-blue-600 hover:underline mr-2 edit-btn"
                                    data-vet-id="<?= $vet['vet_id'] ?>"
                                    data-vet-name="<?= htmlspecialchars($vet['vet_name'], ENT_QUOTES) ?>"
                                    data-vet-contact="<?= htmlspecialchars($vet['vet_contact_number'], ENT_QUOTES) ?>"
                                    data-vet-username="<?= htmlspecialchars($vet['vet_username'], ENT_QUOTES) ?>">
                                    Edit
                                </a>
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
        <div class="bg-white rounded-lg shadow-lg w-full max-w-xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="w-full bg-green-500 px-6 py-4">
                <h3 class="text-xl font-bold text-center text-white">
                    Edit Veterinarian
                </h3>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form method="POST" class="grid grid-cols-1 gap-4">
                    <input type="hidden" name="vet_id" id="edit_vet_id">

                    <input type="text" name="vet_name" id="edit_vet_name" placeholder="Name" required class="p-2 border rounded-md w-full">
                    <input type="text" name="vet_contact_number" id="edit_vet_contact_number" placeholder="Contact Number" required class="p-2 border rounded-md w-full">
                    <input type="text" name="vet_username" id="edit_vet_username" placeholder="Username" required class="p-2 border rounded-md w-full">
                    <input type="password" name="vet_password" id="edit_vet_password" placeholder="Add password to change it" class="p-2 border rounded-md w-full">

                    <div class="flex justify-between gap-4">
                        <button type="submit" name="update_vet" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 w-full">
                            Update Veterinarian
                        </button>
                        <button type="button" id="closeEditModal" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 w-full">
                            Close
                        </button>
                    </div>
                </form>
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

        // ✅ Removed automatic modal close when clicking overlay
        // To prevent overlay click from closing the modal:
        ['addModal', 'editModal'].forEach(modalId => {
            const modal = document.getElementById(modalId);
            const content = modal.querySelector('div'); // assumes first child is modal content

            modal.addEventListener('click', function(e) {
                if (!content.contains(e.target)) {
                    // Do nothing: clicking outside does NOT close modal
                    e.stopPropagation();
                }
            });
        });
    </script>

    <script src="../js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/confirmLogout.js"></script>
</body>

</html>
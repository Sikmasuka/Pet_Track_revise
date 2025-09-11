<?php
session_start();
require_once '../record-handler.php';
include(__DIR__ . '/../../includes/sitemap/Help/support.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../image/MainIcon.png" type="image/x-icon">
    <title>Veterinarian Records</title>

    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Load SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../Assets/Extension.js"></script>
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
        #modalContent {
            max-width: 90vw;
            max-height: 85vh;
        }

        @media (min-width: 640px) {
            #modalContent {
                max-width: 80vw;
            }
        }

        @media (min-width: 768px) {
            #modalContent {
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
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-slate-700 text-white p-3 rounded-md shadow-lg hover:bg-slate-600 transition-colors">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-[200px] bg-slate-800 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col border-r border-slate-600">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl lg:text-2xl font-semibold flex items-center gap-2">
                <img src="/image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-gray-300 hover:text-white duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-grow mt-8 lg:mt-12 space-y-0.5">
            <a href="../admin-dashboard.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="../admin.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-user-md mr-2"></i> Veterinarians
            </a>

            <!-- Records Dropdown -->
            <div class="space-y-0.5">
                <button id="recordsBtn"
                    class="w-full flex items-center justify-between text-sm text-white hover:bg-slate-600 px-4 py-2 rounded-md hover:bg-slate-500 transition-colors">
                    <span><i class="fa-solid fa-file-lines mr-2"></i> Records</span>
                    <svg id="recordsArrow" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Submenu -->
                <div id="recordsMenu" class="max-h-0 overflow-hidden opacity-0 transition-all duration-200 ease-in-out pl-8 space-y-1">
                    <a href="./pet-records.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-2 py-1 rounded-md hover:text-white transition-colors">
                        Pets
                    </a>
                    <a href="records-clients.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        Clients
                    </a>
                    <a href="./medical-records.php" class="block text-sm text-gray-300 hover:bg-slate-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        Medical Records
                    </a>
                    <a href="./veterinarian-records.php" class="block text-sm text-gray-300 bg-slate-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        Veterinarians
                    </a>
                </div>
            </div>

            <a href="#" class="block text-sm text-gray-300 hover:bg-slate-600 px-4 py-2 rounded-md hover:text-white transition-colors" onclick="toggleModal('adminHelpModal')">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
            </a>
        </nav>
        <div class="pt-4">
            <a href="../index.php" onclick="confirmLogout(event)" class="block text-sm text-gray-300 hover:text-red-400 px-4 py-2 rounded-md transition-colors">
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
                <h1 class="text-xl lg:text-2xl font-bold">Veterinarian Records</h1>

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

        <!-- Single Column for Records -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Table All Clients -->
            <div class="bg-white p-4 lg:p-6 rounded-lg shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors mb-6">
                <h3 class="text-lg lg:text-xl font-semibold text-gray-800 mb-4">All Clients</h3>

                <?php
                $vets = getVeterinarians($pdo); // Call function from record-handler.php
                if (isset($error) && !empty($error)) {
                    echo "<p class='text-red-500 text-sm'>Error: $error</p>";
                } elseif (!empty($vets) && is_array($vets)) {
                ?>
                    <div class="table-container overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-gray-300">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">Vet Id</th>
                                    <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">Contact No.</th>
                                    <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">username</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <?php foreach ($vets as $vet): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($vet['vet_id'] ?? '') ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($vet['vet_name'] ?? '') ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($vet['vet_contact_number'] ?? '') ?></td>
                                        <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($vet['vet_username'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-gray-500 text-sm">No pets found.</p>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Add Veterinarian Modal -->
    <div id="addModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div id="modalContent" class="bg-white rounded-lg shadow-lg w-full overflow-hidden border border-slate-200">
            <!-- Modal Header -->
            <div class="bg-indigo-500 px-6 py-4">
                <h3 id="petModalTitle" class="text-lg lg:text-xl font-bold text-center text-white">
                    Add Veterinarian
                </h3>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form method="POST" class="grid grid-cols-1 gap-4">
                    <label for="vet_name" class="font-medium text-sm text-gray-800">Name</label>
                    <input type="text" name="vet_name" id="vet_name" placeholder="Name" required class="p-2 border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">

                    <label for="vet_contact_number" class="font-medium text-sm text-gray-800">Contact Number</label>
                    <input type="text" name="vet_contact_number" id="vet_contact_number" placeholder="Contact Number" required class="p-2 border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">

                    <label for="vet_username" class="font-medium text-sm text-gray-800">Username</label>
                    <input type="text" name="vet_username" id="vet_username" placeholder="Username" required class="p-2 border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">

                    <label for="vet_password" class="font-medium text-sm text-gray-800">Password</label>
                    <input type="password" name="vet_password" id="vet_password" placeholder="Password" required class="p-2 border border-slate-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">

                    <div class="flex justify-between items-center mt-4 gap-3">
                        <button type="submit" name="add_vet" class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 text-sm transition-colors">
                            Add Veterinarian
                        </button>
                        <button type="button" id="closeAddModal" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 text-sm transition-colors">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
        // Close Add Modal
        document.getElementById('closeAddModal').addEventListener('click', function() {
            document.getElementById('addModal').classList.add('hidden');
        });

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
                recordsMenu.classList.add('max-h-40', 'opacity-100');
            } else {
                recordsMenu.classList.remove('max-h-40', 'opacity-100');
                recordsMenu.classList.add('max-h-0', 'opacity-0');
            }
            recordsArrow.classList.toggle('rotate-180');
        });
    </script>

    <script src="../../js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../js/confirmLogout.js"></script>
</body>

</html>
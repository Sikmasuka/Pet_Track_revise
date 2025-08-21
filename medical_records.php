<?php
// Start session and include database connection
session_start();
require_once 'db.php';
include "includes/sitemap/Help/support.php";

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
 * Handle adding and updating medical records via POST requests
 */
// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        if (empty($_POST['pet_id']) || empty($_POST['date']) || empty($_POST['medical_condition']) || empty($_POST['medical_diagnosis']) || empty($_POST['medical_symptoms']) || empty($_POST['medical_treatment'])) {
            throw new Exception("All fields are required.");
        }

        // Validate pet_id exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Pet WHERE pet_id = ?");
        $stmt->execute([$_POST['pet_id']]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("Invalid pet_id: " . $_POST['pet_id']);
        }

        if (isset($_POST['add_record'])) {
            $stmt = $pdo->prepare("INSERT INTO Medical_Records (pet_id, date, medical_condition, medical_diagnosis, medical_symptoms, medical_treatment, status, record_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['pet_id'],
                $_POST['date'],
                $_POST['medical_condition'],
                $_POST['medical_diagnosis'],
                $_POST['medical_symptoms'],
                $_POST['medical_treatment'],
                1, // Default status (matches table default)
                $_POST['date'] // Use same date as 'date' field
            ]);
            header('Location: medical_records.php');
            exit;
        } elseif (isset($_POST['update_record'])) {
            $stmt = $pdo->prepare("UPDATE Medical_Records SET pet_id = ?, date = ?, medical_condition = ?, medical_diagnosis = ?, medical_symptoms = ?, medical_treatment = ?, status = ?, record_date = ? WHERE record_id = ?");
            $stmt->execute([
                $_POST['pet_id'],
                $_POST['date'],
                $_POST['medical_condition'],
                $_POST['medical_diagnosis'],
                $_POST['medical_symptoms'],
                $_POST['medical_treatment'],
                1, // Default status (adjust if needed)
                $_POST['date'],
                $_POST['record_id']
            ]);
            header('Location: medical_records.php');
            exit;
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

/**
 * Handle deleting a medical record via GET request
 */
if (isset($_GET['delete_record_id'])) {
    $stmt = $pdo->prepare("DELETE FROM Medical_Records WHERE record_id=?");
    $stmt->execute([$_GET['delete_record_id']]);
    header('Location: medical_records.php');
    exit;
}

/**
 * Fetch medical record data for editing if edit_record_id is set
 */
$recordToEdit = null;
if (isset($_GET['edit_record_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM Medical_Records WHERE record_id=?");
    $stmt->execute([$_GET['edit_record_id']]);
    $recordToEdit = $stmt->fetch();
}

/**
 * Fetch all medical records joined with pet names, ordered by date descending
 */
$stmt = $pdo->prepare("SELECT Medical_Records.record_id, Pet.pet_name, Medical_Records.date, Medical_Records.medical_condition, Medical_Records.medical_diagnosis, Medical_Records.medical_symptoms, Medical_Records.medical_treatment FROM Medical_Records JOIN Pet ON Medical_Records.pet_id = Pet.pet_id ORDER BY Medical_Records.date DESC");
$stmt->execute();
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records</title>

    <script src="Assets/Extension.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-slate-900 min-h-screen">

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
            <a href="dashboard.php" class="block text-sm text-white hover:bg-slate-700 px-4 py-2 rounded-md hover:bg-slate-600 transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="clients.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-user mr-2"></i> Clients
            </a>
            <a href="pets.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-paw mr-2"></i> Pets
            </a>
            <a href="medical_records.php" class="block text-sm text-gray-300 bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
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
            <a href="#" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors" onclick="toggleModal('vetHelpModal')">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
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

        <!-- header -->
        <header class="bg-slate-800 rounded-lg text-white py-4 shadow-sm mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-700">
            <!-- Top Section with Dropdown -->
            <div class="flex justify-between items-center mb-6">

                <!-- Dashboard Title -->
                <h1 class="text-xl lg:text-2xl font-bold">Medical Records</h1>

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

        <main class="bg-slate-800 p-4 lg:p-6 rounded-lg shadow-sm">
            <!-- Medical Records Section -->
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-white mb-4">Medical Records</h2>

            <?php if (count($records) > 0): ?>
                <div class="overflow-x-auto">
                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-700 sticky top-0 z-2">
                                <tr class="border-b border-slate-600">
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Pet Name</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Date</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Condition</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Diagnosis</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Symptoms</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Treatment</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px] whitespace-nowrap overflow-hidden truncate">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-slate-800 divide-y divide-slate-700">
                                <?php foreach ($records as $record): ?>
                                    <tr class="border-b border-slate-700 hover:bg-slate-700/50 transition-colors">
                                        <td class="px-4 py-2 text-slate-200"><?= htmlspecialchars($record['pet_name']) ?></td>
                                        <td class="px-4 py-2 text-slate-200"><?= htmlspecialchars($record['date']) ?></td>
                                        <td class="px-4 py-2 text-slate-200"><?= htmlspecialchars($record['medical_condition']) ?></td>
                                        <td class="px-4 py-2 text-slate-200"><?= htmlspecialchars($record['medical_diagnosis']) ?></td>
                                        <td class="px-4 py-2 text-slate-200"><?= htmlspecialchars($record['medical_symptoms']) ?></td>
                                        <td class="px-4 py-2 text-slate-200"><?= htmlspecialchars($record['medical_treatment']) ?></td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm sm:text-base">
                                            <a href="?edit_record_id=<?= $record['record_id'] ?>" class="text-blue-500 hover:underline">Edit</a> |
                                            <a href="?delete_record_id=<?= $record['record_id'] ?>" onclick="return confirm('Delete this record?')" class="text-red-500 hover:underline">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-sm sm:text-base text-gray-700 text-center">No medical records added yet.</p>
            <?php endif; ?>

            <button onclick="showRecordModal('add')" class="mt-6 bg-indigo-600 text-white px-4 py-2 font-semibold rounded-md hover:bg-indigo-700 transition-colors text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i> Add New Medical Record
            </button>
        </main>
    </div>

    <div id="recordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-slate-800 rounded-lg shadow-lg w-11/12 max-w-3xl max-h-[70vh] overflow-hidden flex flex-col border border-slate-700">
            <div class="w-full bg-slate-700 rounded-t-lg text-white border-b border-slate-600">
                <h3 id="recordModalTitle" class="text-lg font-bold text-center py-2">Add New Medical Record</h3>
            </div>
            <form id="recordForm" method="POST" class="p-4 overflow-y-auto custom-scrollbar">
                <style>
                    .custom-scrollbar::-webkit-scrollbar {
                        width: 8px;
                    }

                    .custom-scrollbar::-webkit-scrollbar-track {
                        background: #2d3748;
                        border-radius: 4px;
                    }

                    .custom-scrollbar::-webkit-scrollbar-thumb {
                        background: #4a5568;
                        border-radius: 4px;
                    }

                    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                        background: #718096;
                    }
                </style>
                <input type="hidden" name="record_id" id="record_id">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-300 mb-2">Medical Record Information</h4>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Pet</label>
                            <select name="pet_id" id="petId" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                                <?php
                                $stmt = $pdo->prepare("SELECT * FROM Pet ORDER BY pet_name ASC");
                                $stmt->execute();
                                $pets = $stmt->fetchAll();
                                foreach ($pets as $pet):
                                ?>
                                    <option value="<?= $pet['pet_id'] ?>"><?= htmlspecialchars($pet['pet_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Date</label>
                            <input type="date" name="date" id="recordDate" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Condition</label>
                            <textarea name="medical_condition" id="medicalCondition" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Diagnosis</label>
                            <textarea name="medical_diagnosis" id="medicalDiagnosis" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Symptoms</label>
                            <textarea name="medical_symptoms" id="medicalSymptoms" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-slate-400 mb-1">Treatment</label>
                            <textarea name="medical_treatment" id="medicalTreatment" class="w-full p-2 border border-slate-700 rounded-md text-sm bg-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-4 pt-2 border-t border-slate-700">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors text-sm">Save</button>
                    <button type="button" onclick="hideRecordModal()" class="text-slate-400 hover:text-white text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function hideRecordModal() {
            document.getElementById('recordModal').classList.add('hidden');
        }
        // Add showRecordModal() if needed: document.getElementById('recordModal').classList.remove('hidden');
    </script>

    <script>
        // Show modal for add or edit record
        function showRecordModal(action) {
            const modal = document.getElementById('recordModal');
            const form = document.getElementById('recordForm');

            if (action === 'add') {
                document.getElementById('recordModalTitle').textContent = 'Add New Medical Record';
                form.reset();
                form.action = '';
                form.innerHTML += '<input type="hidden" name="add_record" value="1">';
            }
            modal.classList.remove('hidden');
        }

        // Hide record modal
        function hideRecordModal() {
            document.getElementById('recordModal').classList.add('hidden');
        }

        // If there's a record to edit, populate the form
        <?php if ($recordToEdit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('recordModalTitle').textContent = 'Edit Medical Record';
                document.getElementById('record_id').value = '<?= $recordToEdit['record_id'] ?>';
                document.getElementById('petId').value = '<?= $recordToEdit['pet_id'] ?>';
                document.getElementById('recordDate').value = '<?= htmlspecialchars($recordToEdit['date']) ?>';
                document.getElementById('medicalCondition').value = '<?= htmlspecialchars($recordToEdit['medical_condition']) ?>';
                document.getElementById('medicalDiagnosis').value = '<?= htmlspecialchars($recordToEdit['medical_diagnosis']) ?>';
                document.getElementById('medicalSymptoms').value = '<?= htmlspecialchars($recordToEdit['medical_symptoms']) ?>';
                document.getElementById('medicalTreatment').value = '<?= htmlspecialchars($recordToEdit['medical_treatment']) ?>';

                const form = document.getElementById('recordForm');
                form.innerHTML += '<input type="hidden" name="update_record" value="1">';

                document.getElementById('recordModal').classList.remove('hidden');
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
    </script>

    <script src="./js/profile-dropdown.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
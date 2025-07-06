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
 * Handle adding and updating medical records via POST requests
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_record'])) {
        // Add new medical record
        $stmt = $pdo->prepare("INSERT INTO Medical_Records (pet_id, date, medical_condition, medical_diagnosis, medical_symptoms, medical_treatment) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['pet_id'], $_POST['date'], $_POST['medical_condition'], $_POST['medical_diagnosis'], $_POST['medical_symptoms'], $_POST['medical_treatment']]);
        header('Location: medical_records.php');
        exit;
    } elseif (isset($_POST['update_record'])) {
        // Update existing medical record
        $stmt = $pdo->prepare("UPDATE Medical_Records SET pet_id=?, date=?, medical_condition=?, medical_diagnosis=?, medical_symptoms=?, medical_treatment=? WHERE record_id=?");
        $stmt->execute([$_POST['pet_id'], $_POST['date'], $_POST['medical_condition'], $_POST['medical_diagnosis'], $_POST['medical_symptoms'], $_POST['medical_treatment'], $_POST['record_id']]);
        header('Location: medical_records.php');
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

<body class="bg-gray-100 flex">

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
            <a href="medical_records.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
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
            <a href="logout.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Content -->
    <div class="ml-0 lg:ml-64 p-4 lg:p-8 pt-16 lg:pt-4 min-h-screen w-full">
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-8 p-8">
            <div class="flex justify-between items-center">
                <h1 class="lg:text-2xl md:text-xl sm:text-lg text-md font-bold">
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
                <h1 class="lg:text-2xl md:text-2xl text-xl font-bold">
                    Manage Medical Records
                </h1>
            </div>
        </header>

        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-sm">
            <!-- Medical Records Section -->
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-green-800 mb-4">Medical Records</h2>

            <?php if (count($records) > 0): ?>
                <div class="overflow-x-auto">
                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-2">
                                <tr class="border-b bg-gray-200">
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Pet Name</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Date</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Condition</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Diagnosis</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Symptoms</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Treatment</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px] whitespace-nowrap overflow-hidden truncate">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($records as $record): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-2 py-4 whitespace-nowrap text-sm sm:text-base"><?= htmlspecialchars($record['pet_name']) ?></td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm sm:text-base"><?= htmlspecialchars($record['date']) ?></td>
                                        <td class="px-2 py-4 text-sm sm:text-base"><?= htmlspecialchars($record['medical_condition']) ?></td>
                                        <td class="px-2 py-4 text-sm sm:text-base"><?= htmlspecialchars($record['medical_diagnosis']) ?></td>
                                        <td class="px-2 py-4 text-sm sm:text-base"><?= htmlspecialchars($record['medical_symptoms']) ?></td>
                                        <td class="px-2 py-4 text-sm sm:text-base"><?= htmlspecialchars($record['medical_treatment']) ?></td>
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

            <button onclick="showRecordModal('add')" class="mt-6 bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 text-sm sm:text-base font-semibold duration-200">
                Add New Medical Record
            </button>
        </main>
    </div>

    <!-- Add/Edit Medical Record Modal -->
    <div id="recordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-[600px]">
            <div class="w-full bg-green-500 rounded-t-lg text-white py-3">
                <h3 id="recordModalTitle" class="text-lg sm:text-xl lg:text-2xl font-bold text-center text-white m-0 py-3">Add New Medical Record</h3>
            </div>

            <form id="recordForm" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                <input type="hidden" name="record_id" id="record_id">
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Pet</label>
                    <select name="pet_id" id="petId" class="w-full p-2 border rounded-md" required>
                        <?php
                        // Get all pets
                        $stmt = $pdo->prepare("SELECT * FROM Pet ORDER BY pet_name ASC");
                        $stmt->execute();
                        $pets = $stmt->fetchAll();
                        foreach ($pets as $pet):
                        ?>
                            <option value="<?= $pet['pet_id'] ?>"><?= htmlspecialchars($pet['pet_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Date</label>
                    <input type="date" name="date" id="recordDate" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Condition</label>
                    <textarea name="medical_condition" id="medicalCondition" class="w-full p-2 border rounded-md" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Diagnosis</label>
                    <textarea name="medical_diagnosis" id="medicalDiagnosis" class="w-full p-2 border rounded-md" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Symptoms</label>
                    <textarea name="medical_symptoms" id="medicalSymptoms" class="w-full p-2 border rounded-md" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Treatment</label>
                    <textarea name="medical_treatment" id="medicalTreatment" class="w-full p-2 border rounded-md" required></textarea>
                </div>
            </form>
            <div class="flex justify-between px-6 pb-6">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Save</button>
                <button type="button" onclick="hideRecordModal()" class="text-gray-500">Cancel</button>
            </div>
        </div>
    </div>

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
    </script>
    <script src="./js/sidebarHandler.js"></script>
</body>

</html>
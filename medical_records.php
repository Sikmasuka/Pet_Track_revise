<?php
ob_start();
session_start();
require_once 'db.php';
require_once 'functions/logs.php';

// Check if user is logged in
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

// Define $vetName
$vetName = htmlspecialchars($currentVet['vet_name'] ?? 'Unknown');

// Fetch veterinarian data for modal (if not already set)
if (!isset($currentVet)) {
    $stmt = $pdo->prepare("SELECT * FROM veterinarian WHERE vet_id = ?");
    $stmt->execute([$_SESSION['vet_id']]);
    $currentVet = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch vet data for modal
$stmt = $pdo->prepare("SELECT * FROM veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$vet = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch vet name and vet_username for logging
$stmt = $pdo->prepare("SELECT vet_name, vet_username FROM Veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$user = $stmt->fetch();
$vetName = $user ? htmlspecialchars($user['vet_name']) : "Veterinarian not found";
$username = $user ? htmlspecialchars($user['vet_username']) : "Unknown";

// Handle POST requests for adding/updating medical records
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $pet_id = $_POST['pet_id'] ?? '';
        $medical_condition = trim($_POST['medical_condition'] ?? '');
        $medical_diagnosis = trim($_POST['medical_diagnosis'] ?? '');
        $medical_symptoms = trim($_POST['medical_symptoms'] ?? '');
        $medical_treatment = trim($_POST['medical_treatment'] ?? '');

        if (empty($pet_id) || empty($medical_condition) || empty($medical_diagnosis) || empty($medical_symptoms) || empty($medical_treatment)) {
            throw new Exception("All medical record fields are required.");
        }

        // Validate pet_id exists and is active
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Pet WHERE pet_id = ? AND status = 1");
        $stmt->execute([$pet_id]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("Invalid or inactive pet selected.");
        }

        if (isset($_POST['add_record'])) {
            $stmt = $pdo->prepare("INSERT INTO Medical_Records (pet_id, date, medical_condition, medical_diagnosis, medical_symptoms, medical_treatment, status, record_date) VALUES (?, CURDATE(), ?, ?, ?, ?, 1, CURDATE())");
            $stmt->execute([$pet_id, $medical_condition, $medical_diagnosis, $medical_symptoms, $medical_treatment]);
            $record_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT pet_name FROM Pet WHERE pet_id = ?");
            $stmt->execute([$pet_id]);
            $pet = $stmt->fetch();
            $pet_name = $pet ? htmlspecialchars($pet['pet_name']) : 'Unknown';
            $description = "$username added a medical record for pet ID $pet_id ('$pet_name')";
            logAction($pdo, $_SESSION['vet_id'], 'add', $description, 'Admin');
            header('Location: medical_records.php?message=Medical record added successfully');
            exit;
        } elseif (isset($_POST['update_record'])) {
            $record_id = $_POST['record_id'] ?? '';
            if (empty($record_id)) {
                throw new Exception("Record ID is required for updating.");
            }
            $stmt = $pdo->prepare("UPDATE Medical_Records SET pet_id = ?, date = CURDATE(), medical_condition = ?, medical_diagnosis = ?, medical_symptoms = ?, medical_treatment = ?, status = 1, updated_at = NOW() WHERE record_id = ?");
            $stmt->execute([$pet_id, $medical_condition, $medical_diagnosis, $medical_symptoms, $medical_treatment, $record_id]);
            $stmt = $pdo->prepare("SELECT pet_name FROM Pet WHERE pet_id = ?");
            $stmt->execute([$pet_id]);
            $pet = $stmt->fetch();
            $pet_name = $pet ? htmlspecialchars($pet['pet_name']) : 'Unknown';
            $description = "$username updated medical record ID $record_id for pet ID $pet_id ('$pet_name')";
            logAction($pdo, $_SESSION['vet_id'], 'update', $description, 'Admin');
            header('Location: medical_records.php?message=Medical record updated successfully');
            exit;
        }
    } catch (PDOException $e) {
        echo "Database Error: Cannot process medical record. " . $e->getMessage();
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

// Handle archiving of medical records
if (isset($_GET['archive_record_id']) && is_numeric($_GET['archive_record_id'])) {
    try {
        $record_id = (int)$_GET['archive_record_id'];
        $stmt = $pdo->prepare("SELECT Pet.pet_name FROM Medical_Records JOIN Pet ON Medical_Records.pet_id = Pet.pet_id WHERE Medical_Records.record_id = ?");
        $stmt->execute([$record_id]);
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);
        $pet_name = $pet ? htmlspecialchars($pet['pet_name']) : 'Unknown';
        $stmt = $pdo->prepare("SELECT vet_username FROM Veterinarian WHERE vet_id = ?");
        $stmt->execute([$_SESSION['vet_id']]);
        $vet = $stmt->fetch(PDO::FETCH_ASSOC);
        $username = $vet ? htmlspecialchars($vet['vet_username']) : 'Unknown';
        $stmt = $pdo->prepare("UPDATE Medical_Records SET status = 0, updated_at = NOW() WHERE record_id = ?");
        $stmt->execute([$record_id]);
        $description = "$username archived medical record ID $record_id for pet '$pet_name'";
        logAction($pdo, $_SESSION['vet_id'], 'archive', $description, 'Admin');
        header('Location: medical_records.php?message=Medical record archived successfully');
        exit;
    } catch (PDOException $e) {
        echo "Database Error: Cannot archive medical record. " . $e->getMessage();
        exit;
    }
}

// Fetch medical record for editing
$recordToEdit = null;
if (isset($_GET['edit_record_id']) && is_numeric($_GET['edit_record_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM Medical_Records WHERE record_id = ? AND status = 1");
    $stmt->execute([$_GET['edit_record_id']]);
    $recordToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch active medical records with pet names
$stmt = $pdo->prepare("SELECT Medical_Records.record_id, Pet.pet_name, Medical_Records.date, Medical_Records.medical_condition, Medical_Records.medical_diagnosis, Medical_Records.medical_symptoms, Medical_Records.medical_treatment 
                       FROM Medical_Records 
                       JOIN Pet ON Medical_Records.pet_id = Pet.pet_id 
                       WHERE Medical_Records.status = 1 
                       ORDER BY Medical_Records.date DESC");
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_end_flush();
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
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #edf2f7;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .table-container {
            overflow-x: auto;
        }

        .mobile-menu-hidden {
            transform: translateX(-100%);
        }

        .mobile-menu-visible {
            transform: translateX(0);
        }

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
    <?php include('./includes/edit-profile.php') ?>
    <?php include "includes/sitemap/Help/support.php"; ?>

    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-slate-700 text-white p-3 rounded-md shadow-lg hover:bg-slate-600 transition-colors">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-[200px] bg-slate-800 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col border-r border-slate-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl lg:text-2xl font-semibold flex items-center gap-2">
                <img src="image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-gray-300 hover:text-white duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-grow mt-8 lg:mt-12 space-y-0.5">
            <a href="dashboard.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="clients.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-user mr-2"></i> Clients
            </a>
            <a href="pets.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-paw mr-2"></i> Pets
            </a>
            <a href="medical_records.php" class="block text-sm text-white bg-slate-700 px-4 py-2 rounded-md">
                <i class="fas fa-file-medical mr-2"></i> Medical Records
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
        <header class="bg-white shadow-lg rounded-lg text-gray-800 py-4 mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-200">
            <div class="flex justify-between items-center">
                <h1 class="text-xl lg:text-2xl font-bold">Medical Records</h1>
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

        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-lg border border-slate-200">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-800 mb-4">Medical Records</h2>
            <?php if (isset($_GET['message'])): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md text-sm">
                    <?= htmlspecialchars($_GET['message']) ?>
                </div>
            <?php endif; ?>
            <?php if (count($records) > 0): ?>
                <div class="table-container">
                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-gray-100 sticky top-0 z-2">
                                <tr class="border-b border-slate-200">
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px]">Pet Name</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px]">Date</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px]">Condition</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px]">Diagnosis</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px]">Symptoms</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px]">Treatment</th>
                                    <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[100px]">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <?php foreach ($records as $record): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($record['pet_name']) ?></td>
                                        <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($record['date']) ?></td>
                                        <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($record['medical_condition']) ?></td>
                                        <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($record['medical_diagnosis']) ?></td>
                                        <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($record['medical_symptoms']) ?></td>
                                        <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($record['medical_treatment']) ?></td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm sm:text-base">
                                            <a href="?edit_record_id=<?= (int)$record['record_id'] ?>" class="text-indigo-500 hover:underline">Edit</a> |
                                            <a href="#" onclick="confirmArchive(<?= (int)$record['record_id'] ?>)" class="text-red-500 hover:underline">Archive</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-sm sm:text-base text-gray-500 text-center">No medical records added yet.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function confirmArchive(recordId) {
            if (typeof Swal === 'undefined') {
                if (confirm('Are you sure you want to archive this medical record?')) {
                    window.location.href = `?archive_record_id=${recordId}`;
                }
                return false;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will archive the medical record.',
                icon: 'warning',
                background: '#1e293b',
                color: '#e2e8f0',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?archive_record_id=${recordId}`;
                }
            });
            return false;
        }

        function showRecordModal(action) {
            const modal = document.getElementById('recordModal');
            const form = document.getElementById('recordForm');
            form.reset();
            form.innerHTML = form.innerHTML.replace(/<input type="hidden" name="(add_record|update_record)" value="1">/, '');
            if (action === 'add') {
                document.getElementById('recordModalTitle').textContent = 'Add New Medical Record';
                form.innerHTML += '<input type="hidden" name="add_record" value="1">';
            } else if (action === 'edit') {
                document.getElementById('recordModalTitle').textContent = 'Edit Medical Record';
                form.innerHTML += '<input type="hidden" name="update_record" value="1">';
            }
            modal.classList.remove('hidden');
        }

        function hideRecordModal() {
            document.getElementById('recordModal').classList.add('hidden');
        }

        <?php if ($recordToEdit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showRecordModal('edit');
                document.getElementById('record_id').value = '<?= (int)$recordToEdit['record_id'] ?>';
                document.getElementById('petId').value = '<?= (int)$recordToEdit['pet_id'] ?>';
                document.getElementById('medicalCondition').value = <?= json_encode($recordToEdit['medical_condition']) ?>;
                document.getElementById('medicalDiagnosis').value = <?= json_encode($recordToEdit['medical_diagnosis']) ?>;
                document.getElementById('medicalSymptoms').value = <?= json_encode($recordToEdit['medical_symptoms']) ?>;
                document.getElementById('medicalTreatment').value = <?= json_encode($recordToEdit['medical_treatment']) ?>;
            });
        <?php endif; ?>

        <?php if (isset($_GET['message'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Success',
                        text: <?= json_encode($_GET['message']) ?>,
                        icon: 'success',
                        background: '#1e293b',
                        color: '#e2e8f0',
                        confirmButtonColor: '#6366f1',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.delete('message');
                        window.history.replaceState({}, document.title, url);
                    });
                } else {
                    alert(<?= json_encode($_GET['message']) ?>);
                    const url = new URL(window.location.href);
                    url.searchParams.delete('message');
                    window.history.replaceState({}, document.title, url);
                }
            });
        <?php endif; ?>

        function toggleModal(modalId) {
            console.log("Toggling modal:", modalId);
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle('hidden');
            } else {
                console.error("Modal not found:", modalId);
            }
        }
    </script>

    <script src="./js/dashboard.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
    <script src="./js/edit-profile.js"></script>
</body>

</html>
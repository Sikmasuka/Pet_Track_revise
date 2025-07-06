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
 * Fetch veterinarian data for the logged-in user
 */
$stmt = $pdo->prepare("SELECT * FROM Veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$vet = $stmt->fetch();

/**
 * Handle updating veterinarian profile via POST request
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['vet_name'];
    $contact = $_POST['vet_contact_number'];
    $username = $_POST['vet_username'];

    if (!empty($_POST['vet_password'])) {
        $password = password_hash($_POST['vet_password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE Veterinarian SET vet_name = ?, vet_contact_number = ?, vet_username = ?, vet_password = ? WHERE vet_id = ?");
        $stmt->execute([$name, $contact, $username, $password, $_SESSION['vet_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE Veterinarian SET vet_name = ?, vet_contact_number = ?, vet_username = ? WHERE vet_id = ?");
        $stmt->execute([$name, $contact, $username, $_SESSION['vet_id']]);
    }

    header('Location: profile.php?success=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <script src="Assets/Extension.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">

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
            <a href="medical_records.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-file-medical mr-2"></i>
                <span class="md:inline">Medical Records</span>
            </a>
            <a href="profile.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
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
    <div class="flex-1 p-8">
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-8 p-8">
            <h1 class="text-2xl font-bold">My Profile</h1>
        </header>

        <main class="bg-white p-6 rounded-lg shadow-sm max-w-xl mx-auto">
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-4 text-green-600 font-medium">Profile updated successfully!</div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Name</label>
                    <input type="text" name="vet_name" value="<?= htmlspecialchars($vet['vet_name']) ?>" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Contact Number</label>
                    <input type="text" name="vet_contact_number" value="<?= htmlspecialchars($vet['vet_contact_number']) ?>" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Username</label>
                    <input type="text" name="vet_username" value="<?= htmlspecialchars($vet['vet_username']) ?>" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Password</label>
                    <input type="password" name="vet_password" class="w-full p-2 border rounded-md" placeholder="Add password if you want to change your password.">
                </div>
                <div class="flex justify-between">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Save</button>

                </div>
            </form>
        </main>
    </div>

    <script src="./js/sidebarHandler.js"></script>
</body>

</html>
<?php
// Start session and include database connection
session_start();
require_once 'db.php';


// Check if user is logged in by verifying session role
if (!isset($_SESSION['role'])) {
    header('Location: index.php');
    exit;
}


/**
 * Fetch all veterinarians from the database
 */
$stmt = $pdo->query("SELECT vet_id, vet_name, vet_contact_number, vet_username FROM Veterinarian");
$vets = $stmt->fetchAll();

/**
 * Determine if the page is in edit mode by checking for 'edit' parameter
 */
$edit_mode = false;
$edit_vet = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM Veterinarian WHERE vet_id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_vet = $stmt->fetch();
    if ($edit_vet) {
        $edit_mode = true;
    }
}

/**
 * Handle adding a new veterinarian
 */
if (isset($_POST['add_vet'])) {
    $stmt = $pdo->prepare("INSERT INTO Veterinarian (vet_name, vet_contact_number, vet_username, vet_password) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['vet_name'],
        $_POST['vet_contact_number'],
        $_POST['vet_username'],
        password_hash($_POST['vet_password'], PASSWORD_BCRYPT)
    ]);
    header("Location: admin.php");
    exit;
}

/**
 * Handle updating an existing veterinarian
 */
if (isset($_POST['update_vet'])) {
    if (!empty($_POST['vet_password'])) {
        $stmt = $pdo->prepare("UPDATE Veterinarian SET vet_name=?, vet_contact_number=?, vet_username=?, vet_password=? WHERE vet_id=?");
        $stmt->execute([
            $_POST['vet_name'],
            $_POST['vet_contact_number'],
            $_POST['vet_username'],
            password_hash($_POST['vet_password'], PASSWORD_BCRYPT),
            $_POST['vet_id']
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE Veterinarian SET vet_name=?, vet_contact_number=?, vet_username=? WHERE vet_id=?");
        $stmt->execute([
            $_POST['vet_name'],
            $_POST['vet_contact_number'],
            $_POST['vet_username'],
            $_POST['vet_id']
        ]);
    }
    header("Location: admin.php");
    exit;
}

/**
 * Handle deleting a veterinarian
 */
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM Veterinarian WHERE vet_id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Veterinarian Admin</title>
    <script src="Assets/Extension.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">

</head>

<body class="bg-gray-100 flex">
    <div class="w-64 bg-gradient-to-b from-green-500 to-green-600 text-white h-screen p-4">
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
            <img src="image/MainIconWhite.png" alt="Dashboard" class="w-8"> Dashboard
        </h2>
        <nav class="mt-48">
            <a href="admin.php" class="block text-lg text-white bg-green-600 px-4 py-2 rounded-md">
                <i class="fas fa-user-md mr-2"></i> Veterinarians
            </a>
            <a href="logout.php" class="block text-lg text-white hover:bg-green-600 px-4 py-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </nav>
    </div>

    <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold text-green-700 mb-6">Veterinarian Accounts</h1>

        <!-- Add/Edit Form -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4"><?= $edit_mode ? 'Edit Veterinarian' : 'Add New Veterinarian' ?></h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="vet_id" value="<?= $edit_vet['vet_id'] ?>">
                <?php endif; ?>
                <input type="text" name="vet_name" placeholder="Name" required class="p-2 border rounded-md"
                    value="<?= $edit_mode ? htmlspecialchars($edit_vet['vet_name']) : '' ?>">
                <input type="text" name="vet_contact_number" placeholder="Contact Number" required class="p-2 border rounded-md"
                    value="<?= $edit_mode ? htmlspecialchars($edit_vet['vet_contact_number']) : '' ?>">
                <input type="text" name="vet_username" placeholder="Username" required class="p-2 border rounded-md"
                    value="<?= $edit_mode ? htmlspecialchars($edit_vet['vet_username']) : '' ?>">
                <input type="password" name="vet_password" placeholder="<?= $edit_mode ? 'Add password to change it' : 'Password' ?>" class="p-2 border rounded-md">
                <button type="submit" name="<?= $edit_mode ? 'update_vet' : 'add_vet' ?>" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 col-span-full w-fit">
                    <?= $edit_mode ? 'Update' : 'Add' ?> Veterinarian
                </button>
            </form>
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
                                <a href="?edit=<?= $vet['vet_id'] ?>" class="text-blue-600 hover:underline mr-2">Edit</a>
                                <a href="?delete=<?= $vet['vet_id'] ?>" onclick="return confirm('Are you sure?')" class="text-red-600 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
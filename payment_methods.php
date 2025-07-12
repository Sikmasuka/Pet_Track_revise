<?php
// Start session and include database connection
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['vet_id'])) {
    header('Location: index.php');
    exit;
}

/**
 * Handle adding, updating payment methods, and recording payments via POST requests
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_payment_method'])) {
        $stmt = $pdo->prepare("INSERT INTO Payment_Method (method_name) VALUES (?)");
        $stmt->execute([$_POST['method_name']]);
        header('Location: payment_methods.php');
        exit;
    } elseif (isset($_POST['update_payment_method'])) {
        $stmt = $pdo->prepare("UPDATE Payment_Method SET method_name=? WHERE method_id=?");
        $stmt->execute([$_POST['method_name'], $_POST['method_id']]);
        header('Location: payment_methods.php');
        exit;
    } elseif (isset($_POST['record_payment'])) {
        $stmt = $pdo->prepare("INSERT INTO Payments (client_name, method_id, amount, description, date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_POST['client_name'],
            $_POST['method_id'],
            $_POST['amount'],
            $_POST['description']
        ]);
        header('Location: payment_methods.php');
        exit;
    }
}

/**
 * Handle deleting a payment method via GET request
 */
if (isset($_GET['delete_method_id'])) {
    $stmt = $pdo->prepare("DELETE FROM Payment_Method WHERE method_id=?");
    $stmt->execute([$_GET['delete_method_id']]);
    header('Location: payment_methods.php');
    exit;
}

/**
 * Fetch payment method data for editing
 */
$methodToEdit = null;
if (isset($_GET['edit_method_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM Payment_Method WHERE method_id=?");
    $stmt->execute([$_GET['edit_method_id']]);
    $methodToEdit = $stmt->fetch();
}

/**
 * Fetch all payment methods
 */
$stmt = $pdo->prepare("SELECT * FROM Payment_Method ORDER BY method_name ASC");
$stmt->execute();
$methods = $stmt->fetchAll();

/**
 * Fetch all payments
 */
$stmt = $pdo->query("SELECT p.*, m.method_name FROM Payments p JOIN Payment_Method m ON p.method_id = m.method_id ORDER BY p.date DESC");
$payments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vet Clinic Payments</title>
    <script src="Assets/Extension.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .mobile-menu-hidden {
            transform: translateX(-100%);
        }

        .mobile-menu-visible {
            transform: translateX(0);
        }

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
                <i class="fas fa-tachometer-alt mr-2"></i><span class="md:inline">Dashboard</span>
            </a>
            <a href="clients.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-user mr-2"></i><span class="md:inline">Clients</span>
            </a>
            <a href="pets.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-paw mr-2"></i><span class="md:inline">Pets</span>
            </a>
            <a href="medical_records.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-file-medical mr-2"></i><span class="md:inline">Medical Records</span>
            </a>
            <a href="profile.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-id-badge mr-2"></i><span class="md:inline">Profile</span>
            </a>
            <a href="payment_methods.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-credit-card mr-2"></i><span class="md:inline">Payments</span>
            </a>
            <a href="#" onclick="confirmLogout(event)" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i><span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Content -->
    <div class="ml-0 lg:ml-64 p-4 lg:p-8 pt-16 lg:pt-4 w-full">
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-6 lg:mb-8 p-4 lg:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold">
                    Hello,
                    <?php
                    $stmt = $pdo->prepare("SELECT vet_name FROM veterinarian WHERE vet_id=?");
                    $stmt->execute([$_SESSION['vet_id']]);
                    $user = $stmt->fetch();
                    echo $user ? htmlspecialchars($user['vet_name']) : "Veterinarian not found.";
                    ?>.
                </h1>
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold">Manage Payments</h1>
            </div>
        </header>

        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-green-800">Payment Methods</h2>
                <div class="flex gap-2">
                    <button onclick="showMethodModal('add')" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 text-sm sm:text-base">
                        Add Payment Method
                    </button>
                    <button onclick="showPaymentModal()" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm sm:text-base">
                        Record Payment
                    </button>
                </div>
            </div>

            <!-- Payment Methods Table -->
            <?php if (count($methods) > 0): ?>
                <div class="table-container mb-8">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-5">
                            <tr class="border-b bg-gray-200">
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Method Name</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($methods as $method): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($method['method_name']) ?></td>
                                    <td class="px-4 py-2 text-sm">
                                        <a href="?edit_method_id=<?= (int)$method['method_id'] ?>" class="text-blue-500 hover:underline">Edit</a> |
                                        <a href="?delete_method_id=<?= (int)$method['method_id'] ?>" class="text-red-500 hover:underline">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-700 text-sm sm:text-base mb-8">No payment methods added yet.</p>
            <?php endif; ?>

            <!-- Payment History Table -->
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-green-800 mb-4">Payment History</h2>
            <?php if (count($payments) > 0): ?>
                <div class="table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-5">
                            <tr class="border-b bg-gray-200">
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Client</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Method</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Amount</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Description</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Date</th>
                                <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($payments as $pay): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($pay['client_name']) ?></td>
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($pay['method_name']) ?></td>
                                    <td class="px-4 py-2 text-sm">₱<?= number_format($pay['amount'], 2) ?></td>
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($pay['description']) ?></td>
                                    <td class="px-4 py-2 text-sm"><?= date('M j, Y', strtotime($pay['date'])) ?></td>
                                    <td class="px-4 py-2 text-sm">
                                        <button onclick="printReceipt(`<?= htmlspecialchars($pay['client_name']) ?>`, `<?= htmlspecialchars($pay['method_name']) ?>`, `<?= $pay['amount'] ?>`, `<?= htmlspecialchars($pay['description']) ?>`, `<?= $pay['date'] ?>`)" class="text-green-600 hover:underline">Print</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-700 text-sm sm:text-base">No payments recorded yet.</p>
            <?php endif; ?>
        </main>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg">
            <div class="w-full bg-green-500 rounded-t-lg text-white">
                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-center py-3">Record Payment</h3>
            </div>
            <form method="POST" class="p-4 sm:p-6">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Client Name</label>
                    <input type="text" name="client_name" required class="w-full p-2 border rounded-md text-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Payment Method</label>
                    <select name="method_id" required class="w-full p-2 border rounded-md text-sm">
                        <option value="">Select</option>
                        <?php foreach ($methods as $method): ?>
                            <option value="<?= (int)$method['method_id'] ?>"><?= htmlspecialchars($method['method_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Amount</label>
                    <input type="number" name="amount" min="0" step="0.01" required class="w-full p-2 border rounded-md text-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Description</label>
                    <textarea name="description" class="w-full p-2 border rounded-md text-sm" rows="3"></textarea>
                </div>
                <div class="flex justify-between mt-6">
                    <button type="submit" name="record_payment" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 text-sm sm:text-base">Save</button>
                    <button type="button" onclick="hidePaymentModal()" class="text-gray-500 text-sm sm:text-base">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Method Modal -->
    <div id="methodModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg">
            <div class="w-full bg-green-500 rounded-t-lg text-white">
                <h3 id="modalTitle" class="text-lg sm:text-xl lg:text-2xl font-bold text-center py-3">Add Payment Method</h3>
            </div>
            <form id="methodForm" method="POST" class="p-4 sm:p-6">
                <input type="hidden" name="method_id" id="method_id">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700">Method Name</label>
                    <input type="text" name="method_name" id="methodName" class="w-full p-2 border rounded-md text-sm" required>
                </div>
                <div class="flex justify-between mt-6">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 text-sm sm:text-base">Save</button>
                    <button type="button" onclick="hideMethodModal()" class="text-gray-500 text-sm sm:text-base">Cancel</button>
                </div>
                <input type="hidden" name="add_payment_method" id="formAction" value="1">
            </form>
        </div>
    </div>

    <!-- Print Logic -->
    <iframe id="receiptFrame" class="hidden"></iframe>

    <script>
        function showMethodModal(action) {
            const modal = document.getElementById('methodModal');
            const form = document.getElementById('methodForm');
            const modalTitle = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');

            form.reset();
            form.querySelector('input[name="update_payment_method"]')?.remove();
            formAction.name = 'add_payment_method';
            modalTitle.textContent = 'Add Payment Method';

            if (action === 'edit') {
                modalTitle.textContent = 'Edit Payment Method';
                formAction.name = 'update_payment_method';
            }

            modal.classList.remove('hidden');
        }

        function hideMethodModal() {
            document.getElementById('methodModal').classList.add('hidden');
        }

        function showPaymentModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function hidePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        <?php if ($methodToEdit): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showMethodModal('edit');
                document.getElementById('method_id').value = <?= json_encode($methodToEdit['method_id'] ?? '') ?>;
                document.getElementById('methodName').value = <?= json_encode($methodToEdit['method_name'] ?? '') ?>;
            });
        <?php endif; ?>
    </script>
    <script src="./js/printScript.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
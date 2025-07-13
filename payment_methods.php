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
    if (isset($_POST['record_payment'])) {
        $stmt = $pdo->prepare("INSERT INTO Payments (client_name, method_id, amount, description, date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_POST['client_name'],
            $_POST['method_id'],
            $_POST['amount'],
            $_POST['description']
        ]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

/**
 * Fetch all payments
 */
$stmt = $pdo->query("SELECT p.*, m.method_name FROM Payments p JOIN Payment_Methods m ON p.method_id = m.method_id ORDER BY p.date DESC");
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

        /* Fix main content overflow */
        .main-content {
            width: calc(100% - 0px);
            /* Full width on mobile */
            max-width: 100%;
            overflow-x: hidden;
        }

        @media (min-width: 1024px) {
            .main-content {
                width: calc(100% - 256px);
                /* Subtract sidebar width on desktop */
                margin-left: 256px;
            }
        }

        /* Table container fixes */
        .table-container {
            overflow-x: auto;
            width: 100%;
            max-width: 100%;
            -webkit-overflow-scrolling: touch;
        }

        .table-container table {
            min-width: 600px;
            /* Minimum width for proper table display */
            width: 100%;
        }

        /* Mobile adjustments */
        @media (max-width: 1023px) {
            .main-content {
                padding-left: 1rem;
                padding-right: 1rem;
                padding-top: 5rem;
                margin-left: 0;
            }
        }

        /* Ensure mobile menu button doesn't interfere */
        .mobile-menu-btn {
            z-index: 1000;
        }

        /* Fix body overflow */
        body {
            overflow-x: hidden;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="mobile-menu-btn lg:hidden fixed top-4 left-4 z-50 bg-green-600 text-white p-3 rounded-md shadow-lg">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-green-500 to-green-600 text-white p-4 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl lg:text-3xl font-semibold mb-6 flex items-center gap-2">
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
    <div class="main-content min-h-screen p-4 lg:p-8 pt-20 lg:pt-4">
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


        <div class="bg-white rounded-lg shadow-sm p-6 mb-6 lg:mb-8">
            <!-- Payment History Table -->
            <?php if (count($payments) > 0): ?>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-green-800 mb-4">Payment History</h2>
                    <!-- Record Payment Button -->
                    <button onclick="showPaymentModal()" class="bg-green-500 text-white px-6 py-3 rounded-md hover:bg-green-600 text-sm sm:text-base transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Record Payment
                    </button>
                </div>
                <div class="table-container bg-white rounded-lg shadow-sm overflow-hidden mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-5">
                            <tr class="border-b bg-gray-200">
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($payments as $pay): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm"><?= htmlspecialchars($pay['client_name']) ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-900"><?= htmlspecialchars($pay['method_name']) ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-900 font-medium">₱<?= number_format($pay['amount'], 2) ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="<?= htmlspecialchars($pay['description']) ?>">
                                            <?= htmlspecialchars($pay['description']) ?>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-900"><?= date('M j, Y', strtotime($pay['date'])) ?></td>
                                    <td class="px-3 py-3 text-sm">
                                        <button onclick="printReceipt('<?= htmlspecialchars($pay['client_name']) ?>', '<?= htmlspecialchars($pay['method_name']) ?>', '<?= $pay['amount'] ?>', '<?= htmlspecialchars($pay['description']) ?>', '<?= $pay['date'] ?>')" class="text-blue-600 hover:text-blue-800 hover:underline">
                                            Print
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 bg-white rounded-lg shadow-sm mb-4">
                    <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No payments recorded yet.</p>
                    <p class="text-gray-400 text-sm mt-2">Click "Record Payment" to add your first payment.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-lg max-h-screen overflow-y-auto">
            <div class="bg-green-500 rounded-t-lg text-white px-6 py-4">
                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold">Record Payment</h3>
            </div>
            <form method="POST" class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Client Name</label>
                    <input type="text" name="client_name" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <select name="method_id" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Select Payment Method</option>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM Payment_Methods ORDER BY method_name ASC");
                        $methods = $stmt->fetchAll();
                        foreach ($methods as $method): ?>
                            <option value="<?= (int)$method['method_id'] ?>"><?= htmlspecialchars($method['method_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (₱)</label>
                    <input type="number" name="amount" min="0" step="0.01" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" rows="3" placeholder="Enter payment description..."></textarea>
                </div>
                <div class="flex justify-end gap-4">
                    <button type="button" onclick="hidePaymentModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" name="record_payment" class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Print Logic -->
    <iframe id="receiptFrame" class="hidden"></iframe>

    <script>
        function showPaymentModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function hidePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>
    <script src="./js/printScript.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
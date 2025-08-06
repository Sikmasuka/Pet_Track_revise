<?php
// Start session and include database connection
session_start();
require_once 'db.php';

// Check if user is logged in
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
        /* Custom dark theme scrollbar */
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

        .table-container {
            overflow-x: auto;
            width: 100%;
            max-width: 100%;
            -webkit-overflow-scrolling: touch;
        }

        .table-container table {
            min-width: 600px;
            width: 100%;
        }

        .mobile-menu-btn {
            z-index: 1000;
        }

        body {
            overflow-x: hidden;
        }

        /* Truncate long text with ellipsis */
        .truncate-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body class="bg-slate-900 min-h-screen text-gray-100">
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="mobile-menu-btn lg:hidden fixed top-4 left-4 z-50 bg-slate-700 text-white p-3 rounded-md shadow-lg hover:bg-slate-600 transition-colors">
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
            <a href="dashboard.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="clients.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-user mr-2"></i> Clients
            </a>
            <a href="pets.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-paw mr-2"></i> Pets
            </a>
            <a href="medical_records.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-file-medical mr-2"></i> Medical Records
            </a>
            <a href="profile.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-id-badge mr-2"></i> Profile
            </a>
            <a href="payment_methods.php" class="block text-sm text-white bg-slate-700 px-4 py-2 rounded-md">
                <i class="fas fa-credit-card mr-2"></i> Payments
            </a>
            <a href="appointments.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-calendar-days mr-2"></i> Appointments
            </a>
            <a href="archive.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fa-solid fa-box-archive mr-2"></i> Archive
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
    <div class="main-content ml-0 lg:ml-52 p-4 pt-12 lg:pt-4">
        <header class="bg-slate-800 rounded-lg py-4 shadow-sm mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-700">
            <!-- Top Section with Dropdown -->
            <div class="flex justify-between items-center mb-6">
                <!-- Dashboard Title -->
                <h1 class="text-xl lg:text-2xl font-bold">Payment Records</h1>

                <!-- Profile Dropdown -->
                <div class="relative inline-block text-left">
                    <button id="profileButton" class="flex items-center justify-center w-10 h-10 bg-slate-700 border border-slate-600 rounded-full hover:bg-slate-600 text-white text-lg transition-colors">
                        <i class="fas fa-user"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="dropdownMenu" class="origin-top-right absolute right-0 mt-2 w-72 rounded-lg shadow-lg bg-slate-800 ring-1 ring-black ring-opacity-5 opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out z-50 border border-slate-700">
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

        <div class="bg-slate-800 rounded-lg shadow-sm p-6 mb-6 lg:mb-8 border border-slate-700">
            <!-- Payment History Table -->
            <?php if (count($payments) > 0): ?>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-white mb-4">Payment History</h2>
                    <!-- Record Payment Button -->
                    <button onclick="showPaymentModal()" class="bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 text-sm sm:text-base transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Record Payment
                    </button>
                </div>
                <div class="table-container">
                    <table class="min-w-full divide-y divide-slate-700">
                        <thead class="bg-slate-700 sticky top-0 z-5">
                            <tr class="border-b border-slate-600">
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider">Client</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider">Method</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider">Amount</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider">Description</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider">Date</th>
                                <th class="px-3 py-3 text-left text-xs sm:text-sm font-medium text-slate-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-800 divide-y divide-slate-700">
                            <?php foreach ($payments as $pay): ?>
                                <tr class="hover:bg-slate-700/50 transition-colors">
                                    <td class="px-4 py-2 text-sm text-slate-200"><?= htmlspecialchars($pay['client_name']) ?></td>
                                    <td class="px-3 py-3 text-sm text-slate-300"><?= htmlspecialchars($pay['method_name']) ?></td>
                                    <td class="px-3 py-3 text-sm text-slate-300 font-medium">₱<?= number_format($pay['amount'], 2) ?></td>
                                    <td class="px-3 py-3 text-sm text-slate-300 truncate-cell" title="<?= htmlspecialchars($pay['description']) ?>">
                                        <?= htmlspecialchars($pay['description']) ?>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-slate-300"><?= date('M j, Y', strtotime($pay['date'])) ?></td>
                                    <td class="px-3 py-3 text-sm">
                                        <button onclick="printReceipt('<?= htmlspecialchars($pay['client_name']) ?>', '<?= htmlspecialchars($pay['method_name']) ?>', '<?= $pay['amount'] ?>', '<?= htmlspecialchars($pay['description']) ?>', '<?= $pay['date'] ?>')"
                                            class="text-indigo-400 hover:text-indigo-300 hover:underline">
                                            Print
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 rounded-lg mb-4 bg-slate-700/50">
                    <i class="fas fa-receipt text-slate-400 text-4xl mb-4"></i>
                    <p class="text-slate-300 text-lg">No payments recorded yet.</p>
                    <p class="text-slate-400 text-sm mt-2">Click "Record Payment" to add your first payment.</p>
                    <button onclick="showPaymentModal()" class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Record Payment
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50 p-4">
        <div class="bg-slate-800 rounded-lg shadow-xl w-full max-w-md max-h-[90vh] flex flex-col border border-slate-700">
            <div class="bg-slate-700 rounded-t-lg text-white px-4 py-3 border-b border-slate-600">
                <h3 class="text-lg font-bold">Record Payment</h3>
            </div>
            <form method="POST" class="p-4 overflow-y-auto">
                <div class="mb-3">
                    <label class="block text-xs font-medium text-slate-300 mb-1">Client Name</label>
                    <input type="text" name="client_name" required
                        class="w-full p-2 text-sm border border-slate-700 rounded-md bg-slate-700 text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-slate-300 mb-1">Payment Method</label>
                    <select name="method_id" required
                        class="w-full p-2 text-sm border border-slate-700 rounded-md bg-slate-700 text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">Select Payment Method</option>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM Payment_Methods ORDER BY method_name ASC");
                        $methods = $stmt->fetchAll();
                        foreach ($methods as $method): ?>
                            <option value="<?= (int)$method['method_id'] ?>"><?= htmlspecialchars($method['method_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-slate-300 mb-1">Amount (₱)</label>
                    <input type="number" name="amount" min="0" step="0.01" required
                        class="w-full p-2 text-sm border border-slate-700 rounded-md bg-slate-700 text-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-slate-300 mb-1">Description</label>
                    <textarea name="description"
                        class="w-full p-2 text-sm border border-slate-700 rounded-md bg-slate-700 text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        rows="2" placeholder="Enter payment description..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-3 border-t border-slate-700">
                    <button type="button" onclick="hidePaymentModal()"
                        class="px-3 py-1 text-sm text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" name="record_payment"
                        class="bg-indigo-600 text-white px-4 py-1 text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500">
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

        function printReceipt(clientName, methodName, amount, description, date) {
            // Format the date
            const formattedDate = new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Create receipt HTML
            const receiptHTML = `
                <html>
                <head>
                    <title>Payment Receipt</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; max-width: 400px; margin: 0 auto; }
                        .header { text-align: center; margin-bottom: 20px; }
                        .clinic-name { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
                        .receipt-title { font-size: 18px; margin-bottom: 20px; }
                        .details { margin-bottom: 20px; }
                        .detail-row { display: flex; margin-bottom: 8px; }
                        .detail-label { font-weight: bold; width: 120px; }
                        .thank-you { text-align: center; margin-top: 30px; font-style: italic; }
                        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #666; }
                        hr { border: 0; border-top: 1px dashed #ccc; margin: 20px 0; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="clinic-name">Vet Clinic</div>
                        <div>123 Clinic Street, Vet City</div>
                        <div>Phone: (123) 456-7890</div>
                        <hr>
                        <div class="receipt-title">PAYMENT RECEIPT</div>
                    </div>
                    
                    <div class="details">
                        <div class="detail-row">
                            <div class="detail-label">Date:</div>
                            <div>${formattedDate}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Client Name:</div>
                            <div>${clientName}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Payment Method:</div>
                            <div>${methodName}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Amount:</div>
                            <div>₱${parseFloat(amount).toFixed(2)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Description:</div>
                            <div>${description || 'N/A'}</div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="thank-you">
                        Thank you for your payment!
                    </div>
                    
                    <div class="footer">
                        This is an official receipt from Vet Clinic
                    </div>
                </body>
                </html>
            `;

            // Print the receipt
            const frame = document.getElementById('receiptFrame');
            frame.contentDocument.open();
            frame.contentDocument.write(receiptHTML);
            frame.contentDocument.close();
            setTimeout(() => {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            }, 500);
        }
    </script>

    <script src="./js/profile-dropdown.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
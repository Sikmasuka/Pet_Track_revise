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
 * Handle adding, updating payment methods and recording payments via POST requests
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
 * Fetch payment method data for editing if edit_method_id is set
 */
$methodToEdit = null;
if (isset($_GET['edit_method_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM Payment_Method WHERE method_id=?");
    $stmt->execute([$_GET['edit_method_id']]);
    $methodToEdit = $stmt->fetch();
}

/**
 * Fetch all payment methods ordered by name
 */
$stmt = $pdo->prepare("SELECT * FROM Payment_Method ORDER BY method_name ASC");
$stmt->execute();
$methods = $stmt->fetchAll();

/**
 * Fetch all payments joined with method names, ordered by date descending
 */
$stmt = $pdo->query("SELECT p.*, m.method_name FROM Payments p JOIN Payment_Method m ON p.method_id = m.method_id ORDER BY p.date DESC");
$payments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vet Clinic Payments</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <a href="profile.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-id-badge mr-2"></i>
                <span class="md:inline">Profile</span>
            </a>
            <a href="payment_methods.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
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
    <div class="ml-0 lg:ml-64 p-4 lg:p-8 pt-16 lg:pt-4 w-full">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-green-700">Vet Clinic Payments</h1>
                <div class="space-x-2">
                    <button onclick="showMethodModal('add')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Add Payment Method
                    </button>
                    <button onclick="showPaymentModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Record Payment
                    </button>
                </div>
            </div>


            <h2 class="text-lg font-semibold text-gray-700 mt-8 mb-2">Payment History</h2>
            <?php if (count($payments) > 0): ?>
                <table class="w-full table-auto border border-gray-200">
                    <thead>
                        <tr class="bg-blue-100 text-left">
                            <th class="px-4 py-2">Client</th>
                            <th class="px-4 py-2">Method</th>
                            <th class="px-4 py-2">Amount</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $pay): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?= htmlspecialchars($pay['client_name']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($pay['method_name']) ?></td>
                                <td class="px-4 py-2">₱<?= number_format($pay['amount'], 2) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($pay['description']) ?></td>
                                <td class="px-4 py-2"><?= date('F j, Y', strtotime($pay['date'])) ?></td>
                                <td class="px-4 py-2">
                                    <button onclick="printReceipt(`<?= htmlspecialchars($pay['client_name']) ?>`, `<?= htmlspecialchars($pay['method_name']) ?>`, `<?= $pay['amount'] ?>`, `<?= htmlspecialchars($pay['description']) ?>`, `<?= $pay['date'] ?>`)" class="text-green-600 hover:underline">
                                        Print
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-600">No payments recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center sm:p-5">
        <div class="bg-white rounded-lg shadow-lg w-96">
            <div class="w-full bg-green-500 rounded-t-lg text-white">
                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-center text-white m-0 py-3">Record Payment</h3>
            </div>
            
            <form method="POST" class="p-5">
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Client Name</label>
                    <input type="text" name="client_name" required class="w-full border px-3 py-2 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Payment Method</label>
                    <select name="method_id" required class="w-full border px-3 py-2 rounded">
                        <?php foreach ($methods as $method): ?>
                            <option value="<?= $method['method_id'] ?>"><?= htmlspecialchars($method['method_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Amount</label>
                    <input type="number" name="amount" min="0" step="0.01" required class="w-full border px-3 py-2 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Description</label>
                    <textarea name="description" class="w-full border px-3 py-2 rounded" rows="2"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="submit" name="record_payment" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Save</button>
                    <button type="button" onclick="hidePaymentModal()" class="text-gray-600 hover:underline">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Method Modal (reused) -->
    <div id="methodModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 id="modalTitle" class="text-xl font-semibold text-green-700 mb-4">Add New Method</h3>
            <form id="methodForm" method="POST">
                <input type="hidden" name="method_id" id="method_id">
                <div class="mb-4">
                    <label class="block text-sm text-gray-700">Method Name</label>
                    <input type="text" name="method_name" id="methodName" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Save</button>
                    <button type="button" onclick="hideMethodModal()" class="text-gray-600 hover:underline">Cancel</button>
                </div>
                <input type="hidden" name="add_payment_method" value="1">
            </form>
        </div>
    </div>

    <!-- Print Logic -->
    <iframe id="receiptFrame" class="hidden"></iframe>
    <script>
        function showMethodModal() {
            document.getElementById('methodForm').reset();
            document.getElementById('methodModal').classList.remove('hidden');
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

        function printReceipt(client, method, amount, description, date) {
            const content = `
                <html>
                <head>
                    <title>Receipt</title>
                    <style>
                        body {
                            font-family: sans-serif;
                            padding: 20px;
                            background-color: #f9f9f9;
                        }
                        .receipt-box {
                            background: #fff;
                            padding: 20px;
                            border-radius: 8px;
                            border: 1px solid #ccc;
                            max-width: 500px;
                            margin: auto;
                        }
                        .receipt-header {
                            background-color: #16a34a; /* green-600 */
                            color: #ffffff;
                            padding: 15px;
                            border-top-left-radius: 8px;
                            border-top-right-radius: 8px;
                            display: flex;
                            align-items: center;
                            text-align: center;
                            justify-content: center;
                            gap: 15px;
                        }
                        .logo {
                            width: 50px;
                            height: 50px;
                            object-fit: contain;
                        }
                        .clinic-info {
                            display: flex;
                            flex-direction: column;
                            line-height: 1.4;
                        }
                        .clinic-name {
                            font-size: 20px;
                            font-weight: bold;
                            margin: 0;
                            color:rgb(0, 0, 0);
                        }
                        .clinic-address {
                            font-size: 14px;
                            margin: 0;
                            color:rgb(63, 61, 61);
                        }
                        .receipt-body {
                            padding-top: 15px;
                            margin-left: 20px;
                        }
                        h2 {
                            margin-top: 20px;
                            margin-bottom: 10px;
                        }
                        .info-line {
                            margin: 4px 0;
                        }
                        .footer {
                            margin-top: 20px;
                            border-top: 1px dashed #ccc;
                            padding-top: 10px;
                            text-align: center;
                        }
                        .pr {
                            padding-top: 10px;
                            text-align: center;
                        }
                    </style>
                </head>
                <body onload="window.print()">
                    <div class="receipt-box">
                        <div class="receipt-header">
                            <img src="image/MainIcon.png" alt="Vet Clinic Logo" class="logo">
                            <div class="clinic-info">
                                <p class="clinic-name">Balingasag Dog and Cat Clinic</p>
                                <p class="clinic-address">Cogon, Balingasag, Misamis Oriental</p>
                            </div>
                        </div>
                        <div class="pr">
                            <h3 class="pr">Payment Receipt</h3>
                        </div>
                        <div class="receipt-body">
                            <p class="info-line"><strong>Client:</strong> ${client}</p>
                            <p class="info-line"><strong>Payment Method:</strong> ${method}</p>
                            <p class="info-line"><strong>Amount:</strong> ₱${parseFloat(amount).toFixed(2)}</p>
                            <p class="info-line"><strong>Description:</strong> ${description}</p>
                            <p class="info-line"><strong>Date:</strong> ${date}</p>
                        </div>
                        <div class="footer">
                            <p>Thank you for your payment!</p>
                        </div>
                    </div>
                </body>
                </html>
            `;

            const frame = document.getElementById('receiptFrame').contentWindow;
            frame.document.open();
            frame.document.write(content);
            frame.document.close();
        }


        <?php if ($methodToEdit): ?>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('methodModal');
                const form = document.getElementById('methodForm');
                document.getElementById('modalTitle').textContent = 'Edit Payment Method';
                form.querySelector('input[name="method_id"]').value = '<?= $methodToEdit['method_id'] ?>';
                document.getElementById('methodName').value = '<?= htmlspecialchars($methodToEdit['method_name']) ?>';
                form.innerHTML += '<input type="hidden" name="update_payment_method" value="1">';
                modal.classList.remove('hidden');
            });
        <?php endif; ?>
    </script>
    <script src="./js/sidebarHandler.js"></script>
</body>

</html>
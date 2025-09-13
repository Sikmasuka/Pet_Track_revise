<?php
session_start();
require_once '../record-handler.php';
include(__DIR__ . '/../../includes/sitemap/Help/support.php');

// Fetch clinic details
try {
    $stmt = $pdo->query("SELECT name, address, phone FROM Clinic_Details WHERE id = 1");
    $clinic = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$clinic) {
        $clinic = ['name' => 'Vet Clinic', 'address' => '123 Clinic Street, Vet City', 'phone' => '(123) 456-7890'];
    }
} catch (PDOException $e) {
    $clinic = ['name' => 'Vet Clinic', 'address' => '123 Clinic Street, Vet City', 'phone' => '(123) 456-7890'];
}

// Fetch payments with applied filters
$query = "SELECT p.*, m.method_name, YEAR(p.date) AS payment_year, MONTH(p.date) AS payment_month 
          FROM Payments p 
          JOIN Payment_Methods m ON p.method_id = m.method_id";
$conditions = [];
$params = [];
if (isset($_GET['year']) && $_GET['year'] !== 'All' && is_numeric($_GET['year'])) {
    $conditions[] = "YEAR(p.date) = ?";
    $params[] = $_GET['year'];
}
if (isset($_GET['month']) && $_GET['month'] !== 'All' && is_numeric($_GET['month']) && $_GET['month'] >= 1 && $_GET['month'] <= 12) {
    $conditions[] = "MONTH(p.date) = ?";
    $params[] = $_GET['month'];
}
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}
$query .= " ORDER BY p.date DESC";
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payments = [];
}

// Calculate total payments
$total_amount = array_sum(array_column($payments, 'amount'));

// Fetch all payments grouped by month and year for print report
$query_all = "SELECT p.*, m.method_name, YEAR(p.date) AS payment_year, MONTH(p.date) AS payment_month 
              FROM Payments p 
              JOIN Payment_Methods m ON p.method_id = m.method_id 
              ORDER BY p.date DESC";
try {
    $stmt_all = $pdo->prepare($query_all);
    $stmt_all->execute();
    $all_payments = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_payments = [];
}

// Group payments by year and month
$monthly_payments = [];
foreach ($all_payments as $pay) {
    $year = $pay['payment_year'];
    $month = $pay['payment_month'];
    if (!isset($monthly_payments[$year])) {
        $monthly_payments[$year] = [];
    }
    if (!isset($monthly_payments[$year][$month])) {
        $monthly_payments[$year][$month] = [
            'payments' => [],
            'total' => 0,
            'count' => 0
        ];
    }
    $monthly_payments[$year][$month]['payments'][] = $pay;
    $monthly_payments[$year][$month]['total'] += $pay['amount'];
    $monthly_payments[$year][$month]['count']++;
}

// Calculate total for all payments
$total_amount_all = array_sum(array_column($all_payments, 'amount'));

// Define months for filter
$months = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];

// Fetch available years for filter
try {
    $stmt = $pdo->query("SELECT DISTINCT YEAR(date) AS year FROM Payments ORDER BY year DESC");
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $years = [date('Y')];
}

// Determine period for print report
$period = 'All Time';
if (isset($_GET['year']) && $_GET['year'] !== 'All' && is_numeric($_GET['year'])) {
    $period = $_GET['year'];
    if (isset($_GET['month']) && $_GET['month'] !== 'All' && is_numeric($_GET['month']) && $_GET['month'] >= 1 && $_GET['month'] <= 12) {
        $period = $months[$_GET['month']] . ' ' . $_GET['year'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../image/MainIcon.png" type="image/x-icon">
    <title>Manage Payments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../Assets/Extension.js"></script>
    <style>
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 400px;
        }

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

        /* Print-specific styles */
        .print-report {
            display: none;
            font-family: 'Helvetica', Arial, sans-serif;
            max-width: 900px;
            margin: 20px auto;
            color: #333;
            line-height: 1.6;
        }

        @media print {
            body {
                margin: 0;
                font-size: 12pt;
            }

            body * {
                visibility: hidden;
            }

            .print-report,
            .print-report * {
                visibility: visible;
            }

            .print-report {
                display: block !important;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            .sidebar,
            .header,
            .filter-form,
            .table-container,
            button {
                display: none !important;
            }

            .print-report .header {
                text-align: center;
                margin-bottom: 30px;
            }

            .print-report .clinic-name {
                font-size: 28pt;
                font-weight: bold;
                color: #2ecc71;
                margin-bottom: 10px;
            }

            .print-report .clinic-details {
                font-size: 14pt;
                color: #555;
            }

            .print-report .report-title {
                font-size: 20pt;
                font-weight: bold;
                margin: 20px 0;
                text-transform: uppercase;
            }

            .print-report .summary {
                background-color: #f8fafc;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 30px;
                font-size: 14pt;
                border: 1px solid #e2e8f0;
                page-break-inside: avoid;
            }

            .print-report table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
                font-size: 10pt;
            }

            .print-report th,
            .print-report td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }

            .print-report th {
                background-color: #e2e8f0;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 10pt;
            }

            .print-report .amount {
                text-align: right;
            }

            .print-report .total-row td {
                font-weight: bold;
                background-color: #f1f5f9;
            }

            .print-report .no-data {
                text-align: center;
                padding: 20px;
                background-color: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 8px;
                color: #b91c1c;
                page-break-inside: avoid;
                font-size: 14pt;
            }

            .print-report .month-section {
                margin-bottom: 40px;
                page-break-inside: avoid;
            }

            .print-report .month-title {
                font-size: 16pt;
                font-weight: bold;
                margin: 20px 0 10px;
                color: #2b6cb0;
            }

            .print-report .footer {
                text-align: center;
                font-style: italic;
                margin-top: 30px;
                font-size: 12pt;
                color: #666;
                page-break-inside: avoid;
            }

            .print-report .signature-section {
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #ccc;
                page-break-inside: avoid;
            }

            .print-report .signature-section p {
                font-size: 12pt;
                margin: 10px 0;
                font-weight: bold;
            }

            .print-report .signature-line {
                border-bottom: 1px solid #000;
                width: 250px;
                margin: 20px 0;
                display: inline-block;
            }

            .print-report hr {
                border: 0;
                border-top: 1px dashed #ccc;
                margin: 30px 0;
            }

            @page {
                margin: 1in;
            }
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen text-gray-800">
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-slate-700 text-white p-3 rounded-md shadow-lg hover:bg-slate-600 transition-colors">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-[200px] bg-gradient-to-b from-emerald-600 via-teal-700 to-emerald-800 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col border-r border-emerald-900">
        <div class="flex items-center justify-between">
            <h2 class="text-xl lg:text-2xl font-semibold flex items-center gap-2">
                <img src="../../image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden text-gray-300 hover:text-white duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-grow mt-8 lg:mt-12 space-y-0.5">
            <a href="../admin-dashboard.php" class="block text-sm text-white hover:bg-emerald-700 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="../admin.php" class="block text-sm text-white hover:bg-emerald-700 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-user-md mr-2"></i> Veterinarians
            </a>
            <div class="space-y-0.5">
                <button id="recordsBtn" class="w-full flex items-center justify-start gap-2 text-sm text-white px-4 py-2 rounded-md hover:bg-emerald-700 transition-colors">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Records</span>
                    <svg id="recordsArrow" class="w-4 h-4 ml-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="recordsMenu" class="max-h-0 overflow-hidden opacity-0 transition-all duration-200 ease-in-out pl-8 space-y-1">
                    <a href="../records/pet-records.php" class="flex items-center text-sm text-gray-200 hover:bg-emerald-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        <i class="fas fa-paw mr-2"></i> Pets
                    </a>
                    <a href="../records/client-records.php" class="flex items-center text-sm text-gray-200 hover:bg-emerald-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        <i class="fas fa-user mr-2"></i> Clients
                    </a>
                    <a href="../records/medical-records.php" class="flex items-center text-sm text-gray-200 hover:bg-emerald-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        <i class="fas fa-file-medical mr-2"></i> Medical Records
                    </a>
                    <a href="../records/payment-records.php" class="flex items-center text-sm text-gray-200 bg-teal-800 hover:bg-emerald-600 px-3 py-2 rounded-md hover:text-white transition-colors">
                        <i class="fas fa-credit-card mr-2"></i> Payments Records
                    </a>
                </div>
            </div>
            <a href="../admin-appointments.php" class="block text-sm text-white hover:bg-emerald-700 px-4 py-2 rounded-md">
                <i class="fas fa-calendar-days mr-2"></i> Appointments
            </a>
            <a href="#" onclick="toggleModal('adminHelpModal')" class="block text-sm text-gray-200 hover:bg-emerald-600 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
            </a>
        </nav>
        <div class="pt-4">
            <a href="../index.php" onclick="confirmLogout(event)" class="block text-sm text-gray-200 hover:bg-red-600 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main Dashboard Container -->
    <div class="ml-0 lg:ml-52 p-4 pt-16 lg:pt-4">
        <header class="bg-white shadow-lg rounded-lg text-gray-800 py-4 mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-200">
            <div class="flex justify-between items-center">
                <h1 class="text-xl lg:text-2xl font-bold">Manage Payments</h1>
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
                                    <p class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($vetName); ?></p>
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
                            <a href="#" onclick="confirmLogout(event)" class="flex items-center gap-3 px- COLLAPSE_TEXT
                            <div>
                                <div class=" font-medium">Logout
                        </div>
                        <div class="text-xs text-red-600">Sign out of your account</div>
                    </div>
                    </a>
                </div>
            </div>
    </div>
    </header>

    <div class="bg-white shadow-lg rounded-lg text-gray-800 py-4 mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-200">
        <?php if (count($payments) > 0): ?>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold mb-4">Payment History</h2>
                <button onclick="printReport()" class="bg-emerald-600 hover:bg-emerald-700 duration-200 text-white px-4 py-2 rounded-lg shadow">
                    🖨️ Print Report
                </button>
            </div>
            <form method="GET" class="flex flex-row gap-6 mb-4">
                <div>
                    <label for="yearFilter" class="text-sm font-medium text-gray-700 mr-2">Filter by Year:</label>
                    <select name="year" id="yearFilter" class="border border-gray-300 rounded-lg px-4 cursor-pointer py-1 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none" onchange="this.form.submit()">
                        <option value="All" <?php echo (!isset($_GET['year']) || $_GET['year'] === 'All') ? 'selected' : ''; ?>>All</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo (isset($_GET['year']) && $_GET['year'] == $year) ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="monthFilter" class="text-sm font-medium text-gray-700 mr-2">Filter by Month:</label>
                    <select name="month" id="monthFilter" class="border border-gray-300 rounded-lg px-4 cursor-pointer py-1 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none" onchange="this.form.submit()">
                        <option value="All" <?php echo (!isset($_GET['month']) || $_GET['month'] === 'All') ? 'selected' : ''; ?>>All</option>
                        <?php foreach ($months as $num => $name): ?>
                            <option value="<?php echo $num; ?>" <?php echo (isset($_GET['month']) && $_GET['month'] == $num) ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <div class="table-container">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-gray-300 sticky top-0 z-2">
                        <tr class="border-b border-slate-200">
                            <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Client</th>
                            <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Method</th>
                            <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Amount</th>
                            <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Description</th>
                            <th class="px-2 py-3 text-left text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wider min-w-[120px] whitespace-nowrap overflow-hidden truncate">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php foreach ($payments as $pay): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2 text-gray-700"><?php echo htmlspecialchars($pay['client_name']); ?></td>
                                <td class="px-4 py-2 text-gray-700"><?php echo htmlspecialchars($pay['method_name']); ?></td>
                                <td class="px-4 py-2 text-gray-700 font-medium">₱<?php echo number_format($pay['amount'], 2); ?></td>
                                <td class="px-4 py-2 text-gray-700 truncate-cell" title="<?php echo htmlspecialchars($pay['description']); ?>">
                                    <?php echo htmlspecialchars($pay['description']); ?>
                                </td>
                                <td class="px-4 py-2 text-gray-700"><?php echo date('M j, Y', strtotime($pay['date'])); ?></td>
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

    <!-- Add Veterinarian Modal -->
    <div id="addModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div id="modalContent" class="bg-white rounded-lg shadow-lg w-full overflow-hidden border border-slate-200">
            <div class="bg-indigo-500 px-6 py-4">
                <h3 id="petModalTitle" class="text-lg lg:text-xl font-bold text-center text-white">
                    Add Veterinarian
                </h3>
            </div>
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

    <!-- Print Report Section -->
    <div id="print-report" class="print-report">
        <div class="header">
            <div class="clinic-name"><?php echo htmlspecialchars($clinic['name']); ?></div>
            <div class="clinic-details"><?php echo htmlspecialchars($clinic['address']); ?></div>
            <div class="clinic-details"><?php echo htmlspecialchars($clinic['phone']); ?></div>
            <hr>
            <div class="report-title">Payment Report</div>
            <div class="clinic-details">Generated on <?php echo date('F j, Y'); ?></div>
        </div>
        <div class="summary">
            <p><strong>Summary for <?php echo htmlspecialchars($period); ?></strong></p>
            <p>Total Payments: ₱<?php echo number_format($total_amount_all, 2); ?></p>
            <p>Total Records: <?php echo count($all_payments); ?></p>
        </div>
        <?php if (empty($all_payments)): ?>
            <div class="no-data">No payments found for the selected period.</div>
        <?php else: ?>
            <?php foreach ($monthly_payments as $year => $months_data): ?>
                <?php foreach ($months_data as $month => $data): ?>
                    <?php if (isset($_GET['year']) && $_GET['year'] !== 'All' && $year != $_GET['year']) continue; ?>
                    <?php if (isset($_GET['month']) && $_GET['month'] !== 'All' && $month != $_GET['month']) continue; ?>
                    <div class="month-section">
                        <div class="month-title"><?php echo $months[$month] . ' ' . $year; ?></div>
                        <div class="summary">
                            <p>Total Payments: ₱<?php echo number_format($data['total'], 2); ?></p>
                            <p>Total Records: <?php echo $data['count']; ?></p>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Payment Method</th>
                                    <th class="amount">Amount (₱)</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['payments'] as $pay): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pay['client_name']); ?></td>
                                        <td><?php echo htmlspecialchars($pay['method_name']); ?></td>
                                        <td class="amount"><?php echo number_format($pay['amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($pay['description'] ?: 'N/A'); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($pay['date'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="total-row">
                                    <td colspan="2">Total for <?php echo $months[$month]; ?></td>
                                    <td class="amount"><?php echo number_format($data['total'], 2); ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <div class="summary">
                <p><strong>Grand Total</strong></p>
                <p>Total Payments: ₱<?php echo number_format($total_amount_all, 2); ?></p>
            </div>
        <?php endif; ?>
        <hr>
        <div class="signature-section">
            <p>Prepared by:</p>
            <div class="signature-line"></div>
            <p>Name: _____________________________</p>
            <p>Signature: _________________________</p>
        </div>
        <div class="footer">
            Generated by <?php echo htmlspecialchars($clinic['name']); ?>. Thank you for your continued trust.
        </div>
    </div>

    <script>
        function printReport() {
            const images = document.querySelectorAll('img');
            let loadedImages = 0;
            const totalImages = images.length;

            if (totalImages === 0) {
                triggerPrint();
            } else {
                images.forEach(img => {
                    if (img.complete) {
                        loadedImages++;
                        if (loadedImages === totalImages) {
                            triggerPrint();
                        }
                    } else {
                        img.onload = () => {
                            loadedImages++;
                            if (loadedImages === totalImages) {
                                triggerPrint();
                            }
                        };
                        img.onerror = () => {
                            loadedImages++;
                            if (loadedImages === totalImages) {
                                triggerPrint();
                            }
                        };
                    }
                });
            }
        }

        function triggerPrint() {
            const printSection = document.getElementById('print-report');
            printSection.style.display = 'block';
            void printSection.offsetHeight;
            setTimeout(() => {
                window.print();
                printSection.style.display = 'none';
            }, 100);
        }

        const recordsBtn = document.getElementById('recordsBtn');
        const recordsMenu = document.getElementById('recordsMenu');
        const recordsArrow = document.getElementById('recordsArrow');
        recordsBtn.addEventListener('click', () => {
            if (recordsMenu.classList.contains('max-h-0')) {
                recordsMenu.classList.remove('max-h-0', 'opacity-0');
                recordsMenu.classList.add('max-h-96', 'opacity-100');
            } else {
                recordsMenu.classList.remove('max-h-96', 'opacity-100');
                recordsMenu.classList.add('max-h-0', 'opacity-0');
            }
            recordsArrow.classList.toggle('rotate-180');
        });

        const submenuLinks = document.querySelectorAll('#recordsMenu a');
        submenuLinks.forEach(link => {
            link.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        window.addEventListener('afterprint', () => {
            document.getElementById('print-report').style.display = 'none';
        });
    </script>
    <script src="../../js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../js/confirmLogout.js"></script>
</body>

</html>
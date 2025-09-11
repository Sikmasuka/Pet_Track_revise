<?php
include 'db.php';

// Query payments with method name
$query = "
    SELECT p.payment_id, p.client_name, m.method_name, p.amount, p.description, p.date
    FROM payments p
    JOIN payment_methods m ON p.method_id = m.method_id
    ORDER BY p.date DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Report generated timestamp
$generatedDate = date("F d, Y h:i A");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payments Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white !important;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-xl p-6">
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-700">St. Rita’s College of Balingasag</h2>
                <p class="text-sm text-gray-500">Dean of Student Affairs Office</p>
                <p class="text-sm text-gray-500">Pet Clinic Management System</p>
            </div>
            <div>
                <img src="../Images/logo.png" alt="Logo" class="h-16 w-16">
            </div>
        </div>

        <!-- Report Title -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-red-600">Payments Report</h1>
            <p class="text-sm text-gray-600">Generated on <?= $generatedDate ?></p>
        </div>

        <!-- Table -->
        <div id="reportArea">
            <table class="w-full border border-gray-400 rounded-lg overflow-hidden text-sm">
                <thead class="bg-gray-200 text-gray-700 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="p-3 border">#</th>
                        <th class="p-3 border">Client</th>
                        <th class="p-3 border">Method</th>
                        <th class="p-3 border">Amount</th>
                        <th class="p-3 border">Description</th>
                        <th class="p-3 border">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 border text-center"><?= $row['payment_id'] ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($row['client_name']) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($row['method_name']) ?></td>
                            <td class="p-2 border text-right">₱<?= number_format($row['amount'], 2) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($row['description']) ?></td>
                            <td class="p-2 border text-center"><?= date("M d, Y h:i A", strtotime($row['date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-12 flex justify-between text-sm">
            <div>
                <p>Prepared by:</p>
                <br><br>
                <p class="font-semibold underline">__________________________</p>
                <p>Staff / Cashier</p>
            </div>
            <div>
                <p>Approved by:</p>
                <br><br>
                <p class="font-semibold underline">__________________________</p>
                <p>Dean of Student Affairs</p>
            </div>
        </div>

        <!-- Print Button -->
        <div class="mt-6 no-print">
            <button onclick="printReport()"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow">
                🖨️ Print Report
            </button>
        </div>
    </div>

    <script>
        function printReport() {
            window.print();
        }
    </script>
</body>

</html>
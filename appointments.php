<?php
session_start();
require_once __DIR__ . "/db.php"; // Connect to database

// Get my name from the database
$stmt = $pdo->prepare("SELECT vet_name FROM veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$my_name_data = $stmt->fetch(PDO::FETCH_ASSOC);
$my_name = $my_name_data ? $my_name_data['vet_name'] : "Unknown Vet";

// Get current month and year, or use URL if changed
$cur_year = date('Y'); // Current year
$cur_month = date('m'); // Current month
if (isset($_GET['month']) && isset($_GET['year'])) {
    $cur_month = str_pad((int)$_GET['month'], 2, '0', STR_PAD_LEFT);
    $cur_year = (int)$_GET['year'];
    if ($cur_month < 1) {
        $cur_month = 12;
        $cur_year--;
    }
    if ($cur_month > 12) {
        $cur_month = 1;
        $cur_year++;
    }
}
$start_date = "$cur_year-$cur_month-01";
$end_date = date("Y-m-t", strtotime($start_date));

// Set up pagination
$items_per_page = 10; // Show 10 appointments per page
$page_num = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start_point = ($page_num - 1) * $items_per_page;

// Count total appointments
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date BETWEEN :start_date AND :end_date");
$count_stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$total_items = $count_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

// Get appointments for this page
$stmt = $pdo->prepare("
    SELECT owner_name, contact_number, appointment_date, appointment_time, reason
    FROM appointments
    WHERE appointment_date BETWEEN :start_date AND :end_date
    ORDER BY appointment_date, appointment_time
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
$stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $start_point, PDO::PARAM_INT);
$stmt->execute();
$appoint_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// List of dates with appointments for calendar
$appoint_dates = [];
foreach ($appoint_list as $appt) {
    $appoint_dates[$appt['appointment_date']] = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .chart-container {
            height: 300px;
            width: 100%;
        }

        @media (min-width: 768px) {
            .chart-container {
                height: 400px;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-green-600 text-white p-3 rounded-md">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 w-50 bg-gradient-to-b from-green-500 to-green-600 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl lg:text-2xl font-semibold mb-6 flex items-center gap-2">
                <img src="image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span>Dashboard</span>
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden top-4 right-4 text-white hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="mt-8">
            <a href="dashboard.php" class="block text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
            <a href="clients.php" class="block text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-user mr-2"></i>Clients
            </a>
            <a href="pets.php" class="block text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-paw mr-2"></i>Pets
            </a>
            <a href="medical_records.php" class="block text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-file-medical mr-2"></i>Medical Records
            </a>
            <a href="profile.php" class="block text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-id-badge mr-2"></i>Profile
            </a>
            <a href="payment_methods.php" class="block text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-credit-card mr-2"></i>Payments
            </a>
            <a href="appointments.php" class="block text-md text-white bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-calendar-days mr-2"></i>Appointments
            </a>
            <a href="archive.php" class="block text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fa-solid fa-box-archive mr-2"></i>Archive
            </a>
            <a href="#" onclick="confirmLogout(event)" class="block text-md text-white hover:bg-green-600 px-4 py-2 mb-1 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <!-- Main content -->
    <div class="ml-0 lg:ml-52 p-4 pt-12">
        <header class="bg-white rounded-lg text-green-800 py-4 shadow-sm mb-6 p-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h1 class="text-lg font-bold">Hello, <?php echo $my_name; ?>.</h1>
                <h1 class="text-lg font-bold">Appointments</h1>
            </div>
        </header>

        <!-- Calendar -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-green-800 mb-4">Appointment Days</h2>
            <div class="mt-2 p-2 bg-gray-100 rounded-md border">
                <div class="flex justify-between mb-2">
                    <a href="?month=<?php echo $cur_month - 1 ?: 12; ?>&year=<?php echo $cur_month - 1 ? $cur_year : $cur_year - 1; ?>" class="px-2 bg-[#169976] text-white rounded">&lt;</a>
                    <span class="text-sm font-semibold"><?php echo date('F Y', strtotime($start_date)); ?></span>
                    <a href="?month=<?php echo $cur_month + 1 <= 12 ? $cur_month + 1 : 1; ?>&year=<?php echo $cur_month + 1 <= 12 ? $cur_year : $cur_year + 1; ?>" class="px-2 bg-[#169976] text-white rounded">&gt;</a>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center">
                    <?php
                    $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    foreach ($days as $day) {
                        echo "<div class='text-gray-500 text-xs py-1'>$day</div>";
                    }
                    $first_day = date('w', strtotime($start_date));
                    $last_day = date('t', strtotime($start_date));
                    $day_count = 1;
                    for ($i = 0; $i < 6; $i++) {
                        for ($j = 0; $j < 7; $j++) {
                            if ($i === 0 && $j < $first_day) {
                                echo "<div class='p-1 text-center text-sm text-transparent'></div>";
                            } elseif ($day_count <= $last_day) {
                                $cur_date = "$cur_year-$cur_month-" . str_pad($day_count, 2, '0', STR_PAD_LEFT);
                                $class = 'p-1 text-center text-sm';
                                if (isset($appoint_dates[$cur_date])) {
                                    $class .= ' bg-red-500 text-white rounded-full p-2';
                                } elseif (strtotime($cur_date) < time()) {
                                    $class .= ' text-gray-300';
                                } else {
                                    $class .= ' hover:bg-blue-100 rounded-full';
                                }
                                echo "<div class='$class'>$day_count</div>";
                                $day_count++;
                            }
                        }
                        if ($day_count > $last_day) break;
                    }
                    ?>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-1">Pick a date.</p>
        </div>

        <!-- Appointment Table -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-green-800 mb-4">Appointment List</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Phone</th>
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Reason</th>
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Date</th>
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-700">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appoint_list)): ?>
                            <tr>
                                <td colspan="5" class="py-2 px-4 border-b text-center text-sm text-gray-500">No appointments this month.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appoint_list as $appt): ?>
                                <tr>
                                    <td class="py-2 px-4 border-b text-sm"><?php echo htmlspecialchars($appt['owner_name'] ?? 'N/A'); ?></td>
                                    <td class="py-2 px-4 border-b text-sm"><?php echo htmlspecialchars($appt['contact_number'] ?? 'N/A'); ?></td>
                                    <td class="py-2 px-4 border-b text-sm"><?php echo htmlspecialchars($appt['reason'] ?? 'N/A'); ?></td>
                                    <td class="py-2 px-4 border-b text-sm"><?php echo htmlspecialchars($appt['appointment_date'] ?? 'N/A'); ?></td>
                                    <td class="py-2 px-4 border-b text-sm"><?php echo htmlspecialchars($appt['appointment_time'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Links -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-4 flex justify-between">
                    <a href="?month=<?php echo $cur_month; ?>&year=<?php echo $cur_year; ?>&page=<?php echo $page_num - 1; ?>" class="px-4 py-2 bg-gray-200 rounded <?php echo $page_num <= 1 ? 'opacity-50 cursor-not-allowed' : ''; ?>">Previous</a>
                    <span>Page <?php echo $page_num; ?> of <?php echo $total_pages; ?></span>
                    <a href="?month=<?php echo $cur_month; ?>&year=<?php echo $cur_year; ?>&page=<?php echo $page_num + 1; ?>" class="px-4 py-2 bg-gray-200 rounded <?php echo $page_num >= $total_pages ? 'opacity-50 cursor-not-allowed' : ''; ?>">Next</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
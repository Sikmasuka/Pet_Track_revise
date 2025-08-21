<?php
session_start();
require_once __DIR__ . "/db.php"; // Adjust path to your PDO connection file
require_once __DIR__ . "/functions/logs.php"; // Include the logs.php file
include "includes/sitemap/Help/support.php";


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

// Function to get the current user's role (hypothetical, adjust based on your session/auth system)
function getUserRole()
{
    // Example: Assume role is stored in session
    return isset($_SESSION['role']) ? $_SESSION['role'] : 'Veterinarian'; // Default to Veterinarian
}

// Log appointment booking action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['owner_name'])) {
    try {
        $userId = $_SESSION['vet_id'] ?? 1; // Use vet_id from session, fallback to 1 for testing
        $userRole = getUserRole();
        logAction($pdo, $userId, 'Appointment_Booked', "Booked for {$_POST['owner_name']} on {$_POST['appointment_date']} at {$_POST['appointment_time']}", $userRole);
    } catch (PDOException $e) {
        file_put_contents('debug.log', "Log error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

// Define $start_date and $end_date first
$cur_year = 2025;
$cur_month = 8; // August
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

// Fetch logs related to appointments for the current month
$log_stmt = $pdo->prepare("
    SELECT User_ID AS name, Description, Timestamp, Role 
    FROM Logs 
    WHERE Timestamp BETWEEN :start_date AND :end_date 
    ORDER BY Timestamp DESC
");
$log_stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$logs = $log_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get my name from the database
$stmt = $pdo->prepare("SELECT vet_name FROM veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$my_name_data = $stmt->fetch(PDO::FETCH_ASSOC);
$my_name = $my_name_data ? $my_name_data['vet_name'] : "Unknown Vet";

// Get ALL appointments for this month (without LIMIT for combining with logs)
$stmt = $pdo->prepare("
    SELECT owner_name, contact_number, appointment_date, appointment_time, reason
    FROM appointments
    WHERE appointment_date BETWEEN :start_date AND :end_date
    ORDER BY appointment_date, appointment_time
");
$stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$appoint_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set up pagination for appointments only
$items_per_page = 10;
$page_num = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Calculate pagination based on appointments only
$total_items = count($appoint_list);
$total_pages = ceil($total_items / $items_per_page);
$start_point = ($page_num - 1) * $items_per_page;

// Get the paginated slice of appointments
$paginated_data = array_slice($appoint_list, $start_point, $items_per_page);

// Handle form submission (from booking modal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['owner_name']) && isset($_POST['appointment_date'])) {
    file_put_contents('debug.log', "Form submitted: " . print_r($_POST, true) . "\n", FILE_APPEND);
    try {
        $owner_name = trim($_POST['owner_name']);
        $contact_number = trim($_POST['contact_number']);
        $appointment_date = trim($_POST['appointment_date']);
        $appointment_time = trim($_POST['appointment_time']);
        $reason = trim($_POST['reason']);

        date_default_timezone_set('Asia/Manila'); // Match time zone
        $dateObj = DateTime::createFromFormat('Y-m-d', $appointment_date, new DateTimeZone('Asia/Manila'));
        if (!$dateObj || $dateObj->format('Y-m-d') !== $appointment_date) {
            $_SESSION['error'] = "Invalid date format. Please use YYYY-MM-DD.";
            header("Location: Appointments.php?month=$cur_month&year=$cur_year");
            exit();
        }

        $dateTime = new DateTime("$appointment_date $appointment_time", new DateTimeZone('Asia/Manila'));
        $now = new DateTime('now', new DateTimeZone('Asia/Manila')); // Current time: 10:09 AM PHT, August 18, 2025
        if ($dateTime < $now) {
            $_SESSION['error'] = "Cannot book appointments before " . $now->format('Y-m-d H:i:s') . " PHT.";
            header("Location: Appointments.php?month=$cur_month&year=$cur_year");
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO appointments (owner_name, contact_number, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, 'Scheduled')");
        $stmt->execute([$owner_name, $contact_number, $appointment_date, $appointment_time, $reason]);
        if ($stmt->rowCount() > 0) {
            file_put_contents('debug.log', "Appointment inserted successfully\n", FILE_APPEND);
            // Log the action
            $userId = $_SESSION['vet_id'] ?? 1; // Use vet_id, fallback for testing
            $userRole = getUserRole();
            $description = "Guest $owner_name booked an appointment on $appointment_date at $appointment_time";
            logAction($pdo, $userId, 'Appointment', $description, $userRole);
            $_SESSION['success'] = "Appointment booked successfully!";
            header("Location: Appointments.php?month=$cur_month&year=$cur_year&reload=1");
            exit();
        } else {
            file_put_contents('debug.log', "No rows inserted\n", FILE_APPEND);
            $_SESSION['error'] = "Appointment was not saved.";
            header("Location: Appointments.php?month=$cur_month&year=$cur_year");
            exit();
        }
    } catch (PDOException $e) {
        file_put_contents('debug.log', "Error booking appointment: " . $e->getMessage() . "\n", FILE_APPEND);
        $_SESSION['error'] = "Error booking appointment: " . $e->getMessage();
        header("Location: Appointments.php?month=$cur_month&year=$cur_year");
        exit();
    }
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
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
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

        #calendar {
            width: 100%;
            margin: 0 auto;
            height: 400px;
        }

        /* Custom styles for calendar day indicators */
        .fc-daygrid-day-number {
            cursor: pointer;
            position: relative;
            z-index: 1;
        }

        .fc-daygrid-day.fc-day-other {
            display: none;
        }

        .fc-daygrid-day {
            transition: background-color 0.3s;
            position: relative;
        }

        /* Appointment indicator styles */
        .appointment-indicator {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            z-index: 2;
        }

        .appointment-indicator.has-appointments {
            background-color: #28a745;
        }

        .appointment-indicator.full-day {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #dc3545;
        }

        /* Day with appointments styling */
        .fc-daygrid-day.has-appointments {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }

        .fc-daygrid-day.full-day {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .fc-daygrid-day.full-day .fc-daygrid-day-number {
            color: #dc3545 !important;
            font-weight: bold;
        }

        .fc-daygrid-day.has-appointments .fc-daygrid-day-number {
            color: #28a745 !important;
            font-weight: bold;
        }

        #appointmentModal .appointment-details,
        #bookingModal .appointment-details {
            max-height: 60vh;
            overflow-y: auto;
        }

        /* Legend styles */
        .calendar-legend {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .legend-circle {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .legend-available {
            background-color: #e5e7eb;
        }

        .legend-has-appointments {
            background-color: #28a745;
        }

        .legend-full {
            background-color: #dc3545;
        }
    </style>
</head>

<body class="bg-slate-900 min-h-screen">

    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-slate-700 text-white p-3 rounded-md shadow-lg hover:bg-slate-600 transition-colors">
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
            <a href="dashboard.php" class="block text-sm text-white hover:bg-slate-700 px-4 py-2 rounded-md hover:bg-slate-600 transition-colors">
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
            <a href="payment_methods.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-credit-card mr-2"></i> Payments
            </a>
            <a href="appointments.php" class="block text-sm text-gray-300 bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fas fa-calendar-days mr-2"></i> Appointments
            </a>
            <a href="archive.php" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors">
                <i class="fa-solid fa-box-archive mr-2"></i> Archive
            </a>
            <a href="#" class="block text-sm text-gray-300 hover:bg-slate-700 px-4 py-2 rounded-md hover:text-white transition-colors" onclick="toggleModal('vetHelpModal')">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
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

    <!-- Main content -->
    <div class="ml-0 lg:ml-52 p-4 pt-16 lg:pt-4">

        <!-- header -->
        <header class="bg-slate-800 rounded-lg text-white py-4 shadow-sm mb-6 lg:mb-8 p-4 lg:p-6 border border-slate-700">
            <!-- Top Section with Dropdown -->
            <div class="flex justify-between items-center mb-6">

                <!-- Dashboard Title -->
                <h1 class="text-xl lg:text-2xl font-bold">Medical Records</h1>

                <!-- Profile Dropdown -->
                <div class="relative inline-block text-left">
                    <button id="profileButton" class="flex items-center justify-center w-10 h-10 bg-slate-700 border border-slate-600 rounded-full hover:bg-slate-600 text-white text-lg transition-colors">
                        <i class="fas fa-user"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="dropdownMenu"
                        class="origin-top-right absolute right-0 mt-2 w-72 rounded-lg shadow-lg bg-slate-800 ring-1 ring-black ring-opacity-5 opacity-0 scale-95 pointer-events-none transition-all duration-200 ease-out z-50 border border-slate-700">
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

        <!-- FullCalendar -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Appointment Days</h2>

            <!-- Calendar Legend -->
            <div class="calendar-legend">
                <div class="legend-item">
                    <div class="legend-circle legend-available"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-circle legend-has-appointments"></div>
                    <span>Has Appointments</span>
                </div>
                <div class="legend-item">
                    <div class="legend-circle legend-full"></div>
                    <span>Full (6/6)</span>
                </div>
            </div>

            <div id='calendar'></div>
        </div>

        <!-- Appointment Table -->
        <div class="bg-slate-600 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-white mb-4">Appointment List</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-slate-300 border border-gray-300 rounded-md overflow-hidden">
                    <thead>
                        <tr class="bg-slate-500">
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-white">#</th>
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-white">Owner Name</th>
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-white">Phone</th>
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-white">Reason</th>
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-white">Date</th>
                            <th class="py-2 px-4 border-b text-left text-sm font-semibold text-white">Time</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        <?php if (empty($paginated_data)): ?>
                            <tr>
                                <td colspan="6" class="py-2 px-4 border-b text-center text-sm text-gray-500">No appointments this month.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($paginated_data as $index => $appointment): ?>
                                <?php
                                $serial = $start_point + $index + 1;
                                $name = htmlspecialchars($appointment['owner_name']);
                                $phone = htmlspecialchars($appointment['contact_number']);
                                $reason = htmlspecialchars($appointment['reason']);
                                $date = htmlspecialchars($appointment['appointment_date']);
                                $time = htmlspecialchars($appointment['appointment_time']);
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b text-sm"><?= $serial ?></td>
                                    <td class="py-2 px-4 border-b text-sm"><?= $name ?></td>
                                    <td class="py-2 px-4 border-b text-sm"><?= $phone ?></td>
                                    <td class="py-2 px-4 border-b text-sm"><?= $reason ?></td>
                                    <td class="py-2 px-4 border-b text-sm"><?= $date ?></td>
                                    <td class="py-2 px-4 border-b text-sm"><?= $time ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4 flex justify-center space-x-2">
                <?php if ($page_num > 1): ?>
                    <a href="?month=<?= $cur_month ?>&year=<?= $cur_year ?>&page=<?= $page_num - 1 ?>"
                        class="px-3 py-1 bg-slate-500 text-white rounded hover:bg-slate-600">« Prev</a>
                <?php else: ?>
                    <span class="px-3 py-1 bg-slate-500 text-white rounded opacity-50 cursor-not-allowed">« Prev</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?month=<?= $cur_month ?>&year=<?= $cur_year ?>&page=<?= $i ?>"
                        class="px-3 py-1 <?= $i === $page_num ? 'bg-slate-700 text-white' : 'bg-slate-100 text-white' ?> rounded hover:bg-slate-800 hover:text-white"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page_num < $total_pages): ?>
                    <a href="?month=<?= $cur_month ?>&year=<?= $cur_year ?>&page=<?= $page_num + 1 ?>"
                        class="px-3 py-1 bg-slate-500 text-white rounded hover:bg-slate-600">Next »</a>
                <?php else: ?>
                    <span class="px-3 py-1 bg-slate-500 text-white rounded opacity-50 cursor-not-allowed">Next »</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
        <div class="bg-white w-full max-w-md mx-4 h-[90vh] rounded-xl shadow-lg flex flex-col" tabindex="-1">
            <div class="bg-[#169976] px-6 py-4 rounded-t-xl sticky top-0 z-10 flex justify-between items-center">
                <h2 id="modalTitle" class="text-xl font-semibold text-white text-center">Appointment Details</h2>
                <button type="button" onclick="closeModal('appointmentModal')" class="text-white hover:text-gray-200 focus:outline-none" aria-label="Close modal">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto flex-1 appointment-details">
                <p id="modalDate" class="text-lg font-semibold"></p>
                <p id="appointmentCount" class="text-sm text-gray-600"></p>
                <div id="appointmentDetails" class="space-y-4"></div>
                <button type="button" onclick="openBookingModal()" class="w-full px-4 py-2 bg-[#169976] text-white rounded hover:bg-[#18b98e]" id="bookNewBtn">Book New Appointment</button>
            </div>
        </div>
    </div>

    <script>
        // Placeholder for edit functionality (to be expanded)
        <?php if (isset($_GET['edit_appointment_id'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const appointmentId = <?= $_GET['edit_appointment_id'] ?>;
                showAppointmentModal('edit');
                // Fetch and populate form data here (requires AJAX or additional PHP logic)
            });
        <?php endif; ?>

        document.addEventListener("DOMContentLoaded", function() {
            var calendarEl = document.getElementById("calendar");
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: "dayGridMonth",
                initialDate: "2025-08-17", // Updated to current date
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch("./functions/get-appointments.php")
                        .then((response) => {
                            console.log("Response status:", response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text();
                        })
                        .then((text) => {
                            console.log("Raw response (first 200 chars):", text.substring(0, 200));
                            let events;
                            try {
                                events = JSON.parse(text);
                                console.log("Successfully parsed JSON:", events);
                            } catch (e) {
                                console.error("JSON parse error:", e);
                                console.error("Full response was:", text);
                                const jsonMatch = text.match(/\[.*\]/s);
                                if (jsonMatch) {
                                    try {
                                        events = JSON.parse(jsonMatch[0]);
                                        console.log("Extracted JSON from response:", events);
                                    } catch (e2) {
                                        console.error("Could not extract JSON either:", e2);
                                        events = [];
                                    }
                                } else {
                                    events = [];
                                }
                            }
                            processEvents(events, successCallback);
                        })
                        .catch((error) => {
                            console.error("Fetch error:", error);
                            processEvents([], successCallback);
                        });
                },
                dateClick: function(info) {
                    handleDateClick(info);
                },
                eventDidMount: function(info) {
                    info.el.style.display = "none"; // Hide default event display
                },
                eventsSet: function(events) {
                    console.log("Events set, updating appearance");
                    updateCalendarAppearance();
                },
                dayMaxEvents: false,
                showNonCurrentDates: false,
            });
            calendar.render();

            // Refresh events if reload parameter is present
            if (new URLSearchParams(window.location.search).get('reload') === '1') {
                calendar.refetchEvents();
                // Remove reload param from URL to prevent infinite refresh
                window.history.replaceState({}, document.title, window.location.pathname + '?month=' + <?= $cur_month ?> + '&year=' + <?= $cur_year ?>);
            }
        });

        // Include other functions from appointments-handler.js here
        function processEvents(events, successCallback) {
            console.log("Processing events:", events);
            allEvents = events;

            // Count appointments per date
            appointmentCounts = {};
            events.forEach((event) => {
                const eventDate = new Date(event.start).toISOString().split("T")[0];
                appointmentCounts[eventDate] = (appointmentCounts[eventDate] || 0) + 1;
            });

            console.log("Appointment counts:", appointmentCounts);
            successCallback(events);

            // Update calendar appearance after events are loaded
            setTimeout(() => {
                updateCalendarAppearance();
                console.log("Updated counts:", appointmentCounts);
            }, 100);
        }

        // ... (add other functions like handleDateClick, showAppointmentDetails, etc., from appointments-handler.js)
        // For brevity, I'll include just one more as an example
        function handleDateClick(info) {
            const dateStr = info.dateStr;
            const count = appointmentCounts[dateStr] || 0;

            console.log(`Date clicked: ${dateStr}, Appointment count: ${count}`);

            if (count > 0) {
                showAppointmentDetails(dateStr, count);
            } else {
                openBookingModalForDate(dateStr);
            }
        }

        function toggleModal(modalId) {
            console.log("Toggling modal:", modalId); // Debug log
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle('hidden');
            } else {
                console.error("Modal not found:", modalId);
            }
        }
    </script>

    <script src="./js/profile-dropdown.js"></script>
    <script src="./js/appointment-handler.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
</body>

</html>
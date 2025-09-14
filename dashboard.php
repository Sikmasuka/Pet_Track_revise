<?php
ob_start(); // Start output buffering
require_once __DIR__ . "/functions/auth.php";
require_once __DIR__ . "/functions/dashboard-handler.php";
require_once __DIR__ . "/functions/logs.php";
include "includes/sitemap/Help/support.php";

requireVet();

// Fetch vet data for modal
$stmt = $pdo->prepare("SELECT * FROM veterinarian WHERE vet_id = ?");
$stmt->execute([$_SESSION['vet_id']]);
$vet = $stmt->fetch(PDO::FETCH_ASSOC);

if (
    isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > $_SESSION['expire_time']
) {
    session_unset();
    session_destroy();
    header("Location: index.php?expired=1");
    exit;
}
$_SESSION['last_activity'] = time();

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="Assets/chart.js"></script>
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        @media (min-width: 768px) {
            .chart-container {
                height: 400px;
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
    </style>
</head>

<body class="bg-slate-100 min-h-screen text-gray-800">
    <?php include('./includes/edit-profile.php') ?>

    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="lg:hidden fixed top-4 left-4 z-50 bg-teal-700 text-white p-3 rounded-md shadow-lg hover:bg-teal-600 transition-colors">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-[200px] bg-gradient-to-b from-emerald-600 via-teal-700 to-emerald-800 text-white p-5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col border-r border-teal-800">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl lg:text-2xl font-semibold flex items-center gap-2">
                <img src="image/MainIconWhite.png" alt="Dashboard" class="w-6 lg:w-8">
                <span class="md:inline">Dashboard</span>
            </h2>
            <button id="closeSidebarBtn" class="lg:hidden absolute top-4 right-4 text-white hover:text-gray-200 duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-grow mt-8 lg:mt-12 space-y-0.5">
            <a href="dashboard.php" class="block text-sm text-white bg-teal-800 px-4 py-2 rounded-md hover:bg-teal-900 transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            <a href="clients.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-user mr-2"></i> Clients
            </a>
            <a href="pets.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-paw mr-2"></i> Pets
            </a>
            <a href="medical_records.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-file-medical mr-2"></i> Medical Records
            </a>
            <a href="payment_methods.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-credit-card mr-2"></i> Payments
            </a>
            <a href="appointments.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-calendar-days mr-2"></i> Appointments
            </a>
            <a href="archive.php" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors">
                <i class="fa-solid fa-box-archive mr-2"></i> Archive
            </a>
            <a href="#" class="block text-sm text-white hover:bg-teal-800 px-4 py-2 rounded-md transition-colors" onclick="toggleModal('vetHelpModal')">
                <i class="fas fa-question-circle mr-2"></i> Help/Support
            </a>
        </nav>
        <div class="pt-4">
            <a href="#" onclick="confirmLogout(event)" class="block text-md text-white hover:bg-red-600 px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </aside>

    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <div class="ml-0 lg:ml-52 p-4 pt-16 lg:pt-4">
        <!-- Header -->
        <header class="bg-white shadow-lg rounded-lg text-gray-800 py-4 mb-8 p-4 lg:p-6 border border-slate-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl lg:text-2xl font-bold">Dashboard</h1>
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
                                    <p class="text-sm font-semibold text-gray-800"><?php echo $vetName; ?></p>
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
                            <a href="#" onclick="confirmLogout(event)" class="flex items-center gap-3 px-4 py-3 text-sm text-red-500 hover:bg-gray-100 transition-colors duration-150">
                                <i class="fas fa-sign-out-alt text-red-500"></i>
                                <div>
                                    <div class="font-medium">Logout</div>
                                    <div class="text-xs text-red-600">Sign out of your account</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                <div class="bg-white p-4 rounded-md h-full relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
                    <a href="clients.php" class="absolute top-1 right-2 text-gray-500 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-user mr-2 text-xl text-indigo-500"></i> Clients</h3>
                        <p class="text-xl"><?php echo isset($clientCount) ? $clientCount : 0; ?></p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-md relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
                    <a href="pets.php" class="absolute top-1 right-2 text-gray-500 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-paw mr-2 text-xl text-teal-500"></i> Pets</h3>
                        <p class="text-xl"><?php echo isset($petCount) ? $petCount : 0; ?></p>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-md relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
                    <a href="medical_records.php" class="absolute top-1 right-2 text-gray-500 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fas fa-file-medical mr-2 text-xl text-blue-500"></i> Medical Records</h3>
                        <p class="text-xl"><?php echo isset($recordCount) ? $recordCount : 0; ?></p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-md relative shadow-lg border border-slate-200 hover:border-indigo-400 transition-colors">
                    <a href="payment_methods.php" class="absolute top-1 right-2 text-gray-500 hover:text-indigo-400 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <div class="text-center mt-4">
                        <h3 class="font-bold text-xl mb-1"><i class="fa-solid fa-money-bill-wave mr-2 text-xl text-indigo-500"></i> Total Payments</h3>
                        <p class="text-xl">₱<?php echo isset($totalPayment) ? number_format($totalPayment, 2) : '0.00'; ?></p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="bg-white p-4 lg:p-6 rounded-lg shadow-lg border border-slate-200">
            <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-800 mb-6">Analytics Overview</h2>
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="flex-1 bg-white border border-slate-200 rounded-lg p-4 shadow-lg hover:border-indigo-400 transition-colors">
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Monthly Income</h3>
                    <div class="chart-container">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>
                <div class="flex-0.5 bg-white border border-slate-200 rounded-lg p-4 shadow-lg hover:border-indigo-400 transition-colors">
                    <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Most Common Medical Conditions</h3>
                    <div class="chart-container">
                        <canvas id="conditionChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="mt-8 bg-white border border-slate-200 rounded-lg p-4 shadow-lg hover:border-indigo-400 transition-colors">
                <h3 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Recent Activities</h3>
                <div class="table-container overflow-x-scroll lg:overflow-x-hidden">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-700 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody id="activities-body" class="bg-white divide-y divide-slate-200">
                        </tbody>
                    </table>
                    <!-- Remove server-side pagination since it's handled by AJAX -->
                </div>
            </div>
        </main>
    </div>

    <script>
        // Monthly Income Bar Chart
        const monthlyLabels = <?php echo json_encode($monthlyLabels ?? []); ?>;
        const monthlyTotals = <?php echo json_encode($monthlyTotals ?? []); ?>;

        const incomeCtx = document.getElementById('incomeChart').getContext('2d');
        const incomeChart = new Chart(incomeCtx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Total Income (₱)',
                    data: monthlyTotals,
                    backgroundColor: ['#3b82f6', '#6366f1', '#2dd4bf'],
                    borderColor: ['#2563eb', '#4f46e5', '#14b8a6'],
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (₱)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Most Common Medical Conditions Pie Chart
        const conditionLabels = <?php echo json_encode($conditionLabels ?? []); ?>;
        const conditionCounts = <?php echo json_encode($conditionCounts ?? []); ?>;

        const conditionCtx = document.getElementById('conditionChart').getContext('2d');
        const conditionChart = new Chart(conditionCtx, {
            type: 'pie',
            data: {
                labels: conditionLabels,
                datasets: [{
                    data: conditionCounts,
                    backgroundColor: ['#3b82f6', '#2dd4bf', '#6366f1', '#a855f7'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // AJAX polling for Recent Activities
        let currentPage = 1;
        let isFetching = false;

        async function fetchRecentActivities(page = 1) {
            if (isFetching) return;
            isFetching = true;

            const tbody = document.getElementById("activities-body");
            if (!tbody) {
                console.error("Activities table body not found");
                isFetching = false;
                return;
            }

            // Create a new tbody element in memory (not attached to DOM)
            const newTbody = document.createElement("tbody");
            newTbody.classList.add("bg-white", "divide-y", "divide-slate-200");

            // Add a temporary loading row to the new tbody
            newTbody.innerHTML = '<tr><td colspan="4" class="px-4 py-2 text-sm text-center text-gray-500">Loading...</td></tr>';

            try {
                const response = await fetch(`./functions/get-recent-activities.php?page=${page}`);
                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                const text = await response.text();
                console.log("Raw response:", text);
                const data = text ? JSON.parse(text) : {};
                console.log("Parsed data:", data);

                // Clear the new tbody and populate with actual data
                newTbody.innerHTML = '';
                if (!data.activities || data.activities.length === 0) {
                    newTbody.innerHTML = '<tr><td colspan="4" class="px-4 py-2 text-sm text-center text-gray-500">No recent activities</td></tr>';
                } else {
                    data.activities.forEach((activity, index) => {
                        const serial = data.offset + index + 1;
                        const timestamp = new Date(activity.Timestamp);
                        const formattedDate = isNaN(timestamp.getTime()) ? 'Unknown' : timestamp.toLocaleString();
                        const row = `
                    <tr class="hover:bg-gray-50 opacity-0 transition-opacity duration-300">
                        <td class="px-4 py-2 text-sm whitespace-nowrap">${serial}</td>
                        <td class="px-4 py-2 text-sm whitespace-nowrap">${activity.name || 'Veterinarian'}</td>
                        <td class="px-4 py-2 text-sm whitespace-nowrap">${activity.Description || 'No description'}</td>
                        <td class="px-4 py-2 text-sm whitespace-nowrap">${formattedDate}</td>
                    </tr>
                `;
                        newTbody.insertAdjacentHTML("beforeend", row);
                    });
                }

                // Smoothly replace the old tbody with the new one
                const parentTable = tbody.parentElement;
                tbody.classList.add("opacity-0"); // Fade out old content
                setTimeout(() => {
                    parentTable.replaceChild(newTbody, tbody); // Swap tbody
                    newTbody.id = "activities-body"; // Restore the ID
                    newTbody.classList.remove("opacity-0"); // Fade in new content

                    // Fade in each row for a smoother effect
                    newTbody.querySelectorAll("tr").forEach((row, index) => {
                        setTimeout(() => {
                            row.classList.remove("opacity-0");
                        }, index * 50); // Stagger row animations
                    });
                }, 300); // Match the duration of the opacity transition

                // Update pagination
                const pagination = document.getElementById("pagination");
                if (pagination) {
                    pagination.innerHTML = '';
                    if (data.currentPage > 1) {
                        pagination.innerHTML += `<button onclick="fetchRecentActivities(${data.currentPage - 1})" class="px-3 py-1 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">« Prev</button>`;
                    }
                    for (let i = 1; i <= data.totalPages; i++) {
                        pagination.innerHTML += `<button onclick="fetchRecentActivities(${i})" class="px-3 py-1 ${i === data.currentPage ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-700'} rounded hover:bg-indigo-500 hover:text-white">${i}</button>`;
                    }
                    if (data.currentPage < data.totalPages) {
                        pagination.innerHTML += `<button onclick="fetchRecentActivities(${data.currentPage + 1})" class="px-3 py-1 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">Next »</button>`;
                    }
                }

                currentPage = data.currentPage;
            } catch (error) {
                console.error("Fetch error:", error.message);
                console.log("Response text on error:", await response.text());
                newTbody.innerHTML = `<tr><td colspan="4" class="px-4 py-2 text-sm text-center text-red-500">Failed to load activities: ${error.message}</td></tr>`;
                const parentTable = tbody.parentElement;
                parentTable.replaceChild(newTbody, tbody);
                newTbody.id = "activities-body";
                newTbody.classList.remove("opacity-0");
            } finally {
                isFetching = false;
            }
        }

        // Poll every 10 seconds on page 1
        setInterval(() => {
            if (currentPage === 1 && !isFetching) {
                fetchRecentActivities(1);
            }
        }, 10000);

        // Initial fetch
        document.addEventListener('DOMContentLoaded', () => {
            fetchRecentActivities(1);
        });
    </script>

    <script src="./js/dashboard.js"></script>
    <script src="./js/sidebarHandler.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./js/confirmLogout.js"></script>
    <script src="./js/edit-profile.js"></script>
</body>

</html>
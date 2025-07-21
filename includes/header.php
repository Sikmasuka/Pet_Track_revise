<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pets</title>
    <script src="Assets/Extension.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex">
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
            <a href="pets.php" class="block text-sm lg:text-lg text-white bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-paw mr-2"></i><span class="md:inline">Pets</span>
            </a>
            <a href="medical_records.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-file-medical mr-2"></i><span class="md:inline">Medical Records</span>
            </a>
            <a href="profile.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-id-badge mr-2"></i><span class="md:inline">Profile</span>
            </a>
            <a href="payment_methods.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-credit-card mr-2"></i><span class="md:inline">Payments</span>
            </a>
            <a href="archive.php" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fa-solid fa-box-archive"></i>
                <span class="md:inline">Archive</span>
            </a>
            <a href="#" onclick="confirmLogout(event)" class="block text-sm lg:text-lg text-white hover:bg-green-600 px-4 py-2 mb-2 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i><span class="md:inline">Logout</span>
            </a>
        </nav>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>
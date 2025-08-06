<!-- Landing Page -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon" />
    <title>PetTrack</title>
    <script src="Assets/chart.js"></script>
    <link rel="stylesheet" href="Assets/FontAwsome/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .hero-section {
            min-height: calc(100vh - 64px);
            position: relative;
        }

        .hero-image {
            position: absolute;
            bottom: 0;
            right: 0;
            max-width: 45%;
        }

        .service-card {
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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

        #calendarDays div {
            position: relative;
            padding: 4px;
        }

        @media (max-width: 768px) {
            .hero-image {
                position: static;
                max-width: 100%;
                margin-top: 2rem;
            }

            .hero-section {
                flex-direction: column;
                text-align: center;
                padding: 2rem 1rem;
            }

            .nav-links {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-[#1DCD9F] min-h-screen">
    <!-- header -->
    <header class="fixed top-0 left-0 w-full bg-[#169976] shadow-md h-16 z-50">
        <div class="flex items-center justify-between h-full px-6">
            <div class="flex items-center gap-4">
                <img class="w-[40px]" src="./image/MainIconWhite.png" alt="Logo">
                <p class="text-white font-bold text-xl">PetTrack</p>
            </div>

            <div class="flex items-center gap-10">
                <nav class="nav-links flex items-center gap-6">
                    <a class="text-white font-semibold relative group" href="#">
                        <span class="group-hover:opacity-80 transition-opacity duration-200">Home</span>
                        <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-white transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a class="text-white font-semibold relative group" href="#about">
                        <span class="group-hover:opacity-80 transition-opacity duration-200">About</span>
                        <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-white transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a class="text-white font-semibold relative group" href="#services">
                        <span class="group-hover:opacity-80 transition-opacity duration-200">Services</span>
                        <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-white transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a class="text-white font-semibold relative group" href="#">
                        <span class="group-hover:opacity-80 transition-opacity duration-200">Contact Us</span>
                        <span class="absolute left-0 bottom-0 w-0 h-[2px] bg-white transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </nav>

                <button onclick="openModal()" class="bg-[#1DCD9F] text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-300 transform hover:bg-[#17b38a] hover:scale-105 hover:shadow-lg">
                    Appointment
                </button>
            </div>
        </div>
    </header>


    <!-- Hero Section -->
    <main class="relative min-h-screen w-full px-6 md:px-20 py-16 hero-section flex items-center justify-between bg-cover bg-center" style="background-image: url('./image/HeroBanner.png');">

        <!-- Overlay to darken the background -->
        <div class="absolute inset-0 bg-black bg-opacity-60 z-0"></div>

        <!-- Main Content -->
        <div class="max-w-2xl space-y-6 z-10">
            <h1 class="text-4xl md:text-6xl font-bold text-white leading-tight">Welcome to PetTrack</h1>
            <p class="text-xl md:text-2xl text-white leading-relaxed opacity-90">
                Your trusted partner in managing your pet's health and wellness all in one place.
            </p>
            <button onclick="openModal()" class="inline-block bg-white text-[#169976] font-semibold py-3 px-8 rounded-lg shadow-lg hover:bg-[#18b99e] hover:text-white border hover:border-white transition-colors duration-300 ease-in-out text-lg">
                Appoint Now!
            </button>
        </div>

        <!-- Right-side Image -->
        <div class="hero-image md:block z-10">
            <img src="./image/dog-cat.png" alt="Cat and Dog" class="w-full md:max-w-2xl brightness-75" />
        </div>

    </main>


    <!-- About Section -->
    <section id="about" class="bg-white mx-4 rounded-2xl shadow-lg mt-[-20px] z-50">
        <div class="container mx-auto px-6 py-24">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="space-y-4">
                        <h2 class="text-3xl font-bold text-gray-800">About</h2>
                        <p class="text-gray-600 leading-relaxed">
                            PetTrack offers you everything you need for managing your pet's health and wellness. We organize appointments, vaccinations, diet, and track everything organized in one place.
                        </p>
                    </div>
                </div>
                <div class="bg-[#1DCD9F] rounded-2xl p-8 text-center">
                    <img src="./image/about-dog.png" alt="Dog" class="w-full max-w-sm mx-auto rounded-xl" />
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="bg-[#1DCD9F] w-full py-16">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-4">Our Services</h2>
                <p class="text-xl text-white/90 max-w-3xl mx-auto leading-relaxed">
                    At PetTrack, we understand how much you love your pet and how important it is to keep them healthy and happy. That's why we offer a comprehensive range of services designed to meet all your pet's needs and give you peace of mind.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Pet Management Card -->
                <div class="service-card bg-white rounded-xl p-6 text-center shadow-lg">
                    <div class="w-16 h-16 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-paw text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pet Management</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Keep track of your pet's basic information, breed, age, weight, and other important details in one organized place.
                    </p>
                </div>

                <!-- Medical Records Card -->
                <div class="service-card bg-white rounded-xl p-6 text-center shadow-lg">
                    <div class="w-16 h-16 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-stethoscope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Medical Records</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Store and access your pet's complete medical history, vaccinations, and treatments in one secure location.
                    </p>
                </div>

                <!-- Appointment Scheduling Card -->
                <div class="service-card bg-white rounded-xl p-6 text-center shadow-lg">
                    <div class="w-16 h-16 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Appointment Scheduling</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Easily schedule and manage vet appointments, grooming sessions, and other important pet care activities.
                    </p>
                </div>

                <!-- Client Information Management Card -->
                <div class="service-card bg-white rounded-xl p-6 text-center shadow-lg">
                    <div class="w-16 h-16 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-friends text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Client Information Management</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Manage your profile and contact information to stay connected with your pet care team.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Appointment Modal -->
    <div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
        <div class="bg-white w-full max-w-md mx-4 h-[90vh] rounded-xl shadow-lg flex flex-col" tabindex="-1">
            <!-- Modal Header -->
            <div class="bg-[#169976] px-6 py-4 rounded-t-xl sticky top-0 z-10 flex justify-between items-center">
                <h2 id="modalTitle" class="text-xl font-semibold text-white text-center">Book an Appointment</h2>
                <button type="button" onclick="closeModal()" class="text-white hover:text-gray-200 focus:outline-none" aria-label="Close modal">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="appointmentForm" method="POST" action="./functions/appointment-handler.php" class="p-6 space-y-4 overflow-y-auto flex-1">
                <!-- Name -->
                <div>
                    <label for="owner" class="block text-sm font-medium text-gray-700">Owner Name</label>
                    <input type="text" id="owner" name="owner_name" required class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-[#169976] focus:border-[#169976]" />
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" id="contact" name="contact_number" required class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-[#169976] focus:border-[#169976]" />
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <div id="calendarContainer" class="mt-2 p-2 bg-gray-100 rounded-md border border-gray-300">
                        <div class="flex justify-between mb-2">
                            <button type="button" id="prevMonth" class="px-2 bg-[#169976] text-white rounded">&lt;</button>
                            <span id="monthYear" class="text-sm font-semibold"></span>
                            <button type="button" id="nextMonth" class="px-2 bg-[#169976] text-white rounded">&gt;</button>
                        </div>
                        <div id="calendarDays" class="grid grid-cols-7 gap-1 text-center"></div>
                        <input type="hidden" id="selectedDate" name="appointment_date" required>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Please select your preferred date.</p>
                </div>

                <!-- Time -->
                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                    <input type="time" id="time" name="appointment_time" required class="mt-1 p-2 w-full rounded-md border border-gray-300 focus:ring-[#169976] focus:border-[#169976]" min="08:00" max="18:00" />
                    <p id="timeError" class="text-sm text-red-500 mt-1 hidden">Please pick a time between 8 AM and 6 PM.</p>
                    <p class="text-sm text-gray-500 mt-1">Please type or pick a time between 8 AM and 6 PM.</p>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea id="reason" name="reason" rows="3" required class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-[#169976] focus:border-[#169976]"></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#169976] text-white rounded hover:bg-[#18b98e]">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#169976] py-8">
        <div class="container mx-auto px-6 text-center">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <span class="text-[#169976] font-bold text-lg">🐾</span>
                </div>
                <p class="text-white font-bold text-xl">PetTrack</p>
            </div>
            <p class="text-white/80">Your trusted partner in pet health management</p>
        </div>
    </footer>


    <script>
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonColor: '#169976'
            });
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonColor: '#169976'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>

    <script src="./js/landing-page.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
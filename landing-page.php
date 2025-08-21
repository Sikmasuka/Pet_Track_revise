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

            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-map {
                height: 250px;
                margin-top: 1rem;
            }
        }

        .footer-map {
            border-radius: 8px;
            overflow: hidden;
            height: 200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Updated About Section Styles */
        #about {
            background: linear-gradient(to bottom, #f9fafb, #ffffff);
        }

        #about .container {
            max-width: 1200px;
        }

        #about h2 {
            font-size: 2.5rem;
            line-height: 1.2;
            background: linear-gradient(to right, #169976, #1dcd9f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        #about .story-image {
            transition: transform 0.3s ease;
        }

        #about .story-image:hover {
            transform: scale(1.05);
        }

        #about .mission-vision-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        #about .mission-vision-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        #about .why-choose-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        #about .why-choose-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        #about .stat-card {
            background: linear-gradient(135deg, #169976, #1dcd9f);
            border-radius: 12px;
            padding: 2rem;
            transition: transform 0.3s ease;
        }

        #about .stat-card:hover {
            transform: scale(1.05);
        }

        #about .team-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        #about .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            #about h2 {
                font-size: 2rem;
            }

            #about .story-image {
                max-width: 80%;
                margin: 0 auto;
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
        <div class="absolute inset-0 bg-black bg-opacity-60 z-0"></div>
        <div class="max-w-2xl space-y-6 z-10">
            <h1 class="text-4xl md:text-6xl font-bold text-white leading-tight">Welcome to PetTrack</h1>
            <p class="text-xl md:text-2xl text-white leading-relaxed opacity-90">
                Your trusted partner in managing your pet's health and wellness all in one place.
            </p>
            <button onclick="openModal()" class="inline-block bg-white text-[#169976] font-semibold py-3 px-8 rounded-lg shadow-lg hover:bg-[#18b99e] hover:text-white border hover:border-white transition-colors duration-300 ease-in-out text-lg">
                Appoint Now!
            </button>
        </div>
        <div class="hero-image md:block z-10">
            <img src="./image/dog-cat.png" alt="Cat and Dog" class="w-full md:max-w-2xl brightness-75" />
        </div>
    </main>

    <!-- About Section -->
    <section id="about" class="py-20">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">About PetTrack</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Your trusted partner in comprehensive pet health management</p>
            </div>

            <!-- Our Story -->
            <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
                <div class="space-y-4">
                    <h3 class="text-2xl font-bold text-gray-800">Our Story</h3>
                    <p class="text-gray-600 leading-relaxed">Founded in 2018, PetTrack began with a simple mission: to make pet care management as seamless and stress-free as possible for pet owners. What started as a small team of pet lovers and tech enthusiasts has grown into a comprehensive platform trusted by thousands of pet owners nationwide.</p>
                    <p class="text-gray-600 leading-relaxed">We understand that pets are family, and their health and happiness are paramount. That's why we've dedicated ourselves to creating a system that organizes all aspects of pet care in one convenient place.</p>
                </div>
                <div class="story-image">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='200' viewBox='0 0 300 200'%3E%3Crect fill='%23ffffff' width='300' height='200' rx='15'/%3E%3Cpath fill='%23169976' d='M150,50 Q200,30 250,50 T300,70 L300,180 L0,180 L0,70 Q50,30 100,50 T150,50 Z'/%3E%3Ccircle fill='%23169976' cx='80' cy='40' r='15'/%3E%3Ccircle fill='%23169976' cx='220' cy='40' r='15'/%3E%3C/svg%3E" alt="Happy pets" class="w-full rounded-xl" />
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="grid md:grid-cols-2 gap-8 mb-16">
                <div class="mission-vision-card bg-white p-8 rounded-xl text-center">
                    <div class="w-12 h-12 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bullseye text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Our Mission</h3>
                    <p class="text-gray-600 leading-relaxed">To provide pet owners with an intuitive, comprehensive platform that simplifies pet care management, enhances the well-being of pets, and strengthens the bond between pets and their families.</p>
                </div>
                <div class="mission-vision-card bg-white p-8 rounded-xl text-center">
                    <div class="w-12 h-12 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-eye text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Our Vision</h3>
                    <p class="text-gray-600 leading-relaxed">To become the world's most trusted pet care management platform, recognized for innovation, reliability, and dedication to improving the lives of pets and their owners.</p>
                </div>
            </div>

            <!-- Why Choose Us -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-800 mb-8 text-center">Why Choose PetTrack?</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="why-choose-card text-center">
                        <div class="w-14 h-14 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">All-in-One Platform</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">Manage appointments, medical records, and pet information in one secure place.</p>
                    </div>
                    <div class="why-choose-card text-center">
                        <div class="w-14 h-14 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Time-Saving</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">Automate reminders and streamline your pet care routine with our intuitive system.</p>
                    </div>
                    <div class="why-choose-card text-center">
                        <div class="w-14 h-14 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-heart text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Pet-Loved</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">Designed by pet owners for pet owners, with your furry family members' needs in mind.</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
                <div class="stat-card text-white text-center">
                    <p class="text-3xl font-bold mb-2">5,000+</p>
                    <p class="text-sm">Happy Pets</p>
                </div>
                <div class="stat-card text-white text-center">
                    <p class="text-3xl font-bold mb-2">98%</p>
                    <p class="text-sm">Satisfaction Rate</p>
                </div>
                <div class="stat-card text-white text-center">
                    <p class="text-3xl font-bold mb-2">24/7</p>
                    <p class="text-sm">Support</p>
                </div>
                <div class="stat-card text-white text-center">
                    <p class="text-3xl font-bold mb-2">50+</p>
                    <p class="text-sm">Vet Partners</p>
                </div>
            </div>

            <!-- Team -->
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-8 text-center">Our Team</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="team-card text-center">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Ccircle fill='%23169976' cx='60' cy='60' r='60'/%3E%3Ccircle fill='%23ffffff' cx='60' cy='45' r='20'/%3E%3Cpath fill='%23ffffff' d='M40,95 Q60,75 80,95 Z'/%3E%3C/svg%3E" alt="Team member" class="w-20 h-20 rounded-full mx-auto mb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Sarah Johnson</h4>
                        <p class="text-[#169976] font-medium mb-2 text-sm">Founder & CEO</p>
                        <p class="text-gray-600 text-sm leading-relaxed">Pet lover with 15+ years of veterinary experience</p>
                    </div>
                    <div class="team-card text-center">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Ccircle fill='%23169976' cx='60' cy='60' r='60'/%3E%3Ccircle fill='%23ffffff' cx='60' cy='45' r='20'/%3E%3Cpath fill='%23ffffff' d='M40,95 Q60,75 80,95 Z'/%3E%3C/svg%3E" alt="Team member" class="w-20 h-20 rounded-full mx-auto mb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Michael Chen</h4>
                        <p class="text-[#169976] font-medium mb-2 text-sm">Lead Developer</p>
                        <p class="text-gray-600 text-sm leading-relaxed">Tech enthusiast with a passion for pet wellness</p>
                    </div>
                    <div class="team-card text-center">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Ccircle fill='%23169976' cx='60' cy='60' r='60'/%3E%3Ccircle fill='%23ffffff' cx='60' cy='45' r='20'/%3E%3Cpath fill='%23ffffff' d='M40,95 Q60,75 80,95 Z'/%3E%3C/svg%3E" alt="Team member" class="w-20 h-20 rounded-full mx-auto mb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Emily Rodriguez</h4>
                        <p class="text-[#169976] font-medium mb-2 text-sm">Veterinary Advisor</p>
                        <p class="text-gray-600 text-sm leading-relaxed">Licensed veterinarian with specializations in pet nutrition</p>
                    </div>
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
                <div class="service-card bg-white rounded-xl p-6 text-center shadow-lg">
                    <div class="w-16 h-16 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-paw text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pet Management</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Keep track of your pet's basic information, breed, age, weight, and other important details in one organized place.
                    </p>
                </div>
                <div class="service-card bg-white rounded-xl p-6 text-center shadow-lg">
                    <div class="w-16 h-16 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-stethoscope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Medical Records</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Store and access your pet's complete medical history, vaccinations, and treatments in one secure location.
                    </p>
                </div>
                <div class="service-card bg-white rounded-xl p-6 text-center shadow-lg">
                    <div class="w-16 h-16 bg-[#1DCD9F] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Appointment Scheduling</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Easily schedule and manage vet appointments, grooming sessions, and other important pet care activities.
                    </p>
                </div>
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
            <div class="bg-[#169976] px-6 py-4 rounded-t-xl sticky top-0 z-10 flex justify-between items-center">
                <h2 id="modalTitle" class="text-xl font-semibold text-white text-center">Book an Appointment</h2>
                <button type="button" onclick="closeModal()" class="text-white hover:text-gray-200 focus:outline-none" aria-label="Close modal">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="appointmentForm" method="POST" action="./functions/appointment-handler.php" class="p-6 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label for="owner" class="block text-sm font-medium text-gray-700">Owner Name</label>
                    <input type="text" id="owner" name="owner_name" required class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-[#169976] focus:border-[#169976]" />
                </div>
                <div>
                    <label for="contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" id="contact" name="contact_number" required class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-[#169976] focus:border-[#169976]" />
                </div>
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
                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                    <input type="time" id="time" name="appointment_time" required class="mt-1 p-2 w-full rounded-md border border-gray-300 focus:ring-[#169976] focus:border-[#169976]" min="08:00" max="18:00" />
                    <p id="timeError" class="text-sm text-red-500 mt-1 hidden">Please pick a time between 8 AM and 6 PM.</p>
                    <p class="text-sm text-gray-500 mt-1">Please type or pick a time between 8 AM and 6 PM.</p>
                </div>
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea id="reason" name="reason" rows="3" required class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-[#169976] focus:border-[#169976]"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#169976] text-white rounded hover:bg-[#18b98e]">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#169976] py-8 text-white">
        <div class="container mx-auto px-6">
            <div class="footer-content flex flex-col md:flex-row justify-between items-center md:items-start gap-8">
                <div class="flex-1 max-w-md">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                            <span class="text-[#169976] font-bold text-lg">🐾</span>
                        </div>
                        <p class="text-white font-bold text-xl">PetTrack</p>
                    </div>
                    <p class="mb-4">Your trusted partner in pet health management</p>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-phone-alt text-sm"></i>
                            <span>(123) 456-7890</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-envelope text-sm"></i>
                            <span>info@pettrack.com</span>
                        </div>
                    </div>
                </div>
                <div class="flex-1 max-w-md">
                    <h3 class="text-lg font-semibold mb-4">Our Location</h3>
                    <div class="flex items-start gap-2 mb-4">
                        <i class="fas fa-map-marker-alt mt-1"></i>
                        <p>PQXJ+Q9J, Butuan - Cagayan de Oro - Iligan Rd, Balingasag, Misamis Oriental</p>
                    </div>
                    <div class="flex gap-4">
                        <a href="#" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </a>
                        <a href="#" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                        <a href="#" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fab fa-twitter text-lg"></i>
                        </a>
                    </div>
                </div>
                <div class="flex-1 max-w-md w-full">
                    <div class="footer-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1262.921952432691!2d124.78045648758747!3d8.74964320210998!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32ffe1fbecdd99ad%3A0x73cf6beb3b523f24!2sBalingasag%20Dog%20And%20Cat%20Clinic!5e1!3m2!1sen!2sph!4v1755610325757!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-6 text-center text-sm">
                <p>© 2023 PetTrack. All rights reserved.</p>
            </div>
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
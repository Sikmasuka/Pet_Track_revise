<?php
include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">

        <!-- Header -->
        <div class="bg-slate-700 px-5 py-4 rounded-t-lg">
            <h1 class="text-2xl font-semibold flex items-center gap-2 text-white">
                <i class="fas fa-user text-slate-200"></i> Guest (Pet Owner) — Help & Guide
            </h1>
        </div>

        <!-- Body -->
        <div class="bg-slate-100 p-6 space-y-6 rounded-b-lg shadow-lg">

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-home text-slate-500"></i> Visit the Landing Page
                </h6>
                <ul class="list-disc pl-6 space-y-1 text-slate-700">
                    <li><i class="fas fa-info-circle"></i> Read clinic information.</li>
                    <li><i class="fas fa-list-ul"></i> Check available services.</li>
                    <li><i class="fas fa-map-marker-alt"></i> See clinic location.</li>
                </ul>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-calendar-check text-slate-500"></i> Book an Appointment
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-slate-700">
                    <li><i class="fas fa-mouse-pointer"></i> Click <b>"Book Appointment"</b>.</li>
                    <li><i class="fas fa-user"></i> Enter your name and contact number.</li>
                    <li><i class="fas fa-paw"></i> Enter your pet's name and type (Dog/Cat).</li>
                    <li><i class="fas fa-stethoscope"></i> Select reason (Check-up, Vaccination, etc.).</li>
                    <li><i class="fas fa-calendar-day"></i> Choose a date and time.</li>
                    <li><i class="fas fa-check-circle"></i> Click <b>Submit</b>.</li>
                    <li><i class="fas fa-envelope"></i> Wait for a text or email confirmation.</li>
                </ol>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-phone text-slate-500"></i> Contact the Clinic
                </h6>
                <ul class="list-disc pl-6 space-y-1 text-slate-700">
                    <li><i class="fas fa-map-marker-alt"></i> Go to <b>"Contact Us"</b> page.</li>
                    <li><i class="fas fa-phone-alt"></i> View phone number and email address.</li>
                    <li><i class="fas fa-envelope"></i> Send a message if you need help.</li>
                </ul>
            </section>

        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
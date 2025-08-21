<!-- ==================== Guest Modal Help Support ==================== -->
<div id="guestHelpModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden text-slate-800">
    <div class="bg-slate-100 w-full max-w-2xl mx-4 h-[90vh] rounded-xl shadow-xl flex flex-col overflow-hidden">

        <!-- Header -->
        <div class="bg-slate-700 px-5 py-3 flex justify-between items-center">
            <h5 class="text-xl font-semibold flex items-center gap-2 text-white">
                <i class="fas fa-user text-slate-200"></i> Guest (Pet Owner) — Help & Guide
            </h5>
            <button type="button" class="hover:text-slate-300 transition" onclick="toggleModal('guestHelpModal')">
                <i class="fas fa-times text-2xl text-white"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6 overflow-y-auto flex-1">

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
                    <li><i class="fas fa-mouse-pointer"></i> Click <b>“Book Appointment”</b>.</li>
                    <li><i class="fas fa-user"></i> Enter your name and contact number.</li>
                    <li><i class="fas fa-paw"></i> Enter your pet’s name and type (Dog/Cat).</li>
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
                    <li><i class="fas fa-map-marker-alt"></i> Go to <b>“Contact Us”</b> page.</li>
                    <li><i class="fas fa-phone-alt"></i> View phone number and email address.</li>
                    <li><i class="fas fa-envelope"></i> Send a message if you need help.</li>
                </ul>
            </section>
        </div>
    </div>
</div>

<!-- ==================== Vet Modal Help Support ==================== -->
<div id="vetHelpModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden text-slate-800">
    <div class="bg-slate-100 w-full max-w-2xl mx-4 h-[90vh] rounded-xl shadow-xl flex flex-col overflow-hidden">

        <!-- Header -->
        <div class="bg-slate-700 px-5 py-3 flex justify-between items-center">
            <h5 class="text-xl font-semibold flex items-center gap-2 text-white">
                <i class="fas fa-user-md text-slate-200"></i>
                User (Clinic Staff / Vet) — Help & Guide
            </h5>
            <button type="button" class="hover:text-slate-300 transition" onclick="toggleModal('vetHelpModal')">
                <i class="fas fa-times text-2xl text-white"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6 overflow-y-auto flex-1">

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-sign-in-alt text-slate-500"></i> Login
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-slate-700">
                    <li>Click <b>“Login”</b> on the homepage.</li>
                    <li>Enter your <i class="fas fa-user"></i> username and <i class="fas fa-key"></i> password.</li>
                    <li>Click <b>Login</b>.</li>
                </ol>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-tachometer-alt text-slate-500"></i> Dashboard
                </h6>
                <ul class="list-disc pl-6 space-y-1 text-slate-700">
                    <li><i class="fas fa-calendar-day"></i> See today’s appointments.</li>
                    <li><i class="fas fa-bell"></i> See quick clinic updates.</li>
                </ul>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-paw text-slate-500"></i> Add / Update Pets
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-slate-700">
                    <li>Click <b>“Pets”</b>.</li>
                    <li>Click <b>“Add New Pet”</b> or search an existing pet.</li>
                    <li>Fill pet details (name, type, breed, age, gender).</li>
                    <li>Add notes if needed.</li>
                    <li>Click <b>Save</b>.</li>
                </ol>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-users text-slate-500"></i> Add / Update Clients
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-slate-700">
                    <li>Click <b>“Clients”</b>.</li>
                    <li>Click <b>“Add New Client”</b>.</li>
                    <li>Type owner’s name, address, and contact.</li>
                    <li>Click <b>Save</b>.</li>
                </ol>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-calendar-check text-slate-500"></i> Manage Appointments
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-slate-700">
                    <li>Click <b>“Appointments”</b>.</li>
                    <li>View pending requests.</li>
                    <li>Click <b>Approve</b>, <b>Reschedule</b>, or <b>Cancel</b>.</li>
                </ol>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-file-medical text-slate-500"></i> Update Medical Records
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-slate-700">
                    <li>Search for a pet in <b>“Pets”</b>.</li>
                    <li>Open the pet’s profile.</li>
                    <li>Add treatment, vaccination, or notes.</li>
                    <li>Click <b>Save</b>.</li>
                </ol>
            </section>

        </div>
    </div>
</div>


<!-- ==================== Admin Modal Help Support ==================== -->
<div id="adminHelpModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden text-slate-800">
    <div class="bg-slate-100 w-full max-w-2xl mx-4 h-[90vh] rounded-xl shadow-xl flex flex-col overflow-hidden">

        <!-- Header -->
        <div class="bg-slate-700 px-5 py-3 flex justify-between items-center">
            <h5 class="text-xl font-semibold flex items-center gap-2 text-white">
                <i class="fas fa-user-shield text-slate-200"></i> Admin — Help & Guide
            </h5>
            <button type="button" class="hover:text-slate-300 transition" onclick="toggleModal('adminHelpModal')">
                <i class="fas fa-times text-2xl text-white"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6 overflow-y-auto flex-1">

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-users-cog text-slate-500"></i> Add Staff Accounts
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-slate-700">
                    <li>Login as Admin.</li>
                    <li>Go to <b>“Users”</b>.</li>
                    <li>Click <b>“Add User”</b>.</li>
                    <li>Fill name, username, password, and role.</li>
                    <li>Click <b>Save</b>.</li>
                </ol>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-calendar-alt text-slate-500"></i> Manage Appointments
                </h6>
                <ul class="list-disc pl-6 space-y-1 text-slate-700">
                    <li>View all appointments.</li>
                    <li>Check if staff approved or rescheduled them.</li>
                </ul>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-cash-register text-slate-500"></i> Manage Payments
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-slate-700">
                    <li>Go to <b>“Payments”</b>.</li>
                    <li>Add or edit payment methods (Cash, GCash, etc.).</li>
                    <li>Record payments and print receipts.</li>
                </ol>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-chart-pie text-slate-500"></i> Reports & Analytics
                </h6>
                <ul class="list-disc pl-6 space-y-1 text-slate-700">
                    <li><i class="fas fa-chart-bar"></i> Monthly Income (Bar Graph).</li>
                    <li><i class="fas fa-notes-medical"></i> Common Pet Conditions (Pie Chart).</li>
                    <li><i class="fas fa-download"></i> Export / Download reports.</li>
                </ul>
            </section>

            <section class="bg-slate-50 p-4 rounded-lg shadow-sm border border-slate-200">
                <h6 class="text-lg font-semibold text-slate-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-cogs text-slate-500"></i> System Settings
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-slate-700">
                    <li>Go to <b>“Settings”</b>.</li>
                    <li>Update clinic information.</li>
                    <li><i class="fas fa-database"></i> Backup or restore system data.</li>
                    <li><i class="fas fa-envelope-open-text"></i> Set SMS / Email notifications.</li>
                </ol>
            </section>
        </div>
    </div>
</div>
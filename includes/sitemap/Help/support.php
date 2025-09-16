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
    <div class="bg-white w-full max-w-2xl mx-4 h-[90vh] rounded-xl shadow-xl flex flex-col overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-b from-emerald-600 via-teal-700 to-emerald-800 px-5 py-3 flex justify-between items-center">
            <h5 class="text-xl font-semibold flex items-center gap-2 text-white">
                <i class="fas fa-user-md text-emerald-200"></i>
                Staff / Vet — Help & Guide
            </h5>
            <button type="button" class="hover:text-emerald-200 transition" onclick="toggleModal('vetHelpModal')">
                <i class="fas fa-times text-2xl text-white"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6 overflow-y-auto flex-1">

            <!-- Login -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-sign-in-alt text-emerald-600"></i> Login
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-emerald-700">
                    <li>Click <b>Login</b> on the homepage.</li>
                    <li>Enter your <b>Username</b> and <b>Password</b>.</li>
                    <li>Click <b>Login</b> to access your account.</li>
                </ol>
            </section>

            <!-- Dashboard -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-tachometer-alt text-emerald-600"></i> Dashboard
                </h6>
                <ul class="list-disc pl-6 space-y-1 text-emerald-700">
                    <li>View today’s appointments.</li>
                    <li>See quick updates and notifications.</li>
                </ul>
            </section>

            <!-- Pets -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-paw text-emerald-600"></i> Pets
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-emerald-700">
                    <li>Go to the <b>Pets</b> section.</li>
                    <li>Click <b>Add New Pet</b> or search an existing one.</li>
                    <li>Fill in details (Name, Type, Breed, Age, Gender).</li>
                    <li>Add notes if needed.</li>
                    <li>Click <b>Save</b> to update records.</li>
                </ol>
            </section>

            <!-- Clients -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-users text-emerald-600"></i> Clients
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-emerald-700">
                    <li>Go to the <b>Clients</b> section.</li>
                    <li>Click <b>Add New Client</b>.</li>
                    <li>Enter the client’s Name, Address, and Contact details.</li>
                    <li>Click <b>Save</b> to store the record.</li>
                </ol>
            </section>

            <!-- Appointments -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-calendar-check text-emerald-600"></i> Appointments
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-emerald-700">
                    <li>Go to the <b>Appointments</b> section.</li>
                    <li>Review pending requests.</li>
                    <li>Choose <b>Approve</b>, <b>Reschedule</b>, or <b>Cancel</b>.</li>
                </ol>
            </section>

            <!-- Medical Records -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-file-medical text-emerald-600"></i> Medical Records
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-emerald-700">
                    <li>Search for a pet in the <b>Pets</b> section.</li>
                    <li>Open the pet’s profile.</li>
                    <li>Add treatments, vaccinations, or additional notes.</li>
                    <li>Click <b>Save</b> to update the record.</li>
                </ol>
            </section>

        </div>
    </div>
</div>



<!-- ==================== Admin Modal Help Support ==================== -->
<div id="adminHelpModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden text-slate-800">
    <div class="bg-white w-full max-w-2xl mx-4 h-[90vh] rounded-xl shadow-xl flex flex-col overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-b from-emerald-600 via-teal-700 to-emerald-800 px-5 py-3 flex justify-between items-center">
            <h5 class="text-xl font-semibold flex items-center gap-2 text-white">
                <i class="fas fa-user-shield text-emerald-200"></i> Admin Help & System Guide
            </h5>
            <button type="button" class="hover:text-emerald-200 transition" onclick="toggleModal('adminHelpModal')">
                <i class="fas fa-times text-2xl text-white"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6 overflow-y-auto flex-1">

            <!-- Add Staff -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-users-cog text-emerald-600"></i> Admin Accounts
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-emerald-700">
                    <li>Log in as <b>Admin</b>.</li>
                    <li>Go to the <b>Users</b> section.</li>
                    <li>Click <b>Add User</b>.</li>
                    <li>Enter staff details (Name, Username, Password, Role).</li>
                    <li>Click <b>Save</b> to create the account.</li>
                </ol>
            </section>

            <!-- Manage Appointments -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-calendar-alt text-emerald-600"></i> Appointments
                </h6>
                <ul class="list-disc pl-6 space-y-1 text-emerald-700">
                    <li>View all scheduled appointments.</li>
                    <li>Check staff approval or rescheduling updates.</li>
                </ul>
            </section>

            <!-- Manage Payments -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-cash-register text-emerald-600"></i> Payments
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-emerald-700">
                    <li>Go to the <b>Payments</b> section.</li>
                    <li>Add or edit payment methods (e.g., Cash, GCash).</li>
                    <li>Record client payments and print receipts.</li>
                </ol>
            </section>

            <!-- Reports -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-chart-pie text-emerald-600"></i> Reports & Analytics
                </h6>
                <ul class="list-disc pl-6 space-y-1 text-emerald-700">
                    <li>View <b>Monthly Income</b> (Bar Graph).</li>
                    <li>Track <b>Common Pet Conditions</b> (Pie Chart).</li>
                    <li>Export or download reports.</li>
                </ul>
            </section>

            <!-- System Settings -->
            <section class="bg-emerald-50 p-4 rounded-lg shadow-sm border border-emerald-200">
                <h6 class="text-lg font-semibold text-emerald-800 flex items-center gap-2 mb-2">
                    <i class="fas fa-cogs text-emerald-600"></i> System Settings
                </h6>
                <ol class="list-decimal pl-6 space-y-1 text-emerald-700">
                    <li>Go to the <b>Settings</b> section.</li>
                    <li>Update clinic information.</li>
                    <li>Backup or restore system data.</li>
                    <li>Configure SMS and Email notifications.</li>
                </ol>
            </section>
        </div>
    </div>
</div>
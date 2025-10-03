<?php
include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">

        <!-- Header -->
        <div class="bg-gradient-to-b from-emerald-600 via-teal-700 to-emerald-800 px-5 py-4 rounded-t-lg">
            <h1 class="text-2xl font-semibold flex items-center gap-2 text-white">
                <i class="fas fa-user-shield text-emerald-200"></i> Admin Help & System Guide
            </h1>
        </div>

        <!-- Body -->
        <div class="bg-white p-6 space-y-6 rounded-b-lg shadow-lg">

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

<?php
include 'includes/footer.php';
?>
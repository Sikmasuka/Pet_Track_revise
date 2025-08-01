<?php
// Start the session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['admin_id'])) {
    // Admin is logged in, redirect to admin dashboard
    header('Location: admin/admin-dashboard.php');
    exit;
} elseif (isset($_SESSION['vet_id'])) {
    // Veterinarian is logged in, redirect to veterinarian dashboard
    header('Location: dashboard.php');
    exit;
}

// Include authentication script for handling login form submission
require_once 'functions/authentication.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/MainIcon.png" type="image/x-icon">
    <title>Pet Track | Login</title>

    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Load SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="Assets/Extension.js"></script>
</head>

<body class="bg-gray-100">

    <!-- Main Content Section -->
    <main class="py-5 min-h-screen flex items-center justify-center bg-cover bg-center" style="background-image: url('image/ThumbnailCatDog.png');">
        <div class="w-full max-w-sm px-4 bg-white p-4 rounded-lg shadow-lg" style="max-height: 80vh; overflow-y: auto;">
            <h1 class="text-xl font-bold text-green-800 text-center mb-2 flex items-center justify-center gap-2">PET TRACK<img src="image/MainIcon.png" class="w-6"></h1>
            <h3 class="text-lg font-semibold text-center mb-4">Login</h3>
            <form action="index.php" method="POST" class="p-2">
                <!-- Username Input -->
                <div class="mb-3 relative">
                    <label for="username" class="block text-xs font-medium text-gray-700">Username</label>
                    <div class="relative">
                        <input type="text" id="username" name="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 text-sm" placeholder="Enter your username" required>
                        <p class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"><i class="fa fa-user text-sm"></i></p>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-3 relative">
                    <label for="password" class="block text-xs font-medium text-gray-700">Password</label>
                    <div class="mt-1 flex items-center relative">
                        <input type="password" id="password" name="password" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 pr-10 text-sm" placeholder="Enter your password" required>
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Message (if any) -->
                <?php if (isset($message) && $message): ?>
                    <p class="text-red-700 text-xs text-center mb-3"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <!-- Login Button -->
                <div class="mb-4">
                    <button type="submit" name="login" class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 focus:outline-none text-sm font-bold duration-200">Login</button>
                </div>
            </form>

            <!-- Additional Links -->
            <div class="text-center">
                <p><a href="#" class="text-xs text-blue-500 hover:underline hover:text-blue-800">Forgot password?</a></p>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const passwordIcon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');
                }
            });

            // Check if SweetAlert2 is loaded
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 is not loaded');
                return;
            }

            // Show SweetAlert2 for successful login
            <?php if ($login_success): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful!',
                    text: 'Redirecting to your dashboard...',
                    confirmButtonColor: '#3085d6',
                    timer: 1500,
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = '<?php echo $redirect_url; ?>';
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>
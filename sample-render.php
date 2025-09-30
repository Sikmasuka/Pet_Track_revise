<!-- index.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Food Ordering System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="relative">

    <!-- 🔴 Loading Overlay -->
    <div id="loader" class="fixed inset-0 flex flex-col items-center justify-center bg-white z-50">
        <!-- Replace with your own icon/logo -->
        <img src="./image/MainIcon.png" alt="App Icon" class="w-20 h-20 animate-pulse">
        <p class="mt-4 text-red-600 font-semibold text-lg">Loading...</p>
    </div>

    <!-- ✅ Page Content -->
    <div id="content" class="hidden">
        <header class="p-6 bg-red-600 text-white shadow-md">
            <h1 class="text-2xl font-bold">🍽️ St. Rita’s Canteen</h1>
        </header>

        <main class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Welcome to the Food Ordering System!</h2>
            <p class="text-gray-600">Your page is now loaded and ready.</p>
        </main>
    </div>

    <!-- Script to toggle loader -->
    <script>
        window.addEventListener("load", () => {
            document.getElementById("loader").style.display = "none";
            document.getElementById("content").classList.remove("hidden");
        });
    </script>
</body>

</html>
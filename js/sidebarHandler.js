document.addEventListener("DOMContentLoaded", function () {
  console.log("sidebarHandler.js loaded");

  const mobileMenuBtn = document.getElementById("mobileMenuBtn");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("overlay");
  const closeSidebarBtn = document.getElementById("closeSidebarBtn");

  console.log("mobileMenuBtn:", mobileMenuBtn);
  console.log("sidebar:", sidebar);
  console.log("overlay:", overlay);
  console.log("closeSidebarBtn:", closeSidebarBtn);

  function openSidebar() {
    sidebar.classList.remove("-translate-x-full");
    overlay.classList.remove("hidden");
    mobileMenuBtn.classList.add("hidden");
    document.body.style.overflow = "hidden";
  }

  function closeSidebar() {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
    mobileMenuBtn.classList.remove("hidden");
    document.body.style.overflow = "auto";
  }

  mobileMenuBtn.addEventListener("click", openSidebar);
  closeSidebarBtn.addEventListener("click", closeSidebar);
  overlay.addEventListener("click", closeSidebar);

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", function (event) {
    if (window.innerWidth < 1024) {
      if (
        !sidebar.contains(event.target) &&
        !mobileMenuBtn.contains(event.target)
      ) {
        closeSidebar();
      }
    }
  });

  // Handle window resize
  window.addEventListener("resize", function () {
    if (window.innerWidth >= 1024) {
      closeSidebar();
    }
  });
});

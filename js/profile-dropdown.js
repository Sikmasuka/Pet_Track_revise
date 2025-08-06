// Dropdown functionality
const button = document.getElementById("profileButton");
const menu = document.getElementById("dropdownMenu");
const chevron = document.getElementById("chevronIcon");

button.addEventListener("click", () => {
  const isOpen = menu.classList.contains("opacity-100");

  if (isOpen) {
    // Close dropdown
    menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
    menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
    chevron.style.transform = "rotate(0deg)";
  } else {
    // Open dropdown
    menu.classList.remove("opacity-0", "scale-95", "pointer-events-none");
    menu.classList.add("opacity-100", "scale-100", "pointer-events-auto");
    chevron.style.transform = "rotate(180deg)";
  }
});

// Close dropdown when clicking outside
document.addEventListener("click", (e) => {
  if (!button.contains(e.target) && !menu.contains(e.target)) {
    menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
    menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
    chevron.style.transform = "rotate(0deg)";
  }
});

// Add smooth transitions to dropdown items
const dropdownItems = document.querySelectorAll("#dropdownMenu a");
dropdownItems.forEach((item) => {
  item.addEventListener("mouseenter", () => {
    item.style.transform = "translateX(4px)";
  });
  item.addEventListener("mouseleave", () => {
    item.style.transform = "translateX(0)";
  });
});

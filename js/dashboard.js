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
const dropdownItems = document.querySelectorAll(
  "#dropdownMenu a, #dropdownMenu button"
);
dropdownItems.forEach((item) => {
  item.addEventListener("mouseenter", () => {
    item.style.transform = "translateX(4px)";
  });
  item.addEventListener("mouseleave", () => {
    item.style.transform = "translateX(0)";
  });
});

// Simple Profile Modal Functions
function openProfileModal() {
  // Close dropdown first
  menu.classList.remove("opacity-100", "scale-100", "pointer-events-auto");
  menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
  chevron.style.transform = "rotate(0deg)";

  // Show modal
  const modal = document.getElementById("profileModal");
  const modalContent = document.getElementById("profileModalContent");

  modal.classList.remove("hidden");
  document.body.style.overflow = "hidden";

  // Animate in
  setTimeout(() => {
    modalContent.classList.remove("scale-95", "opacity-0");
    modalContent.classList.add("scale-100", "opacity-100");
  }, 10);
}

function closeProfileModal() {
  const modal = document.getElementById("profileModal");
  const modalContent = document.getElementById("profileModalContent");

  // Animate out
  modalContent.classList.remove("scale-100", "opacity-100");
  modalContent.classList.add("scale-95", "opacity-0");

  setTimeout(() => {
    modal.classList.add("hidden");
    document.body.style.overflow = "";
  }, 300);
}

function toggleModalPassword() {
  const passwordInput = document.getElementById("vetPassword");
  const passwordToggle = document.getElementById("modalPasswordToggle");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    passwordToggle.classList.remove("fa-eye");
    passwordToggle.classList.add("fa-eye-slash");
  } else {
    passwordInput.type = "password";
    passwordToggle.classList.remove("fa-eye-slash");
    passwordToggle.classList.add("fa-eye");
  }
}

// Close modal when clicking outside
document.getElementById("profileModal").addEventListener("click", function (e) {
  if (e.target === this) {
    closeProfileModal();
  }
});

// Close modal with Escape key
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    closeProfileModal();
  }
});

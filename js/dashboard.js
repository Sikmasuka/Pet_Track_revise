console.log("Dashboard.js loaded");

// Wait for DOM to be ready
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM ready, initializing dashboard functionality");

  // DROPDOWN FUNCTIONALITY REMOVED - Handled by edit-profile.js
  // This prevents conflicts between multiple event listeners on the same element
  console.log("Dropdown functionality delegated to edit-profile.js");

  // Add smooth transitions to dropdown items (kept for styling)
  const dropdownItems = document.querySelectorAll(
    "#dropdownMenu a, #dropdownMenu button"
  );
  console.log("Dropdown items found:", dropdownItems.length);

  dropdownItems.forEach((item, index) => {
    item.addEventListener("mouseenter", () => {
      item.style.transform = "translateX(4px)";
      item.style.transition = "transform 0.2s ease";
    });
    item.addEventListener("mouseleave", () => {
      item.style.transform = "translateX(0)";
    });
  });

  // Simple Profile Modal Functions - Make them global
  window.openProfileModal = function () {
    console.log("Opening profile modal");

    // Show modal
    const modal = document.getElementById("profileModal");
    const modalContent = document.getElementById("profileModalContent");

    if (modal) {
      modal.classList.remove("hidden");
      document.body.style.overflow = "hidden";

      // Animate in
      setTimeout(() => {
        if (modalContent) {
          modalContent.classList.remove("scale-95", "opacity-0");
          modalContent.classList.add("scale-100", "opacity-100");
        }
      }, 10);
      console.log("Profile modal opened");
    } else {
      console.error("Profile modal not found");
    }
  };

  window.closeProfileModal = function () {
    console.log("Closing profile modal");

    const modal = document.getElementById("profileModal");
    const modalContent = document.getElementById("profileModalContent");

    if (modal && modalContent) {
      // Animate out
      modalContent.classList.remove("scale-100", "opacity-100");
      modalContent.classList.add("scale-95", "opacity-0");

      setTimeout(() => {
        modal.classList.add("hidden");
        document.body.style.overflow = "";
      }, 300);
      console.log("Profile modal closed");
    }
  };

  window.toggleModalPassword = function () {
    console.log("Toggling modal password visibility");

    const passwordInput = document.getElementById("vetPassword");
    const passwordToggle = document.getElementById("modalPasswordToggle");

    if (passwordInput && passwordToggle) {
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        passwordToggle.classList.remove("fa-eye");
        passwordToggle.classList.add("fa-eye-slash");
        console.log("Password shown");
      } else {
        passwordInput.type = "password";
        passwordToggle.classList.remove("fa-eye-slash");
        passwordToggle.classList.add("fa-eye");
        console.log("Password hidden");
      }
    } else {
      console.error("Password input or toggle not found", {
        passwordInput,
        passwordToggle,
      });
    }
  };

  // Close modal when clicking outside
  const profileModal = document.getElementById("profileModal");
  if (profileModal) {
    profileModal.addEventListener("click", function (e) {
      if (e.target === this) {
        window.closeProfileModal();
      }
    });
  }

  // Close modal with Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      const modal = document.getElementById("profileModal");
      if (modal && !modal.classList.contains("hidden")) {
        window.closeProfileModal();
      }
      // Dropdown escape handling is now in edit-profile.js
    }
  });

  console.log("Dashboard initialization complete");
});

document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("password");
  const togglePassword = document.getElementById("togglePassword");
  const loginForm = document.querySelector("form");
  const passwordError = document.getElementById("passwordError");
  const passwordIcon = togglePassword.querySelector("i");

  // Show/Hide the eye button  visibility
  function toggleButtonVisibility() {
    if (passwordInput.value.length > 0) {
      togglePassword.classList.remove("hidden");
    } else {
      togglePassword.classList.add("hidden");
    }
  }
  toggleButtonVisibility();
  passwordInput.addEventListener("input", toggleButtonVisibility);

  // Toggle password visibility
  togglePassword.addEventListener("click", function () {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      passwordIcon.classList.remove("fa-eye");
      passwordIcon.classList.add("fa-eye-slash");
    } else {
      passwordInput.type = "password";
      passwordIcon.classList.remove("fa-eye-slash");
      passwordIcon.classList.add("fa-eye");
    }
  });

  // Password strength calculator
  function calculatePasswordStrength(password) {
    let strength = 0;

    // Length check
    if (password.length > 0) strength += 10;
    if (password.length >= 8) strength += 20;
    if (password.length >= 12) strength += 20;

    // Character diversity
    if (/[A-Z]/.test(password)) strength += 15;
    if (/[0-9]/.test(password)) strength += 15;
    if (/[^A-Za-z0-9]/.test(password)) strength += 20;

    // Cap at 100
    strength = Math.min(strength, 100);

    // Determine color
    let color;
    if (strength < 30) color = "red"; // weak
    else if (strength < 70) color = "orange"; // medium
    else color = "green"; // strong

    return {
      percentage: strength,
      color: color,
    };
  }

  // Password validation function
  function validatePassword(password) {
    const hasNumber = /\d/;
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/;
    const hasUpperCase = /[A-Z]/;

    let errors = [];
    if (password.length < 8) {
      errors.push("At least 8 characters.");
    }
    if (!hasNumber.test(password)) {
      errors.push("At least one number.");
    }
    if (!hasSpecialChar.test(password)) {
      errors.push("At least one special character.");
    }
    // Optional: comment out if uppercase not required
    if (!hasUpperCase.test(password)) {
      errors.push("At least one uppercase letter.");
    }

    return errors;
  }

  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      const errors = validatePassword(passwordInput.value);

      if (errors.length > 0) {
        e.preventDefault(); //Stop form submission
        passwordError.innerHTML = errors.join("<br>");
        passwordInput.classList.add("border-red-500");
      } else {
        passwordError.innerHTML = ""; //Clears error message
        passwordInput.classlist.remove("border-red-500");
      }
    });
  }

  // ✅ Clear error when typing again (no re-validation)
  passwordInput.addEventListener("input", function () {
    if (passwordError.innerHTML !== "") {
      passwordError.innerHTML = "";
      passwordInput.classList.remove("border-red-500");
    }

    // ✅ Update strength bar
    const strengthBar = document.querySelector(".password-strength-bar");
    if (strengthBar) {
      const strength = calculatePasswordStrength(this.value);
      strengthBar.style.width = strength.percentage + "%";
      strengthBar.style.backgroundColor = strength.color;
    }
  });
});

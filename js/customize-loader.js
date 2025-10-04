function showLoader() {
  document.getElementById("loadingScreen").classList.remove("hidden");
}

// For links
document.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", function (e) {
    if (this.href && !this.href.includes("#")) {
      // Avoid anchor links
      e.preventDefault();
      showLoader();
      setTimeout(() => (window.location.href = this.href), 100); // Small delay to show loader
    }
  });
});

// For forms
document.querySelectorAll("form").forEach((form) => {
  form.addEventListener("submit", showLoader);
});

// Hide loader when page loads
window.addEventListener("load", () => {
  const loadingScreen = document.getElementById("loadingScreen");
  if (loadingScreen) {
    loadingScreen.classList.add("hidden");
  }
});

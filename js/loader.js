document.addEventListener("DOMContentLoaded", () => {
  const sidebarLinks = document.querySelectorAll("#sidebar a");
  const mainContent = document.getElementById("main-content");
  const loader = document.getElementById("content-loader");

  sidebarLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault(); // stop full reload

      const url = link.getAttribute("href");

      // Show loader
      loader.classList.remove("hidden");

      fetch(url)
        .then((res) => res.text())
        .then((html) => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, "text/html");

          // Get only the new main-content body
          const newContent = doc.querySelector("#main-content").innerHTML;

          // Replace old content
          mainContent.innerHTML = newContent;

          // Hide loader
          loader.classList.add("hidden");
        })
        .catch(() => {
          mainContent.innerHTML = `<p class="text-red-600">Failed to load content.</p>`;
          loader.classList.add("hidden");
        });
    });
  });
});

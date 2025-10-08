function confirmLogout(event) {
  event.preventDefault(); // Prevent the link from navigating right away

  Swal.fire({
    title: "Are you sure?",
    text: "You will be logged out of your session.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#e31e10",
    confirmButtonText: "Yes, logout",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      console.log("Attempting redirect to: /Pet-Track/logout.php");
      window.location.href = "/Pet-Track/logout.php"; // Absolute path
      // Alternative relative path: "./logout.php"
    }
  });
}

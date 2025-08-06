function openModal() {
  const modal = document.getElementById("appointmentModal");
  modal.classList.remove("hidden");
  modal.classList.add("flex");
  document.body.style.overflow = "hidden";
  renderCalendar();
  checkTime();
}

function closeModal() {
  const modal = document.getElementById("appointmentModal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
  document.body.style.overflow = "";
}

let currentDate = new Date();
currentDate.setDate(1);

function renderCalendar(
  month = currentDate.getMonth(),
  year = currentDate.getFullYear()
) {
  const daysContainer = document.getElementById("calendarDays");
  const monthYear = document.getElementById("monthYear");
  const selectedDateInput = document.getElementById("selectedDate");

  daysContainer.innerHTML = "";
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  const daysInMonth = lastDay.getDate();
  const startingDay = firstDay.getDay();
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  monthYear.textContent = new Date(year, month).toLocaleString("default", {
    month: "long",
    year: "numeric",
  });

  // Fetch appointment counts for the month
  const startDate = `${year}-${String(month + 1).padStart(2, "0")}-01`;
  const endDate = `${year}-${String(month + 1).padStart(
    2,
    "0"
  )}-${daysInMonth}`;
  fetch(`./functions/get-appointments.php?start=${startDate}&end=${endDate}`)
    .then((response) => response.json())
    .then((data) => {
      const appointmentCounts = {};
      data.forEach((event) => {
        const eventDate = new Date(event.start).toISOString().split("T")[0];
        appointmentCounts[eventDate] = (appointmentCounts[eventDate] || 0) + 1;
      });

      // Add days of the week headers
      const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
      daysOfWeek.forEach((day) => {
        const dayElement = document.createElement("div");
        dayElement.textContent = day;
        dayElement.className = "text-gray-500 text-xs py-1";
        daysContainer.appendChild(dayElement);
      });

      // Add empty days for alignment
      for (let i = 0; i < startingDay; i++) {
        daysContainer.appendChild(document.createElement("div"));
      }

      // Add days of the month
      for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement("div");
        const formattedDate = `${year}-${String(month + 1).padStart(
          2,
          "0"
        )}-${String(day).padStart(2, "0")}`;
        dayElement.textContent = day;
        dayElement.className =
          "p-1 text-center text-sm hover:bg-gray-200 rounded relative";

        // Add red circle and disable selection for full days
        const count = appointmentCounts[formattedDate] || 0;
        if (count >= 6) {
          const indicator = document.createElement("div");
          indicator.className = "appointment-indicator full-day";
          dayElement.appendChild(indicator);
          dayElement.classList.add("cursor-not-allowed", "text-gray-400");
          dayElement.classList.remove("hover:bg-gray-200");
        }

        // Disable past days and handle click events
        if (new Date(year, month, day) < today) {
          dayElement.className =
            "p-1 text-center text-sm text-gray-300 cursor-not-allowed relative";
        } else if (count < 6) {
          dayElement.onclick = () => {
            selectedDateInput.value = formattedDate;
            document
              .querySelectorAll("#calendarDays div")
              .forEach((d) => d.classList.remove("bg-[#169976]", "text-white"));
            dayElement.classList.add("bg-[#169976]", "text-white");
          };
        }
        daysContainer.appendChild(dayElement);
      }
    })
    .catch((error) => {
      console.error("Error fetching appointments:", error);
      // Fallback: render calendar without appointment data
      const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
      daysOfWeek.forEach((day) => {
        const dayElement = document.createElement("div");
        dayElement.textContent = day;
        dayElement.className = "text-gray-500 text-xs py-1";
        daysContainer.appendChild(dayElement);
      });

      for (let i = 0; i < startingDay; i++) {
        daysContainer.appendChild(document.createElement("div"));
      }

      for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement("div");
        const formattedDate = `${year}-${String(month + 1).padStart(
          2,
          "0"
        )}-${String(day).padStart(2, "0")}`;
        dayElement.textContent = day;
        dayElement.className =
          "p-1 text-center text-sm hover:bg-gray-200 rounded";

        if (new Date(year, month, day) < today) {
          dayElement.className =
            "p-1 text-center text-sm text-gray-300 cursor-not-allowed";
        } else {
          dayElement.onclick = () => {
            selectedDateInput.value = formattedDate;
            document
              .querySelectorAll("#calendarDays div")
              .forEach((d) => d.classList.remove("bg-[#169976]", "text-white"));
            dayElement.classList.add("bg-[#169976]", "text-white");
          };
        }
        daysContainer.appendChild(dayElement);
      }
    });
}

function checkTime() {
  const timeInput = document.getElementById("time");
  const errorMsg = document.getElementById("timeError");
  const timeValue = timeInput.value;
  if (timeValue < "08:00" || timeValue > "18:00") {
    errorMsg.classList.remove("hidden");
    return false;
  } else {
    errorMsg.classList.add("hidden");
    return true;
  }
}

document.getElementById("time").addEventListener("change", checkTime);

document
  .getElementById("appointmentForm")
  .addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission
    console.log("Form submit event triggered"); // Debug log

    // Get form input values
    const ownerName = document.getElementById("owner").value.trim();
    const contactNumber = document.getElementById("contact").value.trim();
    const selectedDate = document.getElementById("selectedDate").value;
    const appointmentTime = document.getElementById("time").value;
    const reason = document.getElementById("reason").value.trim();

    // Validate inputs
    if (!ownerName || !contactNumber || !reason) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Please fill in all required fields.",
        confirmButtonColor: "#169976",
      });
      return;
    }

    if (!selectedDate) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Please select a date.",
        confirmButtonColor: "#169976",
      });
      return;
    }

    if (!checkTime()) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Please select a time between 8 AM and 6 PM.",
        confirmButtonColor: "#169976",
      });
      return;
    }

    // Format date for display
    const formattedDate = new Date(selectedDate).toLocaleDateString("en-US", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    });

    // Format time for display
    const timeParts = appointmentTime.split(":");
    const hours = parseInt(timeParts[0]);
    const minutes = timeParts[1];
    const period = hours >= 12 ? "PM" : "AM";
    const formattedHours = hours % 12 || 12;
    const formattedTime = `${formattedHours}:${minutes} ${period}`;

    // Log form data for debugging
    console.log("Form data:", {
      ownerName,
      contactNumber,
      selectedDate,
      appointmentTime,
      reason,
    });

    // Show confirmation dialog
    console.log("Showing confirmation dialog");
    Swal.fire({
      title: "Confirm Appointment",
      html: `
            <div class="text-left">
                <p><strong>Owner Name:</strong> ${ownerName}</p>
                <p><strong>Contact Number:</strong> ${contactNumber}</p>
                <p><strong>Date:</strong> ${formattedDate}</p>
                <p><strong>Time:</strong> ${formattedTime}</p>
                <p><strong>Reason:</strong> ${reason}</p>
            </div>
            <p class="mt-4">Are you sure you want to book this appointment?</p>
        `,
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#169976",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, Book It!",
      cancelButtonText: "Cancel",
    })
      .then((result) => {
        console.log("Confirmation result:", result);
        if (result.isConfirmed) {
          console.log("User confirmed, checking availability");
          // Check if the selected date is full
          fetch(
            `./functions/get-appointments.php?start=${selectedDate}&end=${selectedDate}`
          )
            .then((response) => {
              console.log("Fetch response:", response);
              if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
              }
              return response.json();
            })
            .then((data) => {
              console.log("Appointment data:", data);
              if (data.length >= 6) {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "This day is fully booked (6/6 appointments). Please select another date.",
                  confirmButtonColor: "#169976",
                });
              } else {
                console.log("Submitting form");
                this.submit(); // Submit the form
              }
            })
            .catch((error) => {
              console.error("Fetch error:", error);
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Unable to verify appointment availability. Please try again.",
                confirmButtonColor: "#169976",
              });
            });
        } else {
          console.log("User cancelled the confirmation");
        }
      })
      .catch((error) => {
        console.error("SweetAlert2 error:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Failed to display confirmation dialog. Please try again.",
          confirmButtonColor: "#169976",
        });
      });
  });

// Initialize calendar on page load
document.addEventListener("DOMContentLoaded", () => {
  renderCalendar();
  // Set default date to today if none selected
  const today = new Date();
  const defaultDate = `${today.getFullYear()}-${String(
    today.getMonth() + 1
  ).padStart(2, "0")}-${String(today.getDate()).padStart(2, "0")}`;
  document.getElementById("selectedDate").value = defaultDate;
});

// Navigation event listeners
document.getElementById("prevMonth").addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar();
});

document.getElementById("nextMonth").addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar();
});

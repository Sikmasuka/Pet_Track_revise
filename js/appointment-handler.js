let calendar;
let appointmentCounts = {}; // Store appointment counts for each date
let allEvents = []; // Store all events

document.addEventListener("DOMContentLoaded", function () {
  var calendarEl = document.getElementById("calendar");
  calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    initialDate: "2025-08-03", // Set to today, August 3, 2025
    events: function (fetchInfo, successCallback, failureCallback) {
      // Use relative path instead of absolute localhost URL
      fetch("./functions/get-appointments.php")
        .then((response) => {
          console.log("Response status:", response.status);
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.text(); // Get as text first to see what we're receiving
        })
        .then((text) => {
          console.log(
            "Raw response (first 200 chars):",
            text.substring(0, 200)
          );

          // Try to parse as JSON
          let events;
          try {
            events = JSON.parse(text);
            console.log("Successfully parsed JSON:", events);
          } catch (e) {
            console.error("JSON parse error:", e);
            console.error("Full response was:", text);

            // If there's an HTML comment or other issue, try to extract JSON
            const jsonMatch = text.match(/\[.*\]/s);
            if (jsonMatch) {
              try {
                events = JSON.parse(jsonMatch[0]);
                console.log("Extracted JSON from response:", events);
              } catch (e2) {
                console.error("Could not extract JSON either:", e2);
                events = [];
              }
            } else {
              events = [];
            }
          }

          processEvents(events, successCallback);
        })
        .catch((error) => {
          console.error("Fetch error:", error);
          // Provide fallback empty events
          processEvents([], successCallback);
        });
    },
    dateClick: function (info) {
      handleDateClick(info);
    },
    eventDidMount: function (info) {
      // Hide the default event display as we're using custom indicators
      info.el.style.display = "none";
    },
    eventsSet: function (events) {
      // This runs after events are set/updated
      console.log("Events set, updating appearance");
      updateCalendarAppearance();
    },
    dayMaxEvents: false,
    showNonCurrentDates: false,
  });
  calendar.render();
});

function processEvents(events, successCallback) {
  console.log("Processing events:", events);
  allEvents = events;

  // Count appointments per date
  appointmentCounts = {};
  events.forEach((event) => {
    const eventDate = new Date(event.start).toISOString().split("T")[0];
    appointmentCounts[eventDate] = (appointmentCounts[eventDate] || 0) + 1;
  });

  console.log("Appointment counts:", appointmentCounts);
  successCallback(events);

  // Update calendar appearance after events are loaded
  setTimeout(() => {
    updateCalendarAppearance();
  }, 100);
}

// Alternative method to fetch appointments if the main endpoint fails
function fetchAppointmentsAlternative() {
  return new Promise((resolve, reject) => {
    // You can implement an alternative way to get appointments here
    // For now, let's try a direct database query approach

    fetch("./functions/get-appointments-alt.php")
      .then((response) => response.json())
      .then((data) => resolve(data))
      .catch((err) => {
        // If that fails too, create mock data for testing
        console.warn("Using mock data for testing");
        const mockEvents = [
          {
            title: "John Doe - Checkup",
            start: "2025-08-08T10:00:00",
            extendedProps: {
              contact: "123-456-7890",
            },
          },
        ];
        resolve(mockEvents);
      });
  });
}

function updateCalendarAppearance() {
  console.log("Updating calendar appearance");

  // Get all day cells
  const dayCells = document.querySelectorAll(
    ".fc-daygrid-day:not(.fc-day-other)"
  );

  dayCells.forEach((dayCell) => {
    // Remove existing indicators and classes
    const existingIndicator = dayCell.querySelector(".appointment-indicator");
    if (existingIndicator) {
      existingIndicator.remove();
    }
    dayCell.classList.remove("has-appointments", "full-day");

    // Get the date for this cell
    const dateAttr = dayCell.getAttribute("data-date");
    if (dateAttr) {
      const count = appointmentCounts[dateAttr] || 0;
      console.log(`Date: ${dateAttr}, Count: ${count}`);

      if (count > 0) {
        // Add appointment indicator
        const indicator = document.createElement("div");
        indicator.className = "appointment-indicator";

        if (count >= 6) {
          indicator.classList.add("full-day");
          dayCell.classList.add("full-day");
        } else {
          indicator.classList.add("has-appointments");
          dayCell.classList.add("has-appointments");
        }

        dayCell.appendChild(indicator);
      }
    }
  });
}

function handleDateClick(info) {
  const dateStr = info.dateStr;
  const count = appointmentCounts[dateStr] || 0;

  console.log(`Date clicked: ${dateStr}, Appointment count: ${count}`);

  if (count > 0) {
    // Show appointment details modal
    showAppointmentDetails(dateStr, count);
  } else {
    // Open booking modal for new appointment
    openBookingModalForDate(dateStr);
  }
}

function showAppointmentDetails(dateStr, count) {
  // Filter events for this specific date
  const dayEvents = allEvents.filter((event) => {
    const eventDate = new Date(event.start).toISOString().split("T")[0];
    return eventDate === dateStr;
  });

  console.log(`Showing details for ${dateStr}, events:`, dayEvents);

  const modalDate = document.getElementById("modalDate");
  const appointmentCount = document.getElementById("appointmentCount");
  const appointmentDetails = document.getElementById("appointmentDetails");
  const bookNewBtn = document.getElementById("bookNewBtn");

  modalDate.textContent = `Appointments on ${dateStr}`;
  appointmentCount.textContent = `${count}/6 appointments`;

  appointmentDetails.innerHTML = dayEvents
    .map((event) => {
      const ownerName = event.title.split(" - ")[0] || "N/A";
      const reason = event.title.split(" - ")[1] || "N/A";
      const time = new Date(event.start).toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      });

      return `
                    <div class="border-b pb-2 last:border-b-0">
                        <p><strong>Owner:</strong> ${ownerName}</p>
                        <p><strong>Contact:</strong> ${
                          event.extendedProps.contact || "N/A"
                        }</p>
                        <p><strong>Reason:</strong> ${reason}</p>
                        <p><strong>Time:</strong> ${time}</p>
                    </div>
                `;
    })
    .join("");

  // Disable book new button if day is full
  if (count >= 6) {
    bookNewBtn.disabled = true;
    bookNewBtn.textContent = "Day is Full (6/6)";
    bookNewBtn.classList.remove("hover:bg-[#18b98e]");
    bookNewBtn.classList.add("bg-gray-400", "cursor-not-allowed");
  } else {
    bookNewBtn.disabled = false;
    bookNewBtn.textContent = "Book New Appointment";
    bookNewBtn.classList.add("hover:bg-[#18b98e]");
    bookNewBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
  }

  document.getElementById("appointmentModal").classList.remove("hidden");
}

function openBookingModalForDate(dateStr) {
  document.getElementById("originalDate").value = dateStr;
  document.getElementById("selectedDate").value = dateStr;
  checkDateStatus(dateStr);
  document.getElementById("bookingModal").classList.remove("hidden");
}

function checkDateStatus(dateStr) {
  fetch(`./functions/get-appointments.php?start=${dateStr}&end=${dateStr}`)
    .then((response) => response.json())
    .then((data) => {
      const count = data.length;
      const statusEl = document.getElementById("dateStatus");
      const submitButton = document
        .getElementById("appointmentForm")
        .querySelector('button[type="submit"]');

      if (count >= 6) {
        statusEl.classList.remove("hidden");
        statusEl.textContent = `This day is full (${count}/6 appointments).`;
        submitButton.disabled = true;
        submitButton.classList.add("bg-gray-400", "cursor-not-allowed");
        submitButton.classList.remove("bg-[#169976]", "hover:bg-[#18b98e]");
      } else {
        statusEl.classList.add("hidden");
        submitButton.disabled = false;
        submitButton.classList.remove("bg-gray-400", "cursor-not-allowed");
        submitButton.classList.add("bg-[#169976]", "hover:bg-[#18b98e]");
      }
    })
    .catch((error) => {
      console.error("Error checking date status:", error);
      document.getElementById("dateStatus").classList.add("hidden");
      const submitButton = document
        .getElementById("appointmentForm")
        .querySelector('button[type="submit"]');
      submitButton.disabled = false;
      submitButton.classList.remove("bg-gray-400", "cursor-not-allowed");
      submitButton.classList.add("bg-[#169976]", "hover:bg-[#18b98e]");
    });
}

function validateForm() {
  const timeInput = document.getElementById("time");
  const timeError = document.getElementById("timeError");
  const time = timeInput.value;
  const [hours, minutes] = time.split(":").map(Number);

  if (hours < 8 || hours > 18 || (hours === 18 && minutes > 0)) {
    timeError.classList.remove("hidden");
    return false;
  }

  timeError.classList.add("hidden");
  return true;
}

function closeModal(modalId) {
  document.getElementById(modalId).classList.add("hidden");

  // Reset booking modal state
  if (modalId === "bookingModal") {
    document.getElementById("dateStatus").classList.add("hidden");
    const submitButton = document
      .getElementById("appointmentForm")
      .querySelector('button[type="submit"]');
    submitButton.disabled = false;
    submitButton.classList.remove("bg-gray-400", "cursor-not-allowed");
    submitButton.classList.add("bg-[#169976]", "hover:bg-[#18b98e]");
    document.getElementById("timeError").classList.add("hidden");
  }

  // Reset appointment modal state
  if (modalId === "appointmentModal") {
    const bookNewBtn = document.getElementById("bookNewBtn");
    bookNewBtn.disabled = false;
    bookNewBtn.textContent = "Book New Appointment";
    bookNewBtn.classList.add("hover:bg-[#18b98e]");
    bookNewBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
  }
}

function openBookingModal() {
  closeModal("appointmentModal");
  const modalDateText = document.getElementById("modalDate").textContent;
  const dateStr = modalDateText.replace("Appointments on ", "");

  document.getElementById("originalDate").value = dateStr;
  document.getElementById("selectedDate").value = dateStr;
  checkDateStatus(dateStr);
  document.getElementById("bookingModal").classList.remove("hidden");
}

function showAppointmentModal(action) {
  const modal = document.getElementById("appointmentModal");
  const form = document.getElementById("appointmentForm");

  if (action === "add") {
    document.getElementById("appointmentModalTitle").textContent =
      "Add New Appointment";
    form.reset();
    form.action = "../functions/appointment-handler.php";
    form.innerHTML += '<input type="hidden" name="add_record" value="1">';
  } else if (action === "edit") {
    document.getElementById("appointmentModalTitle").textContent =
      "Edit Appointment";
    // Populate form with appointment data (requires AJAX or PHP pre-population)
  }
  modal.classList.remove("hidden");
}

function hideAppointmentModal() {
  document.getElementById("appointmentModal").classList.add("hidden");
}

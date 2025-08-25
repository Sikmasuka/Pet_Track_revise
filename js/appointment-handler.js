function processEvents(events, successCallback) {
  allEvents = events;
  appointmentCounts = {};
  events.forEach((event) => {
    const eventDate = new Date(event.start).toISOString().split("T")[0];
    appointmentCounts[eventDate] = (appointmentCounts[eventDate] || 0) + 1;
  });
  console.log("Processed appointment counts:", appointmentCounts);
  successCallback(events);
}

function updateCalendarAppearance() {
  console.log("Updating calendar appearance");
  const dayCells = document.querySelectorAll(
    ".fc-daygrid-day:not(.fc-day-other)"
  );
  if (!dayCells.length) {
    console.warn("No day cells found, calendar may not be fully rendered");
    return;
  }
  dayCells.forEach((dayCell) => {
    const existingIndicator = dayCell.querySelector(".appointment-indicator");
    if (existingIndicator) existingIndicator.remove();
    dayCell.classList.remove("has-appointments", "full-day");

    const dateAttr = dayCell.getAttribute("data-date");
    if (dateAttr) {
      const count = appointmentCounts[dateAttr] || 0;
      if (count > 0) {
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
  if (count > 0) showAppointmentDetails(dateStr, count);
}

function showAppointmentDetails(dateStr, count) {
  const dayEvents = allEvents.filter(
    (event) => new Date(event.start).toISOString().split("T")[0] === dateStr
  );
  const modalDate = document.getElementById("modalDate");
  const appointmentCount = document.getElementById("appointmentCount");
  const appointmentDetails = document.getElementById("appointmentDetails");

  modalDate.textContent = `Appointments on ${dateStr}`;
  appointmentCount.textContent = `${count}/6 appointments`;
  appointmentDetails.innerHTML = dayEvents
    .map((event) => {
      const [ownerName, reason] = event.title.split(" - ");
      const time = new Date(event.start).toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        timeZone: "Asia/Manila",
      });
      return `
                    <div class="border-b pb-2 last:border-b-0">
                        <p><strong>Owner:</strong> ${ownerName || "N/A"}</p>
                        <p><strong>Contact:</strong> ${
                          event.extendedProps.contact || "N/A"
                        }</p>
                        <p><strong>Reason:</strong> ${reason || "N/A"}</p>
                        <p><strong>Time:</strong> ${time}</p>
                        <p><strong>Duration:</strong> ${
                          event.extendedProps.duration || "90"
                        } min</p>
                        <p><strong>Status:</strong> ${
                          event.extendedProps.status || "Scheduled"
                        }</p>
                    </div>
                `;
    })
    .join("");

  document.getElementById("appointmentModal").classList.remove("hidden");
}

function closeModal(modalId) {
  document.getElementById(modalId).classList.add("hidden");
}

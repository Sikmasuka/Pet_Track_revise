// Notification Handling
document.addEventListener("DOMContentLoaded", () => {
  const notificationButton = document.getElementById("notificationButton");
  const notificationDropdown = document.getElementById("notificationDropdown");

  notificationButton.addEventListener("click", (e) => {
    e.stopPropagation();
    notificationDropdown.classList.toggle("opacity-0");
    notificationDropdown.classList.toggle("scale-95");
    notificationDropdown.classList.toggle("pointer-events-none");
    if (!notificationDropdown.classList.contains("opacity-0")) {
      fetchNotifications();
    }
  });

  document.addEventListener("click", (e) => {
    if (
      !notificationButton.contains(e.target) &&
      !notificationDropdown.contains(e.target)
    ) {
      notificationDropdown.classList.add(
        "opacity-0",
        "scale-95",
        "pointer-events-none"
      );
    }
  });

  // Initial count fetch
  fetchNotificationCount();

  // Poll for notification count every 10 seconds
  setInterval(fetchNotificationCount, 10000);
});

async function fetchNotifications() {
  const notificationList = document.getElementById("notificationList");
  notificationList.innerHTML =
    '<div class="px-4 py-2 text-sm text-center text-gray-500">Loading...</div>';

  try {
    const response = await fetch(
      "../../functions/get-recent-activities.php?page=1"
    );
    if (!response.ok) throw new Error("Network response was not ok");
    const data = await response.json();
    const activities = data.activities || [];

    const filteredActivities = activities.filter((activity) => {
      const desc = (activity.Description || "").toLowerCase();
      return (
        desc.includes("appointment") ||
        desc.includes("deleted") ||
        desc.includes("created") ||
        desc.includes("added")
      );
    });

    let lastViewed = parseInt(
      localStorage.getItem("lastViewedNotificationTimestamp") || "0",
      10
    );
    const newActivities = filteredActivities.filter(
      (activity) => new Date(activity.Timestamp).getTime() > lastViewed
    );

    notificationList.innerHTML = "";
    if (filteredActivities.length === 0) {
      notificationList.innerHTML =
        '<div class="px-4 py-2 text-sm text-center text-gray-500">No notifications</div>';
    } else {
      filteredActivities.forEach((activity) => {
        const timestamp = new Date(activity.Timestamp);
        const formattedDate = isNaN(timestamp.getTime())
          ? "Unknown"
          : timestamp.toLocaleString();
        const item = `
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-info-circle text-indigo-400"></i>
                        <div>
                            <div class="font-medium">${
                              activity.name || "System"
                            } - ${
          activity.Description || "No description"
        }</div>
                            <div class="text-xs text-gray-500">${formattedDate}</div>
                        </div>
                    </a>
                `;
        notificationList.insertAdjacentHTML("beforeend", item);
      });
    }

    // Mark as read by updating the timestamp to the latest activity's timestamp
    if (filteredActivities.length > 0) {
      const latestTimestamp = new Date(
        filteredActivities[0].Timestamp
      ).getTime();
      localStorage.setItem("lastViewedNotificationTimestamp", latestTimestamp);
      updateNotificationCount(0);
    }
  } catch (error) {
    console.error("Fetch error:", error);
    notificationList.innerHTML =
      '<div class="px-4 py-2 text-sm text-center text-red-500">Failed to load notifications</div>';
  }
}

async function fetchNotificationCount() {
  try {
    const response = await fetch(
      "../../functions/get-recent-activities.php?page=1"
    );
    if (!response.ok) throw new Error("Network response was not ok");
    const data = await response.json();
    const activities = data.activities || [];

    const filtered = activities.filter((activity) => {
      const desc = (activity.Description || "").toLowerCase();
      return (
        desc.includes("appointment") ||
        desc.includes("deleted") ||
        desc.includes("created") ||
        desc.includes("added")
      );
    });

    let lastViewed = parseInt(
      localStorage.getItem("lastViewedNotificationTimestamp") || "0",
      10
    );
    const newCount = filtered.filter(
      (activity) => new Date(activity.Timestamp).getTime() > lastViewed
    ).length;

    updateNotificationCount(newCount);
  } catch (error) {
    console.error("Fetch count error:", error);
  }
}

function updateNotificationCount(count) {
  const span = document.getElementById("notificationCount");
  if (count > 0) {
    span.textContent = count;
    span.classList.remove("hidden");
  } else {
    span.classList.add("hidden");
  }
}

function markAllAsRead(e) {
  e.preventDefault();
  localStorage.setItem("lastViewedNotificationTimestamp", Date.now());
  updateNotificationCount(0);
  const notificationList = document.getElementById("notificationList");
  notificationList.innerHTML =
    '<div class="px-4 py-2 text-sm text-center text-gray-500">All notifications marked as read</div>';
}

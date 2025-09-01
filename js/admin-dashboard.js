async function fetchRecentActivities(page = 1) {
  if (isFetching) return;
  isFetching = true;

  const tbody = document.getElementById("activities-body");
  if (!tbody) {
    console.error("Activities table body not found");
    isFetching = false;
    return;
  }

  const newTbody = document.createElement("tbody");
  newTbody.classList.add("bg-white", "divide-y", "divide-slate-200");
  newTbody.innerHTML =
    '<tr><td colspan="4" class="px-4 py-2 text-sm text-center text-gray-500">Loading...</td></tr>';

  try {
    const url = `../functions/admin-get-recent-activities.php?page=${page}`;
    console.log("Attempting to fetch:", url);
    const response = await fetch(url);
    console.log("Response status:", response.status, response.statusText);
    if (!response.ok) {
      const errorText = await response.text();
      console.log("Error response text:", errorText);
      throw new Error(`HTTP error: ${response.status} ${response.statusText}`);
    }
    const text = await response.text();
    console.log("Raw response:", text);
    if (!text) throw new Error("Empty response from server");
    const data = JSON.parse(text);
    console.log("Parsed data:", data);

    newTbody.innerHTML = "";
    if (!data.activities || data.activities.length === 0) {
      newTbody.innerHTML =
        '<tr><td colspan="4" class="px-4 py-2 text-sm text-center text-gray-500">No recent activities</td></tr>';
    } else {
      data.activities.forEach((activity, index) => {
        const serial = data.offset + index + 1;
        const timestamp = new Date(activity.Timestamp);
        const formattedDate = isNaN(timestamp.getTime())
          ? "Unknown"
          : timestamp.toLocaleString();
        const row = `
                    <tr class="hover:bg-gray-50 opacity-0 transition-opacity duration-300">
                        <td class="px-4 py-2 text-sm whitespace-nowrap">${serial}</td>
                        <td class="px-4 py-2 text-sm whitespace-nowrap">${
                          activity.name || "Admin"
                        }</td>
                        <td class="px-4 py-2 text-sm whitespace-nowrap">${
                          activity.Description || "No description"
                        }</td>
                        <td class="px-4 py-2 text-sm whitespace-nowrap">${formattedDate}</td>
                    </tr>
                `;
        newTbody.insertAdjacentHTML("beforeend", row);
      });
    }

    const parentTable = tbody.parentElement;
    tbody.classList.add("opacity-0");
    setTimeout(() => {
      parentTable.replaceChild(newTbody, tbody);
      newTbody.id = "activities-body";
      newTbody.classList.remove("opacity-0");
      newTbody.querySelectorAll("tr").forEach((row, index) => {
        setTimeout(() => row.classList.remove("opacity-0"), index * 50);
      });
    }, 300);

    const pagination = document.getElementById("pagination");
    if (pagination) {
      pagination.innerHTML = "";
      if (data.currentPage > 1) {
        pagination.innerHTML += `<button onclick="fetchRecentActivities(${
          data.currentPage - 1
        })" class="px-3 py-1 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">« Prev</button>`;
      }
      for (let i = 1; i <= data.totalPages; i++) {
        pagination.innerHTML += `<button onclick="fetchRecentActivities(${i})" class="px-3 py-1 ${
          i === data.currentPage
            ? "bg-indigo-500 text-white"
            : "bg-gray-100 text-gray-700"
        } rounded hover:bg-indigo-500 hover:text-white">${i}</button>`;
      }
      if (data.currentPage < data.totalPages) {
        pagination.innerHTML += `<button onclick="fetchRecentActivities(${
          data.currentPage + 1
        })" class="px-3 py-1 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">Next »</button>`;
      }
    }

    currentPage = data.currentPage;
  } catch (error) {
    console.error("Fetch error:", error.message);
    newTbody.innerHTML = `<tr><td colspan="4" class="px-4 py-2 text-sm text-center text-red-500">Failed to load activities: ${error.message}</td></tr>`;
    const parentTable = tbody.parentElement;
    parentTable.replaceChild(newTbody, tbody);
    newTbody.id = "activities-body";
    newTbody.classList.remove("opacity-0");
  } finally {
    isFetching = false;
  }
}

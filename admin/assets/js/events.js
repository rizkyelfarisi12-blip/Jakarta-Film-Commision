let allEvents = [];

let currentStatusFilter = "";
let currentCategoryFilter = "";

/* =========================================================
   INIT
========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  loadEvents();
});

/* =========================================================
   DEBUG
========================================================= */

console.log("API_URL =", API_URL);

console.log("UPLOAD_URL =", UPLOAD_URL);

/* =========================================================
   LOAD EVENTS
========================================================= */
async function loadEvents() {
  try {
    const url = API_URL + "/events/get-admin-events.php";

    console.log("ADMIN EVENTS API:", url);

    const res = await fetch(url);

    console.log("RESPONSE STATUS:", res.status);

    if (!res.ok) {
      throw new Error("HTTP Error " + res.status);
    }

    const result = await res.json();

    console.log("ADMIN EVENTS DATA:", result);

    console.log("FIRST EVENT STATUS:", result.data?.[0]?.status);

    if (!result.success) {
      throw new Error(result.message || "Failed to load events");
    }

    allEvents = Array.isArray(result.data) ? result.data : [];

    renderStats(allEvents);

    filterEvents();
  } catch (error) {
    console.error("LOAD EVENTS ERROR:", error);

    const tbody = document.getElementById("eventTable");

    tbody.innerHTML = `

            <tr>

                <td
                    colspan="7"
                    style="text-align:center;padding:30px;"
                >

                    Failed to load events.

                </td>

            </tr>

        `;

    renderStats([]);
  }
}

/* =========================================================
   RENDER STATS

   Statistik selalu dihitung dari SELURUH data (allEvents),
   bukan dari hasil filter/search, sama seperti halaman
   Press Release.
========================================================= */

function renderStats(data) {
  const list = Array.isArray(data) ? data : [];

  const total = list.length;

  const published = list.filter(
    (event) =>
      String(event.status || "")
        .toLowerCase()
        .trim() === "published",
  ).length;

  const draft = list.filter(
    (event) =>
      String(event.status || "draft")
        .toLowerCase()
        .trim() === "draft",
  ).length;

  const totalEl = document.getElementById("statTotalEvents");

  const publishedEl = document.getElementById("statPublishedEvents");

  const draftEl = document.getElementById("statDraftEvents");

  if (totalEl) totalEl.textContent = total;

  if (publishedEl) publishedEl.textContent = published;

  if (draftEl) draftEl.textContent = draft;
}

/* =========================================================
   SORT EVENTS

   "updated" memakai updated_at kalau API menyediakannya,
   dengan fallback ke created_at lalu id (perkiraan urutan
   dibuat) kalau updated_at tidak ada di response API.
========================================================= */

function getEventSortTime(event) {
  const value = event.updated_at || event.created_at || event.start_date || "";

  const time = new Date(value).getTime();

  return Number.isNaN(time) ? 0 : time;
}

function sortEvents(list, sortValue) {
  switch (sortValue) {
    case "updated_asc":
      list.sort((a, b) => getEventSortTime(a) - getEventSortTime(b));

      break;

    case "date_desc":
      list.sort((a, b) =>
        String(b.start_date || "").localeCompare(String(a.start_date || "")),
      );

      break;

    case "date_asc":
      list.sort((a, b) =>
        String(a.start_date || "").localeCompare(String(b.start_date || "")),
      );

      break;

    case "updated_desc":
    default:
      list.sort((a, b) => getEventSortTime(b) - getEventSortTime(a));

      break;
  }

  return list;
}

/* =========================================================
   FILTER EVENTS
========================================================= */

function filterEvents() {
  const keyword = document
    .getElementById("searchEvent")
    .value.toLowerCase()
    .trim();

  const status = document.getElementById("statusFilter").value;

  const categoryEl = document.getElementById("categoryFilter");

  const category = categoryEl ? categoryEl.value : "";

  const dateFromEl = document.getElementById("dateFromFilter");

  const dateToEl = document.getElementById("dateToFilter");

  const dateFrom = dateFromEl ? dateFromEl.value : "";

  const dateTo = dateToEl ? dateToEl.value : "";

  const sortEl = document.getElementById("sortFilter");

  const sortValue = sortEl ? sortEl.value : "updated_desc";

  currentStatusFilter = status;

  currentCategoryFilter = category;

  const filtered = allEvents.filter((event) => {
    const title = String(event.title || "").toLowerCase();

    const eventCategory = String(event.category || "").toLowerCase();

    const location = String(event.location || "").toLowerCase();

    const matchesKeyword =
      title.includes(keyword) ||
      eventCategory.includes(keyword) ||
      location.includes(keyword);

    const matchesStatus =
      !status || String(event.status || "").toLowerCase() === status;

    const matchesCategory =
      !category ||
      String(event.category || "").toLowerCase() === category.toLowerCase();

    /*
            |--------------------------------------------------------------
            | DATE RANGE
            |--------------------------------------------------------------
            |
            | Berdasarkan start_date event (kolom "Date" di tabel).
            |
            */

    const eventDate = String(event.start_date || "").slice(0, 10);

    const matchesDateFrom = !dateFrom || (eventDate && eventDate >= dateFrom);

    const matchesDateTo = !dateTo || (eventDate && eventDate <= dateTo);

    return (
      matchesKeyword &&
      matchesStatus &&
      matchesCategory &&
      matchesDateFrom &&
      matchesDateTo
    );
  });

  /*
    |--------------------------------------------------------------------------
    | SORT
    |--------------------------------------------------------------------------
    */

  sortEvents(filtered, sortValue);

  renderTable(filtered);
}

/* =========================================================
   SEARCH
========================================================= */

function searchEvent() {
  filterEvents();
}

/* =========================================================
   STATUS BADGE
========================================================= */

function getStatusBadge(status) {
  const normalized = String(status || "draft").toLowerCase();

  switch (normalized) {
    case "published":
      return `
                <span class="event-status-badge published">
                    Published
                </span>
            `;

    case "archived":
      return `
                <span class="event-status-badge archived">
                    Archived
                </span>
            `;

    case "draft":

    default:
      return `
                <span class="event-status-badge draft">
                    Draft
                </span>
            `;
  }
}

/* =========================================================
   CATEGORY DISPLAY

   Untuk category "Others", tampilkan category_name
   (custom category) alih-alih tulisan "Others", sama
   seperti di halaman Press Release.
========================================================= */

function getCategoryDisplay(event) {
  const category = String(event.category || "").trim();

  const categoryName = String(event.category_name || "").trim();

  if (category.toLowerCase() === "others") {
    return categoryName || "Others";
  }

  return category || "Others";
}

/* =========================================================
   CATEGORY
========================================================= */

function getCategoryClass(category) {
  const normalized = String(category || "Others")
    .toLowerCase()
    .trim()
    .replace(/\s+/g, "-")
    .replace(/[^a-z0-9-]/g, "");

  switch (normalized) {
    case "nonton-di":
      return "category-nonton-di";

    case "nonton-bareng":
      return "category-nonton-bareng";

    case "jakarta-film-lab":
      return "category-jakarta-film-lab";

    default:
      return "category-others";
  }
}

/* =========================================================
   RENDER TABLE
========================================================= */
function renderTable(data) {
  const tbody = document.getElementById("eventTable");

  tbody.innerHTML = "";

  if (!Array.isArray(data) || data.length === 0) {
    tbody.innerHTML = `

            <tr>

                <td
                    colspan="7"
                    style="
                        text-align:center;
                        padding:40px;
                    "
                >

                    No events found.

                </td>

            </tr>

        `;

    return;
  }

  data.forEach((event) => {
    /*
        |--------------------------------------------------------------------------
        | IMAGE
        |--------------------------------------------------------------------------
        */

    let image = "";

    if (event.cover_image) {
      image = event.cover_image.replace("uploads/", "");
    }

    const imageHTML = image
      ? `
                <img
                    src="${UPLOAD_URL}/${image}"
                    class="table-thumb"
                    alt="${event.title || "Event"}"
                >
            `
      : `
                <div
                    class="table-thumb"
                    style="
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:#eee;
                    "
                >
                    —
                </div>
            `;

    /* =========================================================
        STATUS
        ========================================================= */

    const status = String(event.status || "draft")
      .toLowerCase()
      .trim();

    let statusClass = "status-draft";

    let statusLabel = "Draft";

    switch (status) {
      case "published":
        statusClass = "status-published";

        statusLabel = "Published";

        break;

      case "draft":
        statusClass = "status-draft";

        statusLabel = "Draft";

        break;

      case "archived":
        statusClass = "status-archived";

        statusLabel = "Archived";

        break;

      default:
        statusClass = "status-draft";

        statusLabel = "Draft";

        break;
    }

    /*
        |--------------------------------------------------------------------------
        | ROW
        |--------------------------------------------------------------------------
        */

    tbody.innerHTML += `

            <tr>

                <td>

                    ${imageHTML}

                </td>


                <td>

                    <strong>
                        ${event.title || "Untitled Event"}
                    </strong>

                </td>


                <td>

                    <span class="event-category-badge ${getCategoryClass(event.category)}">

                        ${getCategoryDisplay(event)}

                    </span>

                </td>


                <td>

                    ${event.start_date || "TBA"}

                </td>


                <td>

                    ${event.location || "TBA"}

                </td>


                <td>

                    <span class="status-badge ${statusClass}">

                        ${statusLabel}

                    </span>

                </td>


                <td>

                    <div class="table-action">

                        <a
                            href="form.php?id=${event.id}"
                            class="table-btn edit"
                        >

                            Edit

                        </a>


                        <button
                            class="table-btn delete"
                            onclick="deleteEvent(${event.id})"
                        >

                            Delete

                        </button>

                    </div>

                </td>

            </tr>

        `;
  });
}

/* =========================================================
   DELETE EVENT
========================================================= */

async function deleteEvent(id) {
  if (!confirm("Delete Event?")) {
    return;
  }

  try {
    const response = await fetch(API_URL + "/events/delete-event.php", {
      method: "POST",

      headers: {
        "Content-Type": "application/json",
      },

      body: JSON.stringify({
        id: id,
      }),
    });

    const result = await response.json();

    if (!result.success) {
      alert(result.message || "Failed to delete event.");

      return;
    }

    await loadEvents();
  } catch (error) {
    console.error("DELETE EVENT ERROR:", error);

    alert("Failed to delete event.");
  }
}

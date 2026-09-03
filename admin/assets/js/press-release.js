/* =========================================================
   JAKARTA FILM COMMISSION
   PRESS RELEASE MANAGEMENT
   FINAL VERSION
========================================================= */

/* =========================================================
   GLOBAL DATA
========================================================= */

let pressReleaseData = [];

/* =========================================================
   CONFIGURATION
========================================================= */

const PRESS_RELEASE_API = "/jfc/api/press-release";

const PRESS_RELEASE_UPLOAD_PATH = "/jfc/uploads/press-release";

/* =========================================================
   INIT
========================================================= */

document.addEventListener("DOMContentLoaded", function () {
  console.log("PRESS RELEASE MANAGEMENT INITIALIZING...");

  loadPressReleases();

  setupSearch();

  setupFilters();

  console.log("PRESS RELEASE MANAGEMENT READY.");
});

/* =========================================================
   LOAD PRESS RELEASES
========================================================= */

async function loadPressReleases() {
  const table = document.getElementById("pressReleaseTable");

  if (!table) {
    return;
  }

  table.innerHTML = `

        <tr>

            <td
                colspan="6"
                style="text-align:center;"
            >

                Loading Press Releases...

            </td>

        </tr>

    `;

  try {
    const response = await fetch(PRESS_RELEASE_API + "/get-press.php", {
      method: "GET",
      cache: "no-store",
    });

    const result = await parseJsonResponse(response);

    console.log("PRESS RELEASE API:", result);

    if (!result.success) {
      throw new Error(result.message || "Failed to load Press Releases.");
    }

    /*
        |---------------------------------------------------------
        | SAVE DATA
        |---------------------------------------------------------
        */

    pressReleaseData = Array.isArray(result.data?.items)
      ? result.data.items
      : [];

    /*
        |---------------------------------------------------------
        | UPDATE STATISTICS
        |---------------------------------------------------------
        */

    updateStatistics(result.data || {});

    /*
        |---------------------------------------------------------
        | CATEGORY FILTER
        |---------------------------------------------------------
        */

    populateCategoryFilter(pressReleaseData);

    /*
        |---------------------------------------------------------
        | RENDER TABLE
        |---------------------------------------------------------
        */

    applyFilters();
  } catch (error) {
    console.error("LOAD PRESS RELEASE ERROR:", error);

    table.innerHTML = `

            <tr>

                <td
                    colspan="6"
                    style="text-align:center;"
                >

                    Failed to load Press Releases.

                    <br>

                    <small>
                        ${escapeHtml(error.message || "Unknown error.")}
                    </small>

                </td>

            </tr>

        `;
  }
}

/* =========================================================
   PARSE JSON RESPONSE
========================================================= */

async function parseJsonResponse(response) {
  const text = await response.text();

  let result;

  try {
    result = JSON.parse(text);
  } catch (error) {
    console.error("INVALID JSON RESPONSE:", text);

    throw new Error("Server returned an invalid response.");
  }

  if (!response.ok) {
    throw new Error(
      result.message || `Server returned HTTP ${response.status}`,
    );
  }

  return result;
}

/* =========================================================
   STATISTICS
========================================================= */

function updateStatistics(data) {
  const total = document.getElementById("totalPressRelease");

  const published = document.getElementById("publishedPressRelease");

  const draft = document.getElementById("draftPressRelease");

  if (total) {
    total.textContent = Number(data.total || 0);
  }

  if (published) {
    published.textContent = Number(data.published || 0);
  }

  if (draft) {
    draft.textContent = Number(data.draft || 0);
  }
}

/* =========================================================
   CATEGORY FILTER
========================================================= */

function populateCategoryFilter(data) {
  const select = document.getElementById("categoryFilter");

  if (!select) {
    return;
  }

  /*
    |---------------------------------------------------------
    | RESET
    |---------------------------------------------------------
    */

  select.innerHTML = `

        <option value="">
            All Category
        </option>

    `;

  /*
    |---------------------------------------------------------
    | CATEGORY SET
    |---------------------------------------------------------
    */

  const categories = new Set();

  data.forEach((item) => {
    /*
            |-------------------------------------------------
            | IMPORTANT
            |-------------------------------------------------
            |
            | Filter menggunakan category_filter.
            |
            | Jika:
            |
            | category = Others
            | category_name = Film Festival
            |
            | Maka:
            |
            | category_display = Film Festival
            | category_filter  = Others
            |
            */

    const category = String(
      item.category_filter || item.category || "Others",
    ).trim();

    if (category) {
      categories.add(category);
    }
  });

  /*
    |---------------------------------------------------------
    | SORT
    |---------------------------------------------------------
    */

  const sortedCategories = Array.from(categories).sort((a, b) =>
    a.localeCompare(b, undefined, {
      sensitivity: "base",
    }),
  );

  /*
    |---------------------------------------------------------
    | ADD OPTIONS
    |---------------------------------------------------------
    */

  sortedCategories.forEach((category) => {
    const option = document.createElement("option");

    option.value = category;

    option.textContent = category;

    select.appendChild(option);
  });
}

/* =========================================================
   RENDER TABLE
========================================================= */

function renderPressReleases(data) {
  const table = document.getElementById("pressReleaseTable");

  if (!table) {
    return;
  }

  /*
    |---------------------------------------------------------
    | EMPTY
    |---------------------------------------------------------
    */

  if (!Array.isArray(data) || data.length === 0) {
    table.innerHTML = `

            <tr>

                <td
                    colspan="6"
                    style="text-align:center;"
                >

                    No Press Releases found.

                </td>

            </tr>

        `;

    return;
  }

  /*
    |---------------------------------------------------------
    | RENDER
    |---------------------------------------------------------
    */

  table.innerHTML = data.map((item) => createPressReleaseRow(item)).join("");
}

/* =========================================================
   CREATE TABLE ROW
========================================================= */

function createPressReleaseRow(item) {
  /*
    |---------------------------------------------------------
    | ID
    |---------------------------------------------------------
    |
    | ID harus berasal dari database.
    |
    */

  const id = Number(item.id);

  /*
    |---------------------------------------------------------
    | TITLE
    |---------------------------------------------------------
    */

  const title = escapeHtml(item.title || "-");

  /*
    |---------------------------------------------------------
    | CATEGORY DISPLAY
    |---------------------------------------------------------
    */

  const categoryDisplay = getCategoryDisplay(item);

  const category = escapeHtml(categoryDisplay);

  /*
    |---------------------------------------------------------
    | CATEGORY COLOR CLASS
    |---------------------------------------------------------
    */

  const categorySlug = getPressReleaseCategoryClass(
    item.category_filter || item.category,
  );

  /*
    |---------------------------------------------------------
    | STATUS
    |---------------------------------------------------------
    */

  const status = String(item.status || "draft").toLowerCase();

  const statusLabel = status === "published" ? "Published" : "Draft";

  /*
    |---------------------------------------------------------
    | IMAGE
    |---------------------------------------------------------
    */

  const imageHtml = createCoverImageHtml(item, title);

  /*
    |---------------------------------------------------------
    | DATE
    |---------------------------------------------------------
    */

  const dateValue = item.published_date || item.created_at || "";

  const date = formatDate(dateValue);

  /*
    |---------------------------------------------------------
    | STATUS CLASS
    |---------------------------------------------------------
    */

  const statusClass =
    status === "published" ? "status-published" : "status-draft";

  /*
    |---------------------------------------------------------
    | VALID ID
    |---------------------------------------------------------
    */

  const hasValidId = Number.isInteger(id) && id > 0;

  /*
    |---------------------------------------------------------
    | ACTIONS
    |---------------------------------------------------------
    */

  let actionHtml = "";

  if (hasValidId) {
    actionHtml = `

            <div class="action-group">

                <a
                    href="form.php?id=${id}"
                    class="table-btn edit"
                >

                    Edit

                </a>


                <button
                    type="button"
                    class="table-btn delete"
                    onclick="deletePressRelease(${id})"
                >

                    Delete

                </button>

            </div>

        `;
  } else {
    /*
        |-----------------------------------------------------
        | INVALID DATABASE ID
        |-----------------------------------------------------
        */

    actionHtml = `

            <span
                style="
                    color:#999;
                    font-size:13px;
                "
            >

                Invalid ID

            </span>

        `;

    console.error("INVALID PRESS RELEASE ID:", item);
  }

  /*
    |---------------------------------------------------------
    | RETURN ROW
    |---------------------------------------------------------
    */

  return `

        <tr
            data-id="${hasValidId ? id : ""}"
            data-status="${escapeAttribute(status)}"
            data-category="${escapeAttribute(
              item.category_filter || item.category || "Others",
            )}"
        >


            <!-- IMAGE -->

            <td>

                ${imageHtml}

            </td>


            <!-- TITLE -->

            <td>

                <span class="event-table-title">

                    ${title}

                </span>

            </td>


            <!-- CATEGORY -->

            <td>

                <span class="press-badge ${categorySlug}">

                    ${category}

                </span>

            </td>


            <!-- DATE -->

            <td>

                ${escapeHtml(date)}

            </td>


            <!-- STATUS -->

            <td>

                <span
                    class="status-badge ${statusClass}"
                >

                    ${statusLabel}

                </span>

            </td>


            <!-- ACTION -->

            <td>

                ${actionHtml}

            </td>


        </tr>

    `;
}

/* =========================================================
   CATEGORY DISPLAY
========================================================= */

function getCategoryDisplay(item) {
  const category = String(item.category || "").trim();

  const categoryName = String(item.category_name || "").trim();

  /*
    |---------------------------------------------------------
    | OTHERS
    |---------------------------------------------------------
    */

  if (category.toLowerCase() === "others") {
    return categoryName || "Others";
  }

  /*
    |---------------------------------------------------------
    | NORMAL CATEGORY
    |---------------------------------------------------------
    */

  return category || "Others";
}

/* =========================================================
   CATEGORY COLOR CLASS

   Dipetakan manual (bukan slugify otomatis) supaya cocok
   dengan class warna yang sama dipakai di tampilan user:

   Industry News    -> .industry
   Official Release -> .official-release
   Program Update   -> .program
   Others           -> .others
========================================================= */

function getPressReleaseCategoryClass(category) {
  const normalized = String(category || "").toLowerCase().trim();

  switch (normalized) {
    case "industry news":
      return "industry";

    case "official release":
      return "official-release";

    case "program update":
      return "program";

    default:
      return "others";
  }
}

/* =========================================================
   CREATE COVER IMAGE HTML
========================================================= */

function createCoverImageHtml(item, title) {
  const coverImage = String(item.cover_image || "").trim();

  /*
    |---------------------------------------------------------
    | NO IMAGE
    |---------------------------------------------------------
    */

  if (!coverImage) {
    return `

            <div class="table-thumb-placeholder">

                No Image

            </div>

        `;
  }

  /*
    |---------------------------------------------------------
    | NORMALIZE PATH
    |---------------------------------------------------------
    */

  const imageUrl = normalizeImageUrl(coverImage);

  return `

        <img
            src="${escapeAttribute(imageUrl)}"
            alt="${escapeAttribute(title)}"
            class="table-thumb"
            loading="lazy"
            onerror="
                this.style.display='none';
                if (this.nextElementSibling) {
                    this.nextElementSibling.style.display='flex';
                }
            "
        >

        <div
            class="table-thumb-placeholder"
            style="display:none;"
        >

            No Image

        </div>

    `;
}

/* =========================================================
   NORMALIZE IMAGE URL
========================================================= */

function normalizeImageUrl(path) {
  let value = String(path || "").trim();

  if (!value) {
    return "";
  }

  /*
    |---------------------------------------------------------
    | BACKSLASH
    |---------------------------------------------------------
    */

  value = value.replace(/\\/g, "/");

  /*
    |---------------------------------------------------------
    | ABSOLUTE URL
    |---------------------------------------------------------
    */

  if (/^https?:\/\//i.test(value)) {
    return value;
  }

  /*
    |---------------------------------------------------------
    | ROOT PATH
    |---------------------------------------------------------
    */

  if (value.startsWith("/jfc/")) {
    return value;
  }

  /*
    |---------------------------------------------------------
    | LEADING SLASH
    |---------------------------------------------------------
    */

  value = value.replace(/^\/+/, "");

  /*
    |---------------------------------------------------------
    | uploads/press-release
    |---------------------------------------------------------
    */

  if (value.startsWith("uploads/press-release/")) {
    return "/jfc/" + value;
  }

  /*
    |---------------------------------------------------------
    | assets/uploads/press-release
    |---------------------------------------------------------
    |
    | Legacy compatibility.
    |
    */

  if (value.startsWith("assets/uploads/press-release/")) {
    return "/jfc/" + value;
  }

  /*
    |---------------------------------------------------------
    | FALLBACK
    |---------------------------------------------------------
    */

  return "/jfc/" + value;
}

/* =========================================================
   SEARCH
========================================================= */

function setupSearch() {
  const search = document.getElementById("searchPressRelease");

  if (!search) {
    return;
  }

  search.addEventListener("input", applyFilters);
}

/* =========================================================
   SORT PRESS RELEASES
========================================================= */

function getPressReleaseSortTime(item) {
  const value = item.updated_at || item.created_at || item.published_date || "";

  const time = new Date(value).getTime();

  return Number.isNaN(time) ? 0 : time;
}

function sortPressReleases(list, sortValue) {
  switch (sortValue) {
    case "updated_asc":
      list.sort(
        (a, b) => getPressReleaseSortTime(a) - getPressReleaseSortTime(b),
      );
      break;

    case "date_desc":
      list.sort((a, b) =>
        String(b.published_date || "").localeCompare(
          String(a.published_date || ""),
        ),
      );
      break;

    case "date_asc":
      list.sort((a, b) =>
        String(a.published_date || "").localeCompare(
          String(b.published_date || ""),
        ),
      );
      break;

    case "updated_desc":
    default:
      list.sort(
        (a, b) => getPressReleaseSortTime(b) - getPressReleaseSortTime(a),
      );
      break;
  }

  return list;
}

/* =========================================================
   SETUP FILTERS
========================================================= */

function setupFilters() {
  const statusFilter = document.getElementById("statusFilter");

  const categoryFilter = document.getElementById("categoryFilter");

  const dateFromFilter = document.getElementById("dateFromFilter");

  const dateToFilter = document.getElementById("dateToFilter");

  const sortFilter = document.getElementById("sortFilter");

  if (statusFilter) {
    statusFilter.addEventListener("change", applyFilters);
  }

  if (categoryFilter) {
    categoryFilter.addEventListener("change", applyFilters);
  }

  if (dateFromFilter) {
    dateFromFilter.addEventListener("change", applyFilters);
  }

  if (dateToFilter) {
    dateToFilter.addEventListener("change", applyFilters);
  }

  if (sortFilter) {
    sortFilter.addEventListener("change", applyFilters);
  }
}

/* =========================================================
   APPLY FILTERS
========================================================= */

function applyFilters() {
  const searchInput = document.getElementById("searchPressRelease");

  const statusFilter = document.getElementById("statusFilter");

  const categoryFilter = document.getElementById("categoryFilter");

  /*
    |---------------------------------------------------------
    | SEARCH KEYWORD
    |---------------------------------------------------------
    */

  const keyword = searchInput ? searchInput.value.trim().toLowerCase() : "";

  /*
    |---------------------------------------------------------
    | STATUS
    |---------------------------------------------------------
    */

  const status = statusFilter ? statusFilter.value : "";

  /*
    |---------------------------------------------------------
    | CATEGORY
    |---------------------------------------------------------
    */

  const category = categoryFilter ? categoryFilter.value : "";

  /*
    |---------------------------------------------------------
    | DATE RANGE
    |---------------------------------------------------------
    */

  const dateFromEl = document.getElementById("dateFromFilter");

  const dateToEl = document.getElementById("dateToFilter");

  const dateFrom = dateFromEl ? dateFromEl.value : "";

  const dateTo = dateToEl ? dateToEl.value : "";

  /*
    |---------------------------------------------------------
    | SORT
    |---------------------------------------------------------
    */

  const sortEl = document.getElementById("sortFilter");

  const sortValue = sortEl ? sortEl.value : "updated_desc";

  /*
    |---------------------------------------------------------
    | FILTER DATA
    |---------------------------------------------------------
    */

  const filtered = pressReleaseData.filter((item) => {
    /*
                |---------------------------------------------
                | SEARCHABLE TEXT
                |---------------------------------------------
                */

    const searchableText = [
      item.title,

      item.slug,

      item.category,

      item.category_name,

      item.category_display,

      item.description,

      item.published_date,

      item.status,
    ]
      .filter(
        (value) =>
          value !== null && value !== undefined && String(value).trim() !== "",
      )
      .join(" ")
      .toLowerCase();

    /*
                |---------------------------------------------
                | SEARCH MATCH
                |---------------------------------------------
                */

    const matchesSearch = !keyword || searchableText.includes(keyword);

    /*
                |---------------------------------------------
                | STATUS MATCH
                |---------------------------------------------
                */

    const matchesStatus =
      !status ||
      String(item.status || "").toLowerCase() === status.toLowerCase();

    /*
                |---------------------------------------------
                | CATEGORY MATCH
                |---------------------------------------------
                |
                | IMPORTANT:
                |
                | Untuk Others:
                |
                | category       = Others
                | category_name  = Film Festival
                | category_filter = Others
                |
                | Jadi dropdown "Others" tetap bisa mencari
                | seluruh custom category.
                |
                |---------------------------------------------
                */

    const itemCategory = String(
      item.category_filter || item.category || "Others",
    ).trim();

    const matchesCategory = !category || itemCategory === category;

    /*
                |---------------------------------------------
                | DATE RANGE MATCH
                |---------------------------------------------
                |
                | Berdasarkan published_date.
                |
                |---------------------------------------------
                */

    const itemDate = String(item.published_date || "").slice(0, 10);

    const matchesDateFrom = !dateFrom || (itemDate && itemDate >= dateFrom);

    const matchesDateTo = !dateTo || (itemDate && itemDate <= dateTo);

    return (
      matchesSearch &&
      matchesStatus &&
      matchesCategory &&
      matchesDateFrom &&
      matchesDateTo
    );
  });

  /*
    |---------------------------------------------------------
    | SORT
    |---------------------------------------------------------
    */

  sortPressReleases(filtered, sortValue);

  /*
    |---------------------------------------------------------
    | RENDER
    |---------------------------------------------------------
    */

  renderPressReleases(filtered);
}

/* =========================================================
   DELETE PRESS RELEASE
========================================================= */

async function deletePressRelease(id) {
  /*
    |---------------------------------------------------------
    | NORMALIZE ID
    |---------------------------------------------------------
    */

  const pressReleaseId = Number(id);

  /*
    |---------------------------------------------------------
    | VALIDATE ID
    |---------------------------------------------------------
    */

  if (!Number.isInteger(pressReleaseId) || pressReleaseId <= 0) {
    console.error("INVALID DELETE ID:", id);

    alert("Invalid Press Release ID.");

    return;
  }

  /*
    |---------------------------------------------------------
    | FIND DATA
    |---------------------------------------------------------
    */

  const item = pressReleaseData.find(
    (pressRelease) => Number(pressRelease.id) === pressReleaseId,
  );

  const title = item?.title || "this Press Release";

  /*
    |---------------------------------------------------------
    | CONFIRM
    |---------------------------------------------------------
    */

  const confirmed = window.confirm(
    `Are you sure you want to delete "${title}"?`,
  );

  if (!confirmed) {
    return;
  }

  try {
    /*
        |-------------------------------------------------------
        | REQUEST
        |-------------------------------------------------------
        */

    const response = await fetch(PRESS_RELEASE_API + "/delete-press.php", {
      method: "POST",

      headers: {
        "Content-Type": "application/json",

        Accept: "application/json",
      },

      body: JSON.stringify({
        id: pressReleaseId,
      }),
    });

    /*
        |-------------------------------------------------------
        | RESPONSE
        |-------------------------------------------------------
        */

    const result = await parseJsonResponse(response);

    console.log("DELETE PRESS RELEASE:", result);

    /*
        |-------------------------------------------------------
        | FAILED
        |-------------------------------------------------------
        */

    if (!result.success) {
      throw new Error(result.message || "Failed to delete Press Release.");
    }

    /*
        |-------------------------------------------------------
        | SUCCESS
        |-------------------------------------------------------
        */

    alert("Press Release successfully deleted.");

    /*
        |-------------------------------------------------------
        | RELOAD
        |-------------------------------------------------------
        */

    await loadPressReleases();

    /*
        |-------------------------------------------------------
        | RE-APPLY FILTER
        |-------------------------------------------------------
        */

    applyFilters();
  } catch (error) {
    console.error("DELETE PRESS RELEASE ERROR:", error);

    alert(error.message || "An error occurred while deleting Press Release.");
  }
}

/* =========================================================
   DATE FORMAT
========================================================= */

function formatDate(value) {
  if (!value) {
    return "-";
  }

  const stringValue = String(value).trim();

  /*
    |---------------------------------------------------------
    | MYSQL DATE: YYYY-MM-DD
    |---------------------------------------------------------
    |
    | Hindari timezone browser.
    |
    */

  const dateOnlyMatch = stringValue.match(/^(\d{4})-(\d{2})-(\d{2})$/);

  if (dateOnlyMatch) {
    const year = dateOnlyMatch[1];

    const month = dateOnlyMatch[2];

    const day = dateOnlyMatch[3];

    const date = new Date(Number(year), Number(month) - 1, Number(day));

    if (!Number.isNaN(date.getTime())) {
      return date.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
      });
    }
  }

  /*
    |---------------------------------------------------------
    | DATETIME
    |---------------------------------------------------------
    */

  const date = new Date(stringValue);

  if (!Number.isNaN(date.getTime())) {
    return date.toLocaleDateString("en-GB", {
      day: "2-digit",
      month: "short",
      year: "numeric",
    });
  }

  /*
    |---------------------------------------------------------
    | FALLBACK
    |---------------------------------------------------------
    */

  return escapeHtml(stringValue);
}

/* =========================================================
   HTML ESCAPE
========================================================= */

function escapeHtml(value = "") {
  return String(value)
    .replace(/&/g, "&amp;")

    .replace(/</g, "&lt;")

    .replace(/>/g, "&gt;")

    .replace(/"/g, "&quot;")

    .replace(/'/g, "&#039;");
}

/* =========================================================
   ATTRIBUTE ESCAPE
========================================================= */

function escapeAttribute(value = "") {
  return escapeHtml(value);
}
let allIndividuals = [];

/* =========================================================
   INIT
========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  loadIndividuals();

  document.getElementById("searchIndividual").addEventListener("input", filterIndividuals);
  document.getElementById("statusFilter").addEventListener("change", filterIndividuals);
  document.getElementById("interestFilter").addEventListener("change", filterIndividuals);
  document.getElementById("dateFromFilter").addEventListener("change", filterIndividuals);
  document.getElementById("dateToFilter").addEventListener("change", filterIndividuals);
  document.getElementById("sortFilter").addEventListener("change", filterIndividuals);

  document.getElementById("exportExcelBtn").addEventListener("click", exportToExcel);
  document.getElementById("exportPdfBtn").addEventListener("click", exportToPdf);

  document.getElementById("individualModalClose").addEventListener("click", closeIndividualModal);

  document.getElementById("individualModalOverlay").addEventListener("click", (e) => {
    if (e.target.id === "individualModalOverlay") closeIndividualModal();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeIndividualModal();
  });
});

/* =========================================================
   LOAD DATA
========================================================= */

async function loadIndividuals() {
  const tbody = document.getElementById("individualTable");

  try {
    const res = await fetch(API_URL + "/individual/get-individuals.php");

    if (!res.ok) {
      throw new Error("HTTP Error " + res.status);
    }

    const result = await res.json();

    if (!result.success) {
      throw new Error(result.message || "Failed to load data");
    }

    allIndividuals = Array.isArray(result.data?.items) ? result.data.items : [];

    renderStats(result.data || {});
    populateInterestFilter(allIndividuals);
    filterIndividuals();
  } catch (error) {
    console.error("LOAD INDIVIDUAL ERROR:", error);

    tbody.innerHTML = `
      <tr>
        <td colspan="8" style="text-align:center;padding:30px;">
          Failed to load Individual Membership data.
        </td>
      </tr>
    `;

    renderStats({});
  }
}

/* =========================================================
   STATS
========================================================= */

function renderStats(data) {
  const total = document.getElementById("statTotal");
  const pending = document.getElementById("statPending");
  const approved = document.getElementById("statApproved");
  const rejected = document.getElementById("statRejected");

  if (total) total.textContent = data.total ?? 0;
  if (pending) pending.textContent = data.pending ?? 0;
  if (approved) approved.textContent = data.approved ?? 0;
  if (rejected) rejected.textContent = data.rejected ?? 0;
}

/* =========================================================
   INTEREST FILTER (DYNAMIC)
========================================================= */

function populateInterestFilter(data) {
  const select = document.getElementById("interestFilter");

  if (!select) return;

  const current = select.value;

  const interests = new Set();

  data.forEach((item) => {
    splitList(item.interest).forEach((value) => interests.add(value));
  });

  const sorted = Array.from(interests).sort((a, b) =>
    a.localeCompare(b, undefined, { sensitivity: "base" }),
  );

  select.innerHTML = `<option value="">All Interest</option>`;

  sorted.forEach((value) => {
    const option = document.createElement("option");
    option.value = value;
    option.textContent = value;
    select.appendChild(option);
  });

  select.value = current;
}

/* =========================================================
   HELPERS
========================================================= */

function splitList(value) {
  return String(value || "")
    .split(",")
    .map((v) => v.trim())
    .filter(Boolean);
}

function toTime(value) {
  const time = new Date(value).getTime();
  return Number.isNaN(time) ? 0 : time;
}

function getRegistrantDate(item) {
  return item.submitted_at || item.created_at || "";
}

function formatMemberDate(value) {
  if (!value) return "-";

  const date = new Date(String(value).replace(" ", "T"));

  if (Number.isNaN(date.getTime())) return "-";

  return date.toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

function escapeHtml(value = "") {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function resolvePhotoPath(path) {
  if (!path) return "";

  let v = String(path).trim().replace(/\\/g, "/");

  if (/^https?:\/\//i.test(v)) return v;

  v = v.replace(/^\/+/, "");

  if (v.startsWith("uploads/")) return "/" + v;

  return UPLOAD_URL + "/" + v.replace(/^uploads\//, "");
}

function getInitials(name) {
  const parts = String(name || "").trim().split(/\s+/).filter(Boolean);

  if (!parts.length) return "?";

  if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();

  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

function getStatusClass(status) {
  switch (String(status || "pending").toLowerCase()) {
    case "approved":
      return "status-published";
    case "rejected":
      return "status-archived";
    case "pending":
    default:
      return "status-draft";
  }
}

/* =========================================================
   FILTER + SORT
========================================================= */

function filterIndividuals() {
  const keyword = document.getElementById("searchIndividual").value.toLowerCase().trim();
  const status = document.getElementById("statusFilter").value;
  const interest = document.getElementById("interestFilter").value;
  const dateFrom = document.getElementById("dateFromFilter").value;
  const dateTo = document.getElementById("dateToFilter").value;
  const sortValue = document.getElementById("sortFilter").value;

  const filtered = allIndividuals.filter((item) => {
    const name = String(item.full_name || "").toLowerCase();
    const email = String(item.email || "").toLowerCase();
    const phone = String(item.phone || "").toLowerCase();

    const matchesKeyword =
      !keyword ||
      name.includes(keyword) ||
      email.includes(keyword) ||
      phone.includes(keyword);

    const matchesStatus = !status || String(item.status || "pending") === status;

    const matchesInterest = !interest || splitList(item.interest).includes(interest);

    const itemDate = String(getRegistrantDate(item)).slice(0, 10);

    const matchesDateFrom = !dateFrom || (itemDate && itemDate >= dateFrom);
    const matchesDateTo = !dateTo || (itemDate && itemDate <= dateTo);

    return matchesKeyword && matchesStatus && matchesInterest && matchesDateFrom && matchesDateTo;
  });

  sortIndividuals(filtered, sortValue);

  renderTable(filtered);
}

function sortIndividuals(list, sortValue) {
  switch (sortValue) {
    case "date_asc":
      list.sort((a, b) => toTime(getRegistrantDate(a)) - toTime(getRegistrantDate(b)));
      break;

    case "name_asc":
      list.sort((a, b) => String(a.full_name || "").localeCompare(String(b.full_name || "")));
      break;

    case "name_desc":
      list.sort((a, b) => String(b.full_name || "").localeCompare(String(a.full_name || "")));
      break;

    case "date_desc":
    default:
      list.sort((a, b) => toTime(getRegistrantDate(b)) - toTime(getRegistrantDate(a)));
      break;
  }

  return list;
}

/* =========================================================
   RENDER TABLE
========================================================= */

function renderTable(data) {
  const tbody = document.getElementById("individualTable");

  tbody.innerHTML = "";

  if (!Array.isArray(data) || data.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="8" style="text-align:center;padding:40px;">
          No registrants found.
        </td>
      </tr>
    `;

    return;
  }

  data.forEach((item) => {
    const photoHtml = item.photo
      ? `<img src="${resolvePhotoPath(item.photo)}" class="member-photo" alt="${escapeHtml(item.full_name || "Photo")}">`
      : `<div class="member-photo-placeholder">${escapeHtml(getInitials(item.full_name))}</div>`;

    const roleChips = splitList(item.role_in_industry)
      .map((r) => `<span class="chip">${escapeHtml(r)}</span>`)
      .join("") || "-";

    const interestChips = splitList(item.interest)
      .map((r) => `<span class="chip">${escapeHtml(r)}</span>`)
      .join("") || "-";

    const status = String(item.status || "pending").toLowerCase();

    const statusLabel = status.charAt(0).toUpperCase() + status.slice(1);

    tbody.innerHTML += `
      <tr>
        <td>${photoHtml}</td>

        <td>
          <strong>${escapeHtml(item.full_name || "-")}</strong>
        </td>

        <td class="contact-cell">
          <strong>${escapeHtml(item.phone || "-")}</strong>
          <span>${escapeHtml(item.email || "-")}</span>
        </td>

        <td><div class="chip-group">${roleChips}</div></td>

        <td><div class="chip-group">${interestChips}</div></td>

        <td>${formatMemberDate(getRegistrantDate(item))}</td>

        <td>
          <span class="status-badge ${getStatusClass(status)}">
            ${statusLabel}
          </span>
        </td>

        <td>
          <div class="table-action">

            <button type="button" class="table-btn edit" onclick="viewIndividual(${item.id})" title="Quick view">
              View
            </button>

            <a href="form.php?id=${item.id}" class="table-btn edit" title="Edit full data">
              Edit
            </a>

            <button type="button" class="table-btn delete" onclick="deleteIndividual(${item.id})">
              Delete
            </button>

          </div>
        </td>
      </tr>
    `;
  });
}

/* =========================================================
   VIEW DETAIL MODAL
========================================================= */

function viewIndividual(id) {
  const item = allIndividuals.find((x) => Number(x.id) === Number(id));

  if (!item) return;

  document.getElementById("individualModalName").textContent = item.full_name || "Registrant Detail";

  const photoHtml = item.photo
    ? `<img src="${resolvePhotoPath(item.photo)}" class="detail-photo" alt="${escapeHtml(item.full_name || "Photo")}">`
    : "";

  const portfolioHtml = item.portfolio_link
    ? `<a href="${escapeHtml(item.portfolio_link)}" target="_blank" rel="noopener noreferrer">${escapeHtml(item.portfolio_link)}</a>`
    : "-";

  document.getElementById("individualModalBody").innerHTML = `

    ${photoHtml}

    <div class="detail-row">
      <span>Full Name</span>
      <span>${escapeHtml(item.full_name || "-")}</span>
    </div>

    <div class="detail-row">
      <span>Phone (WhatsApp)</span>
      <span>${escapeHtml(item.phone || "-")}</span>
    </div>

    <div class="detail-row">
      <span>Email</span>
      <span>${escapeHtml(item.email || "-")}</span>
    </div>

    <div class="detail-row">
      <span>Role in Film Industry</span>
      <span>${escapeHtml(splitList(item.role_in_industry).join(", ") || "-")}</span>
    </div>

    <div class="detail-row">
      <span>Portfolio Link</span>
      ${portfolioHtml}
    </div>

    <div class="detail-row">
      <span>Interest in JFC</span>
      <span>${escapeHtml(splitList(item.interest).join(", ") || "-")}</span>
    </div>

    <div class="detail-row">
      <span>Submitted</span>
      <span>${formatMemberDate(getRegistrantDate(item))}</span>
    </div>

  `;

  document.getElementById("individualModalFooter").innerHTML = `

    <select id="modalStatusSelect" class="table-filter" style="min-width:140px;">
      <option value="pending" ${item.status === "pending" ? "selected" : ""}>Pending</option>
      <option value="approved" ${item.status === "approved" ? "selected" : ""}>Approved</option>
      <option value="rejected" ${item.status === "rejected" ? "selected" : ""}>Rejected</option>
    </select>

    <button type="button" class="btn btn-secondary" onclick="updateIndividualStatus(${item.id})">
      Update Status
    </button>

    <a href="form.php?id=${item.id}" class="btn btn-primary">
      Edit Full Data
    </a>

  `;

  document.getElementById("individualModalOverlay").classList.add("show");
}

function closeIndividualModal() {
  document.getElementById("individualModalOverlay").classList.remove("show");
}

/* =========================================================
   UPDATE STATUS
========================================================= */

async function updateIndividualStatus(id) {
  const select = document.getElementById("modalStatusSelect");
  const status = select ? select.value : "pending";

  try {
    const response = await fetch(API_URL + "/individual/update-status.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id, status }),
    });

    const result = await response.json();

    if (!result.success) {
      alert(result.message || "Failed to update status.");
      return;
    }

    const item = allIndividuals.find((x) => Number(x.id) === Number(id));
    if (item) item.status = status;

    renderStats({
      total: allIndividuals.length,
      pending: allIndividuals.filter((x) => x.status === "pending").length,
      approved: allIndividuals.filter((x) => x.status === "approved").length,
      rejected: allIndividuals.filter((x) => x.status === "rejected").length,
    });

    filterIndividuals();
    closeIndividualModal();
  } catch (error) {
    console.error("UPDATE STATUS ERROR:", error);
    alert("Failed to update status.");
  }
}

/* =========================================================
   DELETE
========================================================= */

async function deleteIndividual(id) {
  if (!confirm("Delete this registrant? This cannot be undone.")) return;

  try {
    const response = await fetch(API_URL + "/individual/delete-individual.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id }),
    });

    const result = await response.json();

    if (!result.success) {
      alert(result.message || "Failed to delete record.");
      return;
    }

    await loadIndividuals();
  } catch (error) {
    console.error("DELETE INDIVIDUAL ERROR:", error);
    alert("Failed to delete record.");
  }
}

/* =========================================================
   EXPORT - EXCEL (SheetJS)
========================================================= */

function getFilteredRowsForExport() {
  const keyword = document.getElementById("searchIndividual").value.toLowerCase().trim();
  const status = document.getElementById("statusFilter").value;
  const interest = document.getElementById("interestFilter").value;
  const dateFrom = document.getElementById("dateFromFilter").value;
  const dateTo = document.getElementById("dateToFilter").value;
  const sortValue = document.getElementById("sortFilter").value;

  const filtered = allIndividuals.filter((item) => {
    const name = String(item.full_name || "").toLowerCase();
    const email = String(item.email || "").toLowerCase();
    const phone = String(item.phone || "").toLowerCase();

    const matchesKeyword =
      !keyword || name.includes(keyword) || email.includes(keyword) || phone.includes(keyword);

    const matchesStatus = !status || String(item.status || "pending") === status;
    const matchesInterest = !interest || splitList(item.interest).includes(interest);

    const itemDate = String(getRegistrantDate(item)).slice(0, 10);
    const matchesDateFrom = !dateFrom || (itemDate && itemDate >= dateFrom);
    const matchesDateTo = !dateTo || (itemDate && itemDate <= dateTo);

    return matchesKeyword && matchesStatus && matchesInterest && matchesDateFrom && matchesDateTo;
  });

  sortIndividuals(filtered, sortValue);

  return filtered;
}

function exportToExcel() {
  const rows = getFilteredRowsForExport();

  if (!rows.length) {
    alert("No data to export.");
    return;
  }

  const data = rows.map((item) => ({
    "Timestamp": getRegistrantDate(item),
    "Nama Lengkap": item.full_name || "",
    "No. HP (Whatsapp)": item.phone || "",
    "Email": item.email || "",
    "Peran dalam Ekosistem Perfilman": splitList(item.role_in_industry).join(", "),
    "Link Portfolio": item.portfolio_link || "",
    "Minat dalam Ekosistem JFC": splitList(item.interest).join(", "),
    "Status": item.status || "pending",
  }));

  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();

  XLSX.utils.book_append_sheet(workbook, worksheet, "Individual Membership");

  const filename = `individual-membership-${new Date().toISOString().slice(0, 10)}.xlsx`;

  XLSX.writeFile(workbook, filename);
}

/* =========================================================
   EXPORT - PDF (jsPDF + autotable)
========================================================= */

function exportToPdf() {
  const rows = getFilteredRowsForExport();

  if (!rows.length) {
    alert("No data to export.");
    return;
  }

  const { jsPDF } = window.jspdf;

  const doc = new jsPDF({ orientation: "landscape" });

  doc.setFontSize(14);
  doc.text("Individual Membership - Jakarta Film Commission", 14, 15);

  const tableData = rows.map((item) => [
    formatMemberDate(getRegistrantDate(item)),
    item.full_name || "-",
    item.phone || "-",
    item.email || "-",
    splitList(item.role_in_industry).join(", ") || "-",
    splitList(item.interest).join(", ") || "-",
    (item.status || "pending"),
  ]);

  doc.autoTable({
    startY: 22,
    head: [["Date", "Name", "Phone", "Email", "Role", "Interest", "Status"]],
    body: tableData,
    styles: { fontSize: 8, cellPadding: 3 },
    headStyles: { fillColor: [255, 90, 31] },
  });

  const filename = `individual-membership-${new Date().toISOString().slice(0, 10)}.pdf`;

  doc.save(filename);
}
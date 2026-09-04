let allUsers = [];

/* =========================================================
   INIT
========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  loadUsers();
});

/* =========================================================
   LOAD USERS
========================================================= */

async function loadUsers() {
  try {
    const url = API_URL + "/users/get-admin-users.php";

    const res = await fetch(url);

    if (!res.ok) {
      throw new Error("HTTP Error " + res.status);
    }

    const result = await res.json();

    console.log("ADMIN USERS DATA:", result);

    if (!result.success) {
      throw new Error(result.message || "Failed to load users");
    }

    allUsers = Array.isArray(result.data?.items) ? result.data.items : [];

    renderStats(result.data || {});

    filterUsers();
  } catch (error) {
    console.error("LOAD USERS ERROR:", error);

    const tbody = document.getElementById("userTable");

    tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center;padding:30px;">
                    Failed to load users.
                </td>
            </tr>
        `;

    renderStats({});
  }
}

/* =========================================================
   RENDER STATS
========================================================= */

function renderStats(data) {
  const totalEl = document.getElementById("totalUsers");
  const activeEl = document.getElementById("activeUsers");
  const inactiveEl = document.getElementById("inactiveUsers");

  if (totalEl) totalEl.textContent = data.total ?? 0;
  if (activeEl) activeEl.textContent = data.active ?? 0;
  if (inactiveEl) inactiveEl.textContent = data.inactive ?? 0;
}

/* =========================================================
   FILTER + SORT
========================================================= */

function filterUsers() {
  const keyword = document
    .getElementById("searchUser")
    .value.toLowerCase()
    .trim();

  const role = document.getElementById("roleFilter").value;

  const status = document.getElementById("statusFilter").value;

  const sortValue = document.getElementById("sortFilter").value;

  const filtered = allUsers.filter((user) => {
    const name = String(user.name || "").toLowerCase();
    const username = String(user.username || "").toLowerCase();
    const email = String(user.email || "").toLowerCase();

    const matchesKeyword =
      !keyword ||
      name.includes(keyword) ||
      username.includes(keyword) ||
      email.includes(keyword);

    const matchesRole = !role || user.role === role;

    const matchesStatus = !status || user.status === status;

    return matchesKeyword && matchesRole && matchesStatus;
  });

  sortUsers(filtered, sortValue);

  renderTable(filtered);
}

function searchUser() {
  filterUsers();
}

/* =========================================================
   SORT
========================================================= */

function toTime(value) {
  const time = new Date(value).getTime();
  return Number.isNaN(time) ? 0 : time;
}

function getUserSortTime(user) {
  return toTime(user.updated_at || user.created_at || "");
}

function sortUsers(list, sortValue) {
  switch (sortValue) {
    case "updated_asc":
      list.sort((a, b) => getUserSortTime(a) - getUserSortTime(b));
      break;

    case "login_desc":
      list.sort((a, b) => toTime(b.last_login) - toTime(a.last_login));
      break;

    case "name_asc":
      list.sort((a, b) =>
        String(a.name || "").localeCompare(String(b.name || "")),
      );
      break;

    case "name_desc":
      list.sort((a, b) =>
        String(b.name || "").localeCompare(String(a.name || "")),
      );
      break;

    case "updated_desc":
    default:
      list.sort((a, b) => getUserSortTime(b) - getUserSortTime(a));
      break;
  }

  return list;
}

/* =========================================================
   ROLE LABEL / CLASS
========================================================= */

function getRoleLabel(role) {
  switch (role) {
    case "super_admin":
      return "Super Admin";
    case "content_admin":
      return "Content Admin";
    case "communication_admin":
      return "Communication Admin";
    case "membership_admin":
      return "Membership Admin";
    default:
      return role || "Unknown";
  }
}

function getRoleClass(role) {
  switch (role) {
    case "super_admin":
      return "role-badge super-admin";
    case "content_admin":
      return "role-badge content-admin";
    case "communication_admin":
      return "role-badge communication-admin";
    case "membership_admin":
      return "role-badge membership-admin";
    default:
      return "role-badge other";
  }
}

/* =========================================================
   INITIALS AVATAR
========================================================= */

function getInitials(name) {
  const parts = String(name || "")
    .trim()
    .split(/\s+/)
    .filter(Boolean);

  if (!parts.length) return "?";

  if (parts.length === 1) {
    return parts[0].substring(0, 2).toUpperCase();
  }

  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

/* =========================================================
   DATE FORMAT
========================================================= */

function formatUserDate(value) {
  if (!value) return "-";

  const date = new Date(value);

  if (Number.isNaN(date.getTime())) return "-";

  return (
    date.toLocaleDateString("en-GB", {
      day: "2-digit",
      month: "short",
      year: "numeric",
    }) +
    " " +
    date.toLocaleTimeString("en-GB", {
      hour: "2-digit",
      minute: "2-digit",
    })
  );
}

/* =========================================================
   ESCAPE HTML
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
   RENDER TABLE
========================================================= */

function renderTable(data) {
  const tbody = document.getElementById("userTable");

  tbody.innerHTML = "";

  if (!Array.isArray(data) || data.length === 0) {
    tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center;padding:40px;">
                    No users found.
                </td>
            </tr>
        `;

    return;
  }

  data.forEach((user) => {
    const isActive = user.status === "active";

    /*
        |--------------------------------------------------------------------------
        | Super Admin aktif tidak boleh dinonaktifkan lewat toggle
        | (dicegah juga di backend, tapi kita disable di UI biar jelas).
        |--------------------------------------------------------------------------
        */

    const lockToggle = user.role === "super_admin" && isActive;

    tbody.innerHTML += `

            <tr>

                <td>

                    <div style="display:flex;align-items:center;gap:12px;">

                        <div class="user-avatar-initials">
                            ${escapeHtml(getInitials(user.name))}
                        </div>

                        <div>

                            <strong style="display:block;">
                                ${escapeHtml(user.name || "-")}
                            </strong>

                            <span style="display:block;font-size:12px;color:#999e99;">
                                @${escapeHtml(user.username || "-")}
                                ${user.email ? " &middot; " + escapeHtml(user.email) : ""}
                            </span>

                        </div>

                    </div>

                </td>


                <td>
                    <span class="${getRoleClass(user.role)}">
                        ${escapeHtml(getRoleLabel(user.role))}
                    </span>
                </td>


                <td>

                    <label
                        class="switch"
                        title="${lockToggle ? "The only Super Admin cannot be deactivated" : isActive ? "Click to deactivate" : "Click to activate"}"
                    >

                        <input
                            type="checkbox"
                            ${isActive ? "checked" : ""}
                            ${lockToggle ? "disabled" : ""}
                            onchange="handleStatusToggle(${user.id}, this)">

                        <span class="slider"></span>

                    </label>

                </td>


                <td>
                    ${formatUserDate(user.last_login)}
                </td>


                <td>

                    <div class="table-action">

                        <a href="form.php?id=${user.id}" class="table-btn edit">
                            Edit
                        </a>

                        <button
                            class="table-btn delete"
                            onclick="deleteUser(${user.id})">

                            Delete

                        </button>

                    </div>

                </td>

            </tr>

        `;
  });
}

/* =========================================================
   TOGGLE STATUS (ACTIVATE / DEACTIVATE)
========================================================= */

async function handleStatusToggle(id, checkbox) {
  const newStatus = checkbox.checked ? "active" : "inactive";

  checkbox.disabled = true;

  try {
    const response = await fetch(API_URL + "/users/toggle-user-status.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: id, status: newStatus }),
    });

    const result = await response.json();

    if (!result.success) {
      /*
            | Gagal -> kembalikan toggle ke posisi semula
            */
      checkbox.checked = !checkbox.checked;

      alert(result.message || "Failed to update status.");

      return;
    }

    /*
        | Sinkronkan data lokal + stats tanpa perlu reload penuh
        */

    const user = allUsers.find((u) => u.id === id);

    if (user) {
      user.status = newStatus;
    }

    const activeCount = allUsers.filter((u) => u.status === "active").length;

    renderStats({
      total: allUsers.length,
      active: activeCount,
      inactive: allUsers.length - activeCount,
    });
  } catch (error) {
    console.error("TOGGLE STATUS ERROR:", error);

    checkbox.checked = !checkbox.checked;

    alert("Failed to update status.");
  } finally {
    checkbox.disabled = false;
  }
}

/* =========================================================
   DELETE USER
========================================================= */

async function deleteUser(id) {
  if (!confirm("Delete this user? This cannot be undone.")) {
    return;
  }

  try {
    const response = await fetch(API_URL + "/users/delete-user.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: id }),
    });

    const result = await response.json();

    if (!result.success) {
      alert(result.message || "Failed to delete user.");
      return;
    }

    await loadUsers();
  } catch (error) {
    console.error("DELETE USER ERROR:", error);
    alert("Failed to delete user.");
  }
}

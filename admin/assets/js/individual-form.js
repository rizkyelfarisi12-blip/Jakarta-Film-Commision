let isEditMode = false;

/* =========================================================
   INIT
========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");

  isEditMode = !!id;

  setupRoleToggle();

  if (isEditMode) {
    loadIndividual(id);
  }

  document.getElementById("photoFile").addEventListener("change", handlePhotoSelect);
  document.getElementById("saveIndividualBtn").addEventListener("click", saveIndividual);
});

/* =========================================================
   HELPERS
========================================================= */

function getValue(id) {
  const el = document.getElementById(id);
  return el ? el.value.trim() : "";
}

function setValue(id, value) {
  const el = document.getElementById(id);
  if (el) el.value = value ?? "";
}

function resolvePhotoPath(path) {
  if (!path) return "";

  let v = String(path).trim().replace(/\\/g, "/");

  if (/^https?:\/\//i.test(v)) return v;

  v = v.replace(/^\/+/, "");

  if (v.startsWith("uploads/")) return "/" + v;

  return UPLOAD_URL + "/" + v.replace(/^uploads\//, "");
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

/* =========================================================
   ROLE - CUSTOM "OTHERS" TOGGLE
========================================================= */

function setupRoleToggle() {
  const select = document.getElementById("role");
  const group = document.getElementById("customRoleGroup");
  const input = document.getElementById("role_other");

  select.addEventListener("change", () => {
    const isOthers = select.value === "Others";

    group.style.display = isOthers ? "block" : "none";

    if (!isOthers) input.value = "";
  });
}

/* =========================================================
   INTERESTS - CHECKBOX HELPERS
========================================================= */

function getCheckedInterests() {
  return Array.from(
    document.querySelectorAll("#interestCheckboxGroup input[type=checkbox]:checked"),
  ).map((el) => el.value);
}

function setCheckedInterests(values) {
  const list = Array.isArray(values) ? values : [];

  document.querySelectorAll("#interestCheckboxGroup input[type=checkbox]").forEach((el) => {
    el.checked = list.includes(el.value);
  });
}

/* =========================================================
   LOAD (EDIT MODE)
========================================================= */

async function loadIndividual(id) {
  try {
    const response = await fetch(
      API_URL + "/individual/get-individual.php?id=" + encodeURIComponent(id),
    );

    const result = await response.json();

    if (!result.success || !result.data) {
      throw new Error(result.message || "Registrant not found.");
    }

    const item = result.data;

    setValue("individualId", item.id);
    setValue("full_name", item.full_name);
    setValue("whatsapp", item.whatsapp);
    setValue("email", item.email);
    setValue("portfolio_link", item.portfolio_link);
    setValue("status", item.status || "pending");

    setValue("role", item.role || "");

    if (item.role === "Others") {
      document.getElementById("customRoleGroup").style.display = "block";
      setValue("role_other", item.role_other);
    }

    let interests = [];

    try {
      interests = JSON.parse(item.interests || "[]");
    } catch (e) {
      interests = [];
    }

    setCheckedInterests(interests);

    setValue("photo", item.photo);

    if (item.photo) {
      const preview = document.getElementById("imagePreview");
      preview.src = resolvePhotoPath(item.photo);
      preview.style.display = "block";
    }

    const registeredGroup = document.getElementById("registeredAtGroup");
    const registeredValue = document.getElementById("registeredAtValue");

    if (registeredGroup && registeredValue) {
      registeredValue.textContent = formatMemberDate(item.created_at);
      registeredGroup.style.display = "block";
    }

    document.getElementById("pageTitle").textContent = "Edit Registrant";
    document.getElementById("saveIndividualBtn").innerHTML =
      `<i class="ri-save-line"></i> Update`;
  } catch (error) {
    console.error("LOAD INDIVIDUAL ERROR:", error);
    alert(error.message || "Failed to load registrant.");
    window.location.href = "index.php";
  }
}

/* =========================================================
   PHOTO UPLOAD
========================================================= */

async function handlePhotoSelect() {
  const input = document.getElementById("photoFile");
  const file = input.files?.[0];

  if (!file) return;

  const types = ["image/jpeg", "image/png", "image/webp"];

  if (!types.includes(file.type)) {
    alert("Only JPG, PNG and WEBP images are allowed.");
    input.value = "";
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    alert("Image size must not exceed 5 MB.");
    input.value = "";
    return;
  }

  const preview = document.getElementById("imagePreview");

  const reader = new FileReader();
  reader.onload = (e) => {
    preview.src = e.target.result;
    preview.style.display = "block";
  };
  reader.readAsDataURL(file);

  const formData = new FormData();
  formData.append("photo", file);

  try {
    const response = await fetch(API_URL + "/individual/upload-photo.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (!result.success) {
      throw new Error(result.message || "Failed to upload photo.");
    }

    setValue("photo", result.path);
    preview.src = resolvePhotoPath(result.path);
    preview.style.display = "block";
  } catch (error) {
    console.error("UPLOAD PHOTO ERROR:", error);
    alert(error.message || "Failed to upload photo.");
    setValue("photo", "");
    preview.src = "";
    preview.style.display = "none";
    input.value = "";
  }
}

/* =========================================================
   VALIDATE
   (mengikuti kolom NOT NULL di tabel members_individual)
========================================================= */

function validateIndividualForm() {
  if (!getValue("full_name")) {
    return { valid: false, message: "Full name is required." };
  }

  if (!getValue("whatsapp")) {
    return { valid: false, message: "WhatsApp number is required." };
  }

  const email = getValue("email");

  if (!email) {
    return { valid: false, message: "Email is required." };
  }

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(email)) {
    return { valid: false, message: "Please enter a valid email address." };
  }

  const role = getValue("role");

  if (!role) {
    return { valid: false, message: "Please select a role." };
  }

  if (role === "Others" && !getValue("role_other")) {
    return { valid: false, message: "Please specify the custom role." };
  }

  if (!getValue("portfolio_link")) {
    return { valid: false, message: "Portfolio link is required." };
  }

  if (!getValue("photo")) {
    return { valid: false, message: "Profile photo is required." };
  }

  return { valid: true };
}

/* =========================================================
   COLLECT DATA
========================================================= */

function collectIndividualFormData() {
  return {
    id: getValue("individualId") || null,
    full_name: getValue("full_name"),
    whatsapp: getValue("whatsapp"),
    email: getValue("email"),
    role: getValue("role"),
    role_other: getValue("role") === "Others" ? getValue("role_other") : "",
    portfolio_link: getValue("portfolio_link"),
    interests: getCheckedInterests(),
    photo: getValue("photo"),
    status: getValue("status") || "pending",
  };
}

/* =========================================================
   SAVE
========================================================= */

async function saveIndividual() {
  const validation = validateIndividualForm();

  if (!validation.valid) {
    alert(validation.message);
    return;
  }

  const data = collectIndividualFormData();

  const button = document.getElementById("saveIndividualBtn");
  const originalHtml = button.innerHTML;

  button.disabled = true;
  button.innerHTML = `<i class="ri-loader-4-line"></i> Saving...`;

  try {
    const endpoint = isEditMode
      ? API_URL + "/individual/update-individual.php"
      : API_URL + "/individual/create-individual.php";

    const response = await fetch(endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (!result.success) {
      throw new Error(result.message || "Failed to save registrant.");
    }

    alert(result.message || "Registrant successfully saved.");
    window.location.href = "index.php";
  } catch (error) {
    console.error("SAVE INDIVIDUAL ERROR:", error);
    alert(error.message || "An error occurred while saving.");
  } finally {
    button.disabled = false;
    button.innerHTML = originalHtml;
  }
}

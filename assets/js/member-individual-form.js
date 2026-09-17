/* =============================================================
   JAKARTA FILM COMMISSION
   INDIVIDUAL MEMBERSHIP FORM
   Place this file at: assets/js/member-individual-form.js
============================================================= */

/*
|--------------------------------------------------------------------------
| CONFIGURATION
|--------------------------------------------------------------------------
|
| Points to the backend endpoint. Adjust if your API is mounted
| somewhere else (see api/members/create-member-individual.php).
|
*/
const MEMBER_INDIVIDUAL_API = "api/members/create-member-individual.php";

const MAX_PHOTO_SIZE = 5 * 1024 * 1024; // 5 MB
const ALLOWED_PHOTO_TYPES = ["image/jpeg", "image/png", "image/webp"];

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("memberIndividualForm");

  if (!form) {
    return;
  }

  setupRoleToggle();
  setupPhotoUpload();
  setupSubmit(form);
});

/* =============================================================
   ROLE "OTHERS" TOGGLE
============================================================= */
function setupRoleToggle() {
  const role = document.getElementById("role");
  const otherGroup = document.getElementById("roleOtherGroup");
  const otherInput = document.getElementById("roleOther");

  if (!role || !otherGroup) {
    return;
  }

  const sync = () => {
    const isOthers = role.value === "Others";

    otherGroup.classList.toggle("show", isOthers);

    if (otherInput) {
      otherInput.required = isOthers;

      if (!isOthers) {
        otherInput.value = "";
      }
    }
  };

  role.addEventListener("change", sync);
  sync();
}

/* =============================================================
   PHOTO UPLOAD + PREVIEW
============================================================= */
function setupPhotoUpload() {
  const input = document.getElementById("profilePhoto");
  const trigger = document.getElementById("photoUploadBtn");
  const preview = document.getElementById("photoPreview");
  const placeholder = document.getElementById("photoPlaceholder");

  if (!input || !trigger) {
    return;
  }

  trigger.addEventListener("click", () => input.click());

  input.addEventListener("change", () => {
    const file = input.files && input.files[0];

    if (!file) {
      return;
    }

    if (!ALLOWED_PHOTO_TYPES.includes(file.type)) {
      alert("Format foto harus JPG, PNG, atau WEBP.");
      input.value = "";
      return;
    }

    if (file.size > MAX_PHOTO_SIZE) {
      alert("Ukuran foto maksimal 5 MB.");
      input.value = "";
      return;
    }

    const reader = new FileReader();

    reader.onload = (event) => {
      preview.src = event.target.result;
      preview.style.display = "block";

      if (placeholder) {
        placeholder.style.display = "none";
      }
    };

    reader.readAsDataURL(file);
  });
}

/* =============================================================
   VALIDATION
============================================================= */
function validateMemberForm(form) {
  clearFieldErrors(form);

  const fullName = form.full_name.value.trim();
  const whatsapp = form.whatsapp.value.trim();
  const email = form.email.value.trim();
  const role = form.role.value.trim();
  const roleOther = form.role_other.value.trim();
  const portfolioLink = form.portfolio_link.value.trim();
  const interests = form.querySelectorAll('input[name="interests[]"]:checked');
  const photo = form.photo.files[0];
  const consent = document.getElementById("consentCheck");

  if (!fullName) {
    return fieldError(form.full_name, "Nama lengkap wajib diisi.");
  }

  const phonePattern = /^\+?[0-9\s-]{8,20}$/;

  if (!whatsapp || !phonePattern.test(whatsapp)) {
    return fieldError(form.whatsapp, "Masukkan nomor WhatsApp yang valid.");
  }

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!email || !emailPattern.test(email)) {
    return fieldError(form.email, "Masukkan alamat email yang valid.");
  }

  if (!role) {
    return fieldError(form.role, "Pilih peranmu dalam ekosistem perfilman.");
  }

  if (role === "Others" && !roleOther) {
    return fieldError(form.role_other, "Sebutkan peranmu.");
  }

  if (!portfolioLink) {
    return fieldError(form.portfolio_link, "Link portfolio wajib diisi.");
  }

  if (!/^https?:\/\//i.test(portfolioLink)) {
    form.portfolio_link.value = "https://" + portfolioLink;
  }

  try {
    new URL(form.portfolio_link.value.trim());
  } catch (error) {
    return fieldError(form.portfolio_link, "Masukkan link yang valid (contoh: https://...).");
  }

  if (!interests.length) {
    return { valid: false, message: "Pilih minimal satu minat dalam ekosistem Jakarta Film Commission." };
  }

  if (!photo) {
    return { valid: false, message: "Unggah foto profil terlebih dahulu." };
  }

  if (!ALLOWED_PHOTO_TYPES.includes(photo.type)) {
    return { valid: false, message: "Format foto harus JPG, PNG, atau WEBP." };
  }

  if (photo.size > MAX_PHOTO_SIZE) {
    return { valid: false, message: "Ukuran foto maksimal 5 MB." };
  }

  if (consent && !consent.checked) {
    return { valid: false, message: "Kamu perlu menyetujui penggunaan data sebelum mengirim formulir." };
  }

  return { valid: true };
}

function fieldError(field, message) {
  if (field) {
    field.classList.add("mif-field-error");
    field.focus();
  }

  return { valid: false, message };
}

function clearFieldErrors(form) {
  form.querySelectorAll(".mif-field-error").forEach((el) => {
    el.classList.remove("mif-field-error");
  });
}

/* =============================================================
   SUBMIT
============================================================= */
function setupSubmit(form) {
  form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const errorBanner = document.getElementById("formErrorBanner");
    const submitBtn = document.getElementById("submitBtn");

    errorBanner.classList.remove("show");
    errorBanner.textContent = "";

    const validation = validateMemberForm(form);

    if (!validation.valid) {
      errorBanner.textContent = validation.message;
      errorBanner.classList.add("show");
      return;
    }

    const interests = Array.from(
      form.querySelectorAll('input[name="interests[]"]:checked'),
    ).map((el) => el.value);

    const formData = new FormData();

    formData.append("full_name", form.full_name.value.trim());
    formData.append("whatsapp", form.whatsapp.value.trim());
    formData.append("email", form.email.value.trim());
    formData.append("role", form.role.value);
    formData.append(
      "role_other",
      form.role.value === "Others" ? form.role_other.value.trim() : "",
    );
    formData.append("portfolio_link", form.portfolio_link.value.trim());
    formData.append("interests", JSON.stringify(interests));
    formData.append("photo", form.photo.files[0]);

    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = "Mengirim...";

    try {
      const response = await fetch(MEMBER_INDIVIDUAL_API, {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (!result.success) {
        throw new Error(result.message || "Gagal mengirim pendaftaran.");
      }

      form.style.display = "none";

      const successBox = document.getElementById("formSuccess");

      if (successBox) {
        successBox.classList.add("show");
        successBox.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    } catch (error) {
      console.error("MEMBER INDIVIDUAL SUBMIT ERROR:", error);

      errorBanner.textContent =
        error.message || "Terjadi kesalahan saat mengirim pendaftaran. Silakan coba lagi.";
      errorBanner.classList.add("show");
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
}

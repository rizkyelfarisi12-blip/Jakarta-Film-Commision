/* =============================================================
   JAKARTA FILM COMMISSION
   MEMBER INDIVIDU - FORM LOGIC
============================================================= */
(function () {
  "use strict";

  /* -----------------------------------------------------------
     KONFIGURASI
     DEMO_MODE = true  -> belum mengirim ke server (tampilan saja)
     Setelah backend publik siap, ubah ke false dan sesuaikan API_URL.
  ----------------------------------------------------------- */
  const DEMO_MODE = false;
  const API_URL = "api/individual/create-member-individual.php";
  const DRAFT_KEY = "jfc-member-individual-draft";

  const MAX_ORIGINAL = 5 * 1024 * 1024; // foto asli sebelum dipotong
  const MAX_FINAL = 1 * 1024 * 1024; // hasil akhir (sesuai Google Form: 1 MB)

  document.addEventListener("DOMContentLoaded", init);

  function init() {
    const form = document.getElementById("memberForm");
    if (!form) return;

    const $ = (id) => document.getElementById(id);
    const state = { photo: null, original: null, cropped: false };

    const reviewModal = MR.modal($("reviewModal"));
    const cropModal = MR.modal($("cropModal"));
    const consent = $("consent");
    const submitBtn = $("submitBtn");
    const banner = $("formBanner");

    MR.initOptionGroups(form);
    MR.bindLiveClear(form);

    /* ---------------- FOTO + CROP ---------------- */
    let cropper = null;
    const cropImg = $("cropImage");

    const openCrop = (file) => {
      state.cropped = false;
      cropImg.onload = () => {
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropImg, {
          aspectRatio: 4 / 5,
          viewMode: 1,
          autoCropArea: 1,
          background: false,
          movable: true,
          zoomable: true,
          scalable: false,
          rotatable: false,
        });
      };
      cropImg.src = URL.createObjectURL(file);
      cropModal.open();
    };

    $("cropModal").addEventListener("mr:close", () => {
      if (cropper) {
        cropper.destroy();
        cropper = null;
      }
      if (cropImg.src.startsWith("blob:")) URL.revokeObjectURL(cropImg.src);
      cropImg.removeAttribute("src");
      /* dibatalkan sebelum menyimpan & belum ada foto -> reset */
      if (!state.cropped && !state.photo) photoDrop.clear();
    });

    const photoDrop = MR.initDrop({
      root: $("photoDrop"),
      mimes: ["image/jpeg", "image/png", "image/webp"],
      exts: ["jpg", "jpeg", "png", "webp"],
      maxBytes: MAX_ORIGINAL,
      typeMessage: "Format foto harus JPG, PNG, atau WEBP.",
      onFile: (file) => {
        state.original = file;
        openCrop(file);
      },
      onEdit: () => {
        if (state.original) openCrop(state.original);
      },
      onClear: () => {
        state.photo = null;
        state.original = null;
      },
    });

    $("saveCrop").addEventListener("click", () => {
      if (!cropper) return;
      const canvas = cropper.getCroppedCanvas({
        width: 800,
        height: 1000,
        imageSmoothingQuality: "high",
      });

      const toFile = (quality) =>
        new Promise((resolve) => canvas.toBlob(resolve, "image/jpeg", quality));

      (async () => {
        let quality = 0.9;
        let blob = await toFile(quality);
        while (blob && blob.size > MAX_FINAL && quality > 0.4) {
          quality -= 0.1;
          blob = await toFile(quality);
        }
        if (!blob || blob.size > MAX_FINAL) {
          MR.setError(form.querySelector('[data-field="photo"]'), "Foto masih di atas 1 MB. Coba gunakan foto lain.");
          state.cropped = true;
          cropModal.close();
          return;
        }
        state.photo = new File([blob], "profile.jpg", { type: "image/jpeg" });
        state.cropped = true;
        photoDrop.setFile(state.photo);
        cropModal.close();
      })();
    });

    /* ---------------- CHECKLIST ---------------- */
    const val = (name) => form.elements[name].value.trim();
    MR.initChecklist(form, [
      { label: "Data diri", ok: () => val("full_name") && val("whatsapp") && val("email") },
      { label: "Peran", ok: () => groupOk("role") },
      { label: "Minat", ok: () => groupOk("interests") },
      { label: "Link portfolio", ok: () => val("portfolio_link") },
      { label: "Foto profil", ok: () => !!state.photo },
    ]);

    function groupOk(name) {
      const g = MR.readGroup(form, name);
      if (g.otherChecked) return !!g.other;
      return g.values.length > 0;
    }

    /* ---------------- DRAFT ---------------- */
    const draft = MR.initDraft(form, DRAFT_KEY);

    /* ---------------- VALIDASI ---------------- */
    function validate() {
      MR.clearAllErrors(form);
      let ok = true;
      const fail = (name, message) => {
        MR.setError(form.querySelector('[data-field="' + name + '"]'), message);
        ok = false;
      };

      if (val("full_name").length < 3) fail("fullName", "Nama lengkap wajib diisi.");

      if (!val("whatsapp")) fail("whatsapp", "Nomor WhatsApp wajib diisi.");
      else if (!MR.isPhone(val("whatsapp"))) fail("whatsapp", "Masukkan nomor yang valid, contoh 0812 3456 7890.");

      if (!val("email")) fail("email", "Email wajib diisi.");
      else if (!MR.isEmail(val("email"))) fail("email", "Format email belum benar.");

      const role = MR.readGroup(form, "role");
      if (!role.values.length && !role.otherChecked) fail("role", "Pilih satu peran Anda.");
      else if (role.otherChecked && !role.other) fail("role", "Sebutkan peran Anda pada isian lainnya.");

      const interests = MR.readGroup(form, "interests");
      if (!interests.values.length && !interests.otherChecked) fail("interests", "Pilih minimal satu minat.");
      else if (interests.otherChecked && !interests.other && !interests.values.length)
        fail("interests", "Sebutkan minat lainnya atau pilih opsi yang tersedia.");

      if (!val("portfolio_link")) fail("portfolio", "Link portfolio wajib diisi.");
      else if (!MR.normalizeUrl(val("portfolio_link"))) fail("portfolio", "Masukkan link yang valid, contoh https://vimeo.com/namaanda.");

      if (!state.photo) fail("photo", "Foto profil wajib diunggah.");

      if (!ok) MR.focusFirstError(form);
      return ok;
    }

    /* ---------------- DATA ---------------- */
    function collect() {
      const role = MR.readGroup(form, "role");
      const interests = MR.readGroup(form, "interests");
      return {
        fullName: val("full_name"),
        whatsapp: val("whatsapp"),
        email: val("email"),
        role: role.otherChecked ? role.other : role.values[0],
        roleIsOther: role.otherChecked,
        portfolio: MR.normalizeUrl(val("portfolio_link")),
        interests: MR.groupLabels(interests),
      };
    }

    /* ---------------- REVIEW ---------------- */
    $("reviewBtn").addEventListener("click", () => {
      banner.classList.remove("is-show");
      if (!validate()) return;

      const d = collect();
      const photoSrc = document.querySelector("#photoDrop img").src;

      $("reviewContent").innerHTML =
        '<div class="mr-sum-top">' +
        '<img class="mr-sum-photo" src="' + MR.esc(photoSrc) + '" alt="Foto profil">' +
        "<div><strong>" + MR.esc(d.fullName) + "</strong><span>" + MR.esc(d.role) + "</span></div>" +
        "</div>" +
        '<dl class="mr-sum-list">' +
        MR.summaryItem("No. HP (WhatsApp)", MR.esc(d.whatsapp)) +
        MR.summaryItem("Email", MR.esc(d.email)) +
        MR.summaryItem("Peran", MR.esc(d.role)) +
        MR.summaryItem(
          "Link Portfolio",
          '<a href="' + MR.esc(d.portfolio) + '" target="_blank" rel="noopener noreferrer" style="color:inherit;text-decoration:underline">' +
            MR.esc(d.portfolio) + "</a>"
        ) +
        MR.summaryItem("Minat", MR.chipsHtml(d.interests), true) +
        "</dl>";

      consent.checked = false;
      submitBtn.disabled = true;
      reviewModal.open();
    });

    consent.addEventListener("change", () => {
      submitBtn.disabled = !consent.checked;
    });

    /* ---------------- KIRIM ---------------- */
    submitBtn.addEventListener("click", async () => {
      if (!consent.checked) return;
      const d = collect();

      const fd = new FormData();
      fd.append("full_name", d.fullName);
      fd.append("whatsapp", d.whatsapp);
      fd.append("email", d.email);
      fd.append("role", d.roleIsOther ? "Others" : d.role);
      fd.append("role_other", d.roleIsOther ? d.role : "");
      fd.append("portfolio_link", d.portfolio);
      fd.append("interests", JSON.stringify(d.interests));
      fd.append("photo", state.photo, "profile.jpg");

      const original = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = "Mengirim...";

      try {
        await MR.send(API_URL, fd, DEMO_MODE);
        draft.clear();
        reviewModal.close();
        $("formWrap").style.display = "none";
        const success = $("formSuccess");
        success.classList.add("is-show");
        success.scrollIntoView({ behavior: "smooth", block: "center" });
        success.focus({ preventScroll: true });
      } catch (error) {
        reviewModal.close();
        banner.textContent = error.message || "Terjadi kesalahan. Silakan coba lagi.";
        banner.classList.add("is-show");
        banner.scrollIntoView({ behavior: "smooth", block: "center" });
      } finally {
        submitBtn.textContent = original;
        submitBtn.disabled = !consent.checked;
      }
    });
  }
})();

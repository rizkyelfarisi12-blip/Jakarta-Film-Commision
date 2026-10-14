/* =============================================================
   JAKARTA FILM COMMISSION
   MEMBER ENTITAS - FORM LOGIC
============================================================= */
(function () {
  "use strict";

  /* -----------------------------------------------------------
     KONFIGURASI
     DEMO_MODE = true  -> belum mengirim ke server (tampilan saja)
     Setelah backend publik siap, ubah ke false dan sesuaikan API_URL.
  ----------------------------------------------------------- */
  const DEMO_MODE = false;
  const API_URL = "api/entities/create-member-entity.php";
  const DRAFT_KEY = "jfc-member-entity-draft";

  const MAX_PROFILE = 10 * 1024 * 1024; // company profile: 10 MB
  const MAX_LOGO = 1 * 1024 * 1024; // logo: 1 MB

  document.addEventListener("DOMContentLoaded", init);

  function init() {
    const form = document.getElementById("memberForm");
    if (!form) return;

    const $ = (id) => document.getElementById(id);
    const state = { profile: null, logo: null };

    const reviewModal = MR.modal($("reviewModal"));
    const consent = $("consent");
    const submitBtn = $("submitBtn");
    const banner = $("formBanner");

    MR.initOptionGroups(form);
    MR.bindLiveClear(form);

    /* ---------------- UPLOAD ---------------- */
    const profileDrop = MR.initDrop({
      root: $("profileDrop"),
      mimes: [
        "application/pdf",
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.ms-powerpoint",
        "application/vnd.openxmlformats-officedocument.presentationml.presentation",
      ],
      exts: ["pdf", "doc", "docx", "ppt", "pptx"],
      maxBytes: MAX_PROFILE,
      typeMessage: "Format file harus PDF, DOC, DOCX, PPT, atau PPTX.",
      onFile: (file) => {
        state.profile = file;
        profileDrop.setFile(file);
      },
      onClear: () => {
        state.profile = null;
      },
    });

    const logoDrop = MR.initDrop({
      root: $("logoDrop"),
      mimes: ["image/png", "image/jpeg", "image/webp"],
      exts: ["png", "jpg", "jpeg", "webp"],
      maxBytes: MAX_LOGO,
      typeMessage: "Logo harus berformat PNG, JPG, atau WEBP.",
      onFile: (file) => {
        state.logo = file;
        logoDrop.setFile(file);
      },
      onClear: () => {
        state.logo = null;
      },
    });

    /* ---------------- COUNTER DESKRIPSI ---------------- */
    const desc = $("description");
    const counter = form.querySelector('[data-counter-for="description"]');
    const updateCounter = () => {
      counter.textContent = desc.value.length + " / " + desc.maxLength;
    };
    desc.addEventListener("input", updateCounter);

    /* ---------------- CHECKLIST ---------------- */
    const val = (name) => form.elements[name].value.trim();
    const groupOk = (name) => {
      const g = MR.readGroup(form, name);
      return g.otherChecked ? !!g.other : g.values.length > 0;
    };

    MR.initChecklist(form, [
      { label: "Profil entitas", ok: () => val("company_name") && val("description") && groupOk("entity_type") && groupOk("production_type") },
      { label: "Company profile", ok: () => !!state.profile },
      { label: "Minat kolaborasi", ok: () => groupOk("interest") },
      { label: "Kontak PIC", ok: () => val("pic_name") && val("pic_position") && val("pic_email") && val("pic_phone") },
    ]);

    /* ---------------- DRAFT ---------------- */
    const draft = MR.initDraft(form, DRAFT_KEY);
    updateCounter();

    /* ---------------- VALIDASI ---------------- */
    function validate() {
      MR.clearAllErrors(form);
      let ok = true;
      const fail = (name, message) => {
        MR.setError(form.querySelector('[data-field="' + name + '"]'), message);
        ok = false;
      };

      if (!val("company_name")) fail("companyName", "Nama perusahaan wajib diisi.");

      if (val("description").length < 10) fail("description", "Tulis deskripsi singkat (minimal 10 karakter).");

      const type = MR.readGroup(form, "entity_type");
      if (!type.values.length && !type.otherChecked) fail("entityType", "Pilih jenis entitas.");
      else if (type.otherChecked && !type.other) fail("entityType", "Sebutkan jenis entitas Anda.");

      const prod = MR.readGroup(form, "production_type");
      if (!prod.values.length && !prod.otherChecked) fail("productionType", "Pilih minimal satu jenis produksi.");
      else if (prod.otherChecked && !prod.other && !prod.values.length)
        fail("productionType", "Sebutkan jenis produksi lainnya atau pilih opsi yang tersedia.");

      const nib = val("nib").replace(/[\s.-]/g, "");
      if (nib && !/^\d{10,16}$/.test(nib)) fail("nib", "NIB hanya berisi angka (sekitar 13 digit).");

      if (!state.profile) fail("profileFile", "Company profile wajib diunggah.");

      const social = val("social_media");
      if (social && !isSocial(social)) fail("social", "Isi dengan @username atau link media sosial yang valid.");

      const interest = MR.readGroup(form, "interest");
      if (!interest.values.length && !interest.otherChecked) fail("interest", "Pilih minimal satu minat.");
      else if (interest.otherChecked && !interest.other && !interest.values.length)
        fail("interest", "Sebutkan minat lainnya atau pilih opsi yang tersedia.");

      if (val("pic_name").length < 3) fail("picName", "Nama PIC wajib diisi.");
      if (!val("pic_position")) fail("picPosition", "Jabatan PIC wajib diisi.");

      if (!val("pic_email")) fail("picEmail", "Email PIC wajib diisi.");
      else if (!MR.isEmail(val("pic_email"))) fail("picEmail", "Format email belum benar.");

      if (!val("pic_phone")) fail("picPhone", "Nomor WhatsApp PIC wajib diisi.");
      else if (!MR.isPhone(val("pic_phone"))) fail("picPhone", "Masukkan nomor yang valid, contoh 0812 3456 7890.");

      if (!ok) MR.focusFirstError(form);
      return ok;
    }

    /* @username atau URL */
    function isSocial(v) {
      if (/^@[A-Za-z0-9._]{2,30}$/.test(v)) return true;
      return !!MR.normalizeUrl(v);
    }

    /* ---------------- DATA ---------------- */
    function collect() {
      const type = MR.readGroup(form, "entity_type");
      const social = val("social_media");
      return {
        companyName: val("company_name"),
        description: val("description"),
        entityType: type.otherChecked ? "Others" : type.values[0],
        entityTypeName: type.otherChecked ? type.other : "",
        entityLabel: type.otherChecked ? type.other : type.values[0],
        production: MR.groupLabels(MR.readGroup(form, "production_type")),
        nib: val("nib").replace(/[\s.-]/g, ""),
        social: social ? (social.startsWith("@") ? social : MR.normalizeUrl(social)) : "",
        interest: MR.groupLabels(MR.readGroup(form, "interest")),
        picName: val("pic_name"),
        picPosition: val("pic_position"),
        picEmail: val("pic_email"),
        picPhone: val("pic_phone"),
      };
    }

    /* ---------------- REVIEW ---------------- */
    $("reviewBtn").addEventListener("click", () => {
      banner.classList.remove("is-show");
      if (!validate()) return;

      const d = collect();
      const logoSrc = state.logo ? document.querySelector("#logoDrop img").src : "";

      $("reviewContent").innerHTML =
        '<div class="mr-sum-top">' +
        (logoSrc ? '<img class="mr-sum-photo is-square" src="' + MR.esc(logoSrc) + '" alt="Logo">' : "") +
        "<div><strong>" + MR.esc(d.companyName) + "</strong><span>" + MR.esc(d.entityLabel) + "</span></div>" +
        "</div>" +
        '<dl class="mr-sum-list">' +
        MR.summaryItem("Deskripsi", MR.esc(d.description), true) +
        MR.summaryItem("Jenis Entitas", MR.esc(d.entityLabel)) +
        MR.summaryItem("NIB", d.nib ? MR.esc(d.nib) : "-") +
        MR.summaryItem("Jenis Produksi", MR.chipsHtml(d.production), true) +
        MR.summaryItem("Company Profile", MR.esc(state.profile.name) + " (" + MR.formatBytes(state.profile.size) + ")") +
        MR.summaryItem("Logo", state.logo ? MR.esc(state.logo.name) : "-") +
        MR.summaryItem("Media Sosial", d.social ? MR.esc(d.social) : "-", true) +
        MR.summaryItem("Minat", MR.chipsHtml(d.interest), true) +
        MR.summaryItem("Nama PIC", MR.esc(d.picName)) +
        MR.summaryItem("Jabatan", MR.esc(d.picPosition)) +
        MR.summaryItem("Email PIC", MR.esc(d.picEmail)) +
        MR.summaryItem("No. HP PIC", MR.esc(d.picPhone)) +
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
      fd.append("company_name", d.companyName);
      fd.append("description", d.description);
      fd.append("entity_type", d.entityType);
      fd.append("entity_type_name", d.entityTypeName);
      fd.append("production_type", d.production.join(", "));
      fd.append("nib", d.nib);
      fd.append("social_media", d.social);
      fd.append("interest", d.interest.join(", "));
      fd.append("pic_name", d.picName);
      fd.append("pic_position", d.picPosition);
      fd.append("pic_email", d.picEmail);
      fd.append("pic_phone", d.picPhone);
      fd.append("company_profile", state.profile, state.profile.name);
      if (state.logo) fd.append("logo", state.logo, state.logo.name);

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

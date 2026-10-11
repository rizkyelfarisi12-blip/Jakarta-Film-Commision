/* =========================================================
   ENTITIES FORM - STEP UI LOGIC (SHAPE ONLY)
   =========================================================
   File ini baru mengatur tampilan: pindah step/tab,
   toggle "Others" pada Entity Type, preview logo, dan nama
   file Company Profile yang dipilih.

   Load data (edit mode), upload ke server, dan save ke
   database BELUM disambungkan - menyusul setelah bentuk
   halaman ini di-review.
========================================================= */

let currentStep = "company";

document.addEventListener("DOMContentLoaded", () => {
  setupStepTabs();
  setupEntityTypeToggle();
  setupLogoPreview();
  setupCompanyProfilePreview();

  document.getElementById("saveEntityBtn").addEventListener("click", () => {
    alert(
      "Form ini belum tersambung ke database.\n\nLangkah selanjutnya: buatkan API create/update + JS save, sama seperti halaman Individual Membership.",
    );
  });
});

/* =========================================================
   STEP TABS
========================================================= */

function setupStepTabs() {
  const tabs = document.querySelectorAll(".form-step-tab");
  const nextBtn = document.getElementById("stepNextBtn");
  const prevBtn = document.getElementById("stepPrevBtn");

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => goToStep(tab.dataset.step));
  });

  nextBtn.addEventListener("click", () => {
    goToStep(currentStep === "company" ? "pic" : "company");
  });

  prevBtn.addEventListener("click", () => {
    goToStep("company");
  });
}

function goToStep(step) {
  currentStep = step;

  document.querySelectorAll(".form-step").forEach((panel) => {
    panel.style.display = panel.dataset.stepPanel === step ? "block" : "none";
  });

  document.querySelectorAll(".form-step-tab").forEach((tab) => {
    tab.classList.toggle("active", tab.dataset.step === step);
  });

  const nextBtn = document.getElementById("stepNextBtn");
  const prevBtn = document.getElementById("stepPrevBtn");

  if (step === "company") {
    prevBtn.style.visibility = "hidden";
    nextBtn.innerHTML = `Next: PIC / Contact Person <i class="ri-arrow-right-line"></i>`;
  } else {
    prevBtn.style.visibility = "visible";
    nextBtn.innerHTML = `Next: Company Data <i class="ri-arrow-right-line"></i>`;
  }

  window.scrollTo({ top: 0, behavior: "smooth" });
}

/* =========================================================
   ENTITY TYPE - CUSTOM "OTHERS"
========================================================= */

function setupEntityTypeToggle() {
  const select = document.getElementById("entity_type");
  const group = document.getElementById("customEntityTypeGroup");
  const input = document.getElementById("entity_type_name");

  select.addEventListener("change", () => {
    const isOthers = select.value === "Others";

    group.style.display = isOthers ? "block" : "none";

    if (!isOthers) input.value = "";
  });
}

/* =========================================================
   LOGO PREVIEW (LOCAL ONLY FOR NOW)
========================================================= */

function setupLogoPreview() {
  const input = document.getElementById("logoFile");
  const preview = document.getElementById("logoPreview");

  input.addEventListener("change", () => {
    const file = input.files?.[0];

    if (!file) return;

    const reader = new FileReader();

    reader.onload = (e) => {
      preview.src = e.target.result;
      preview.style.display = "block";
    };

    reader.readAsDataURL(file);
  });
}

/* =========================================================
   COMPANY PROFILE FILE NAME PREVIEW
========================================================= */

function setupCompanyProfilePreview() {
  const input = document.getElementById("companyProfileFile");
  const label = document.getElementById("companyProfileFileName");

  input.addEventListener("change", () => {
    const file = input.files?.[0];

    label.textContent = file ? file.name : "";
  });
}

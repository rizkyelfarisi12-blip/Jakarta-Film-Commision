/* ============================================
   JAKARTA FILM COMMISSION - SERVICES SCRIPT
   Expandable Service Panels
   ============================================ */

document.addEventListener("DOMContentLoaded", function () {
  // Get all service panels
  const panels = document.querySelectorAll(".service-panel");

  if (panels.length === 0) {
    console.warn(
      "No service panels found. Make sure .service-panel elements exist in HTML.",
    );
    return;
  }

  /*
  |----------------------------------------------------------------------
  | TRANSITION LOCK
  |----------------------------------------------------------------------
  |
  | Mencegah klik beruntun memicu transisi baru sebelum yang
  | sebelumnya selesai (penyebab utama "lag"/patah saat klik
  | cepat berturut-turut). Kunci dilepas begitu transisi CSS
  | panel selesai (transitionend), dengan safety-net timeout
  | kalau ternyata tidak ada transisi CSS yang terpasang / durasi
  | berubah - supaya UI tidak pernah macet permanen.
  |
  |----------------------------------------------------------------------
  */

  let isTransitioning = false;

  function activatePanel(panel) {

    // No-op kalau panel yang diklik memang sudah aktif -
    // jangan paksa reflow untuk sesuatu yang tidak berubah.
    if (panel.classList.contains("active")) {
      return;
    }

    if (isTransitioning) {
      return;
    }

    isTransitioning = true;

    panels.forEach((p) => p.classList.remove("active"));
    panel.classList.add("active");

    const release = () => {
      isTransitioning = false;
    };

    panel.addEventListener("transitionend", release, { once: true });

    // Safety net: kalau tidak ada transisi CSS (atau selector-nya
    // tidak match), jangan sampai isTransitioning macet true selamanya.
    setTimeout(release, 700);

  }

  // Add click event to each panel
  panels.forEach((panel, index) => {
    panel.addEventListener("click", function (e) {
      e.stopPropagation();

      activatePanel(this);

      console.log("Panel " + (index + 1) + " activated");
    });

    // Add keyboard support
    panel.setAttribute("tabindex", "0");
    panel.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        activatePanel(panel);
      }
    });
  });

  // Set first panel as active on load
  if (panels.length > 0) {
    panels[0].classList.add("active");
    console.log("First panel set as active");
  }

  // ============================================
  // TOUCH SUPPORT (Mobile Swipe)
  // ============================================
  let touchStartX = 0;
  const servicesExpand = document.querySelector(".services-expand");

  if (servicesExpand) {
    servicesExpand.addEventListener(
      "touchstart",
      (e) => {
        touchStartX = e.changedTouches[0].screenX;
      },
      { passive: true },
    );

    servicesExpand.addEventListener(
      "touchend",
      (e) => {
        const touchEndX = e.changedTouches[0].screenX;
        const deltaX = touchEndX - touchStartX;

        // Cuma dianggap swipe kalau geraknya cukup jauh (>50px).
        // Kalau bukan swipe (cuma tap biasa), biarkan browser
        // menangani klik-nya sendiri secara natural.
        if (Math.abs(deltaX) > 50) {

          // PENTING: preventDefault() di sini mencegah browser
          // mengirim event klik "hantu" (ghost click) susulan ke
          // elemen yang sekarang berada di posisi jari diangkat -
          // yang sudah jadi panel BEDA karena lebar panel berubah
          // begitu handleSwipe() memindahkan panel aktif.
          e.preventDefault();

          handleSwipe(deltaX);

        }
      },
      { passive: false },
    );
  }

  function handleSwipe(deltaX) {

    const activePanel = document.querySelector(".service-panel.active");
    const activeIndex = Array.from(panels).indexOf(activePanel);

    if (deltaX < 0) {
      // Swiped left - go to next panel
      const nextIndex = (activeIndex + 1) % panels.length;
      activatePanel(panels[nextIndex]);
    } else {
      // Swiped right - go to previous panel
      const prevIndex = (activeIndex - 1 + panels.length) % panels.length;
      activatePanel(panels[prevIndex]);
    }

  }

  // ============================================
  // KEYBOARD NAVIGATION (Arrow Keys)
  // ============================================
  document.addEventListener("keydown", (e) => {

    if (e.key !== "ArrowRight" && e.key !== "ArrowLeft") {
      return;
    }

    // Jangan ganggu panah kiri/kanan saat user sedang mengetik
    // di input/textarea di bagian lain halaman.
    const activeTag = document.activeElement?.tagName;

    if (
      activeTag === "INPUT" ||
      activeTag === "TEXTAREA" ||
      document.activeElement?.isContentEditable
    ) {
      return;
    }

    // Cuma aktif kalau fokus memang sedang di dalam services-expand
    // (misal user habis klik/tab ke salah satu panel).
    if (!servicesExpand || !servicesExpand.contains(document.activeElement)) {
      return;
    }

    const activePanel = document.querySelector(".service-panel.active");

    if (!activePanel) {
      return;
    }

    const activeIndex = Array.from(panels).indexOf(activePanel);

    e.preventDefault();

    if (e.key === "ArrowRight") {
      const nextIndex = (activeIndex + 1) % panels.length;
      activatePanel(panels[nextIndex]);
    } else {
      const prevIndex = (activeIndex - 1 + panels.length) % panels.length;
      activatePanel(panels[prevIndex]);
    }

  });

  // ============================================
  // PREVENT CONTENT CLICK FROM CLOSING PANEL
  // ============================================
  document.querySelectorAll(".service-content").forEach((content) => {
    content.addEventListener("click", (e) => {
      e.stopPropagation();
    });
  });

  // ============================================
  // PREVENT LINK CLICK FROM CLOSING PANEL
  // ============================================
  document.querySelectorAll(".service-content a").forEach((link) => {
    link.addEventListener("click", (e) => {
      e.stopPropagation();
      // You can add navigation logic here if needed
      // e.g., window.location.href = link.href;
    });
  });

  console.log("Service panels initialized successfully!");
});
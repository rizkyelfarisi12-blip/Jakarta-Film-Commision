async function loadComponent(id, file) {
  const element = document.getElementById(id);

  if (!element) return;

  try {
    const response = await fetch(file);

    const html = await response.text();

    element.innerHTML = html;
  } catch (error) {
    console.log(error);
  }
}

/* LOAD ALL COMPONENTS */
async function initComponents() {
  await loadComponent("loader-container", "components/loader.html");

  await loadComponent("navbar", "components/navbar.html");

  await loadComponent("footer", "components/footer.html");

  await loadComponent("whatsapp-container", "components/whatsapp.html");

  initNavbar();

  initMobileMenu();

  initLoader();

  initWhatsapp();
}

/* NAVBAR */
function initNavbar() {
  const navbar = document.querySelector(".navbar");

  if (!navbar) return;

  /* SCROLL EFFECT */

  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }
  });

  /* AUTO ACTIVE NAVBAR */

  const navLinks = document.querySelectorAll(".nav-menu a");

  let currentPage = window.location.pathname.split("/").pop();

  /* DEFAULT HOME */

  if (currentPage === "") {
    currentPage = "index.html";
  }

  navLinks.forEach((link) => {
    const linkPage = link.getAttribute("href");

    if (linkPage === currentPage) {
      link.classList.add("active");
    }
  });
}

// INIT LOADER
function initLoader() {
  const loader = document.getElementById("loader");

  if (!loader) return;

  setTimeout(() => {
    loader.classList.add("hide");
  }, 800);
}

// INIT WHATSAPP
function initWhatsapp() {
  window.addEventListener("scroll", () => {
    const widget = document.getElementById("whatsappWidget");

    if (!widget) return;

    if (window.scrollY > 300) {
      widget.classList.add("show");
    } else {
      widget.classList.remove("show");
    }
  });
}

// Mobile Init
function initMobileMenu() {
  const toggle = document.getElementById("mobileToggle");

  const menu = document.querySelector(".nav-menu");

  const overlay = document.getElementById("menuOverlay");

  if (!toggle || !menu) return;

  toggle.addEventListener("click", () => {
    toggle.classList.toggle("active");

    menu.classList.toggle("active");

    overlay.classList.toggle("active");
  });

  overlay.addEventListener("click", () => {
    menu.classList.remove("active");

    overlay.classList.remove("active");
  });
}

/* INIT */
initComponents();

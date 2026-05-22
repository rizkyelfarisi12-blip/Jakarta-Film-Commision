async function loadComponent(id, file){

  const element = document.getElementById(id);

  if(!element) return;

  try{

    const response = await fetch(file);

    const html = await response.text();

    element.innerHTML = html;

  } catch(error){

    console.log(error);

  }

}

/* LOAD ALL COMPONENTS */

async function initComponents(){

  await loadComponent("navbar", "components/navbar.html");

  await loadComponent("footer", "components/footer.html");

  initNavbar();

}

/* NAVBAR */

function initNavbar(){

  const navbar = document.querySelector('.navbar');

  if(!navbar) return;

  /* SCROLL EFFECT */

  window.addEventListener('scroll', () => {

    if(window.scrollY > 50){

      navbar.classList.add('scrolled');

    } else {

      navbar.classList.remove('scrolled');

    }

  });

  /* AUTO ACTIVE NAVBAR */

  const navLinks = document.querySelectorAll('.nav-menu a');

  let currentPage = window.location.pathname.split("/").pop();

  /* DEFAULT HOME */

  if(currentPage === ""){

    currentPage = "index.html";

  }

  navLinks.forEach(link => {

    const linkPage = link.getAttribute("href");

    if(linkPage === currentPage){

      link.classList.add("active");

    }

  });

}

/* INIT */

initComponents();
//============= ACCORDION=============
  const accordionItems =
  document.querySelectorAll('.accordion-item');
  
  accordionItems.forEach(item => {

  const header =
  item.querySelector('.accordion-header');

  if(!header) return;

  header.addEventListener('click', () => {

    const activeItem =
    document.querySelector(
      '.accordion-item.active'
    );

    if(
      activeItem &&
      activeItem !== item
    ){
      activeItem.classList.remove('active');
    }

    item.classList.toggle('active');

  });

});

/* =========================
LIMIT TEXT
========================= */
function limitText(text, limit = 120){

    if(!text) return "";

    text = text.trim();

    if(text.length <= limit){
        return text;
    }

    let shortened = text.substring(0, limit);

    shortened = shortened.substring(
        0,
        shortened.lastIndexOf(" ")
    );

    return shortened + "...";
}

/* =========================
AUTO SLUG
========================= */
function generateSlug(title){

  return title
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9\s-]/g, "")
    .replace(/\s+/g, "-");
}

//======================= HERO SLIDER SCRIPT=======================
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.dot');
  const nextBtn = document.querySelector('.next');
  const prevBtn = document.querySelector('.prev');

  let currentSlide = 0;

  function showSlide(index){

    slides.forEach(slide => {
      slide.classList.remove('active');
    });

    dots.forEach(dot => {
      dot.classList.remove('active');
    });

    if(slides[index]){
      slides[index].classList.add('active');
    }

    if(dots[index]){
      dots[index].classList.add('active');
    }

  }

  function nextSlide(){

    currentSlide++;

    if(currentSlide >= slides.length){
      currentSlide = 0;
    }
    showSlide(currentSlide);
  }

  function prevSlide(){

    currentSlide--;

    if(currentSlide < 0){
      currentSlide = slides.length - 1;
    }
    showSlide(currentSlide);
  }

  if(nextBtn){
    nextBtn.addEventListener(
      'click',
      nextSlide
    );
  }

  if(prevBtn){
    prevBtn.addEventListener(
      'click',
      prevSlide
    );
  }

  dots.forEach((dot, index) => {

    dot.addEventListener('click', () => {

      currentSlide = index;
      showSlide(currentSlide);
    });
  });

  /* AUTO SLIDE */
  if(slides.length > 0){

    setInterval(() => {
      nextSlide();
    }, 6000);
  }

  /* NAVBAR */
  const navbar = document.querySelector('.navbar');
    if(navbar){
    window.addEventListener('scroll', () => {

      if(window.scrollY > 50){
        navbar.classList.add('scrolled');

      } else {
        navbar.classList.remove('scrolled');
      }
    });
  }

  const navLinks = document.querySelectorAll('.nav-menu a');

  /* GET CURRENT PAGE */
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


  

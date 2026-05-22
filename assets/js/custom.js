
  const accordionItems = document.querySelectorAll('.accordion-item');

  accordionItems.forEach(item => {

    const header = item.querySelector('.accordion-header');

    header.addEventListener('click', () => {

      const activeItem = document.querySelector('.accordion-item.active');

      if(activeItem && activeItem !== item){
        activeItem.classList.remove('active');
      }

      item.classList.toggle('active');

    });

  });

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

    slides[index].classList.add('active');
    dots[index].classList.add('active');

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

  nextBtn.addEventListener('click', nextSlide);
  prevBtn.addEventListener('click', prevSlide);

  dots.forEach((dot, index) => {

    dot.addEventListener('click', () => {

      currentSlide = index;
      showSlide(currentSlide);

    });

  });

  /* AUTO SLIDE */

  setInterval(() => {
    nextSlide();
  }, 6000);

  /* NAVBAR */
  const navbar = document.querySelector('.navbar');

  window.addEventListener('scroll', () => {

    if(window.scrollY > 50){

      navbar.classList.add('scrolled');

    } else {

      navbar.classList.remove('scrolled');

    }

  });

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


  
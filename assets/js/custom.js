
document.addEventListener("DOMContentLoaded", () => {
  initAccordion();
  initHeroSlider();
  initScrollReveal();
  initCounters();
});

function initAccordion(){
  const accordionItems = document.querySelectorAll(".accordion-item");

  accordionItems.forEach(item => {
    const header = item.querySelector(".accordion-header");

    if(!header) return;

    header.addEventListener("click", () => {
      const activeItem = document.querySelector(".accordion-item.active");

      if(activeItem && activeItem !== item){
        activeItem.classList.remove("active");
      }

      item.classList.toggle("active");
    });
  });
}

function initHeroSlider(){
  const slides = document.querySelectorAll(".hero-slide");
  const dots = document.querySelectorAll(".dot");
  const nextBtn = document.querySelector(".next");
  const prevBtn = document.querySelector(".prev");

  if(!slides.length || !dots.length || !nextBtn || !prevBtn) return;

  let currentSlide = 0;

  function showSlide(index){
    slides.forEach(slide => {
      slide.classList.remove("active");
    });

    dots.forEach(dot => {
      dot.classList.remove("active");
    });

    slides[index].classList.add("active");
    dots[index].classList.add("active");
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

  nextBtn.addEventListener("click", nextSlide);
  prevBtn.addEventListener("click", prevSlide);

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      currentSlide = index;
      showSlide(currentSlide);
    });
  });

  setInterval(nextSlide, 6000);
}

function initScrollReveal(){
  const revealSelector = `
    .section-header,
    .core-services-left,
    .core-services-right,
    .stat-item,
    .film-card,
    .press-card,
    .vision-card,
    .organization-card,
    .contact-card,
    .event-card,
    .program-card,
    .incentive-card
  `;
  const revealTargets = new Set();
  let observer;
  let revealIndex = 0;

  if(!("IntersectionObserver" in window)){
    document.querySelectorAll(revealSelector).forEach(target => {
      target.classList.add("reveal-motion", "is-visible");
    });
    return;
  }

  observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if(entry.isIntersecting){
        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.16,
    rootMargin: "0px 0px -8% 0px"
  });

  function registerRevealTargets(root = document){
    const targets = [];

    if(root.matches && root.matches(revealSelector)){
      targets.push(root);
    }

    root.querySelectorAll(revealSelector).forEach(target => {
      targets.push(target);
    });

    targets.forEach(target => {
      if(revealTargets.has(target)) return;

      revealTargets.add(target);
      target.classList.add("reveal-motion");
      target.style.setProperty("--reveal-delay", `${Math.min(revealIndex % 6, 5) * 70}ms`);
      revealIndex++;
      observer.observe(target);
    });
  }

  registerRevealTargets();

  const mutationObserver = new MutationObserver(mutations => {
    mutations.forEach(mutation => {
      mutation.addedNodes.forEach(node => {
        if(node.nodeType === Node.ELEMENT_NODE){
          registerRevealTargets(node);
        }
      });
    });
  });

  mutationObserver.observe(document.body, {
    childList:true,
    subtree:true
  });
}

function initCounters(){
  const stats = document.querySelectorAll(".stat-item h4");

  if(!stats.length || !("IntersectionObserver" in window)) return;

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if(!entry.isIntersecting) return;

      animateCounter(entry.target);
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.7 });

  stats.forEach(stat => observer.observe(stat));
}

function animateCounter(element){
  const original = element.textContent.trim();
  const number = parseInt(original.replace(/\D/g, ""), 10);

  if(!number) return;

  const prefix = original.match(/^\D+/)?.[0] || "";
  const suffix = original.match(/\D+$/)?.[0] || "";
  const start = performance.now();
  const duration = 1000;

  function update(now){
    const progress = Math.min((now - start) / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    const value = Math.round(number * eased);

    element.textContent = `${prefix}${value}${suffix}`;

    if(progress < 1){
      requestAnimationFrame(update);
    } else {
      element.textContent = original;
    }
  }

  requestAnimationFrame(update);
}

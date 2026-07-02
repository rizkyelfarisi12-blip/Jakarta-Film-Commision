/* ============================================
   JAKARTA FILM COMMISSION - SERVICES SCRIPT
   Expandable Service Panels
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
  // Get all service panels
  const panels = document.querySelectorAll('.service-panel');

  if (panels.length === 0) {
    console.warn('No service panels found. Make sure .service-panel elements exist in HTML.');
    return;
  }

  // Add click event to each panel
  panels.forEach((panel, index) => {
    panel.addEventListener('click', function(e) {
      e.stopPropagation();
      
      // Remove active class from all panels
      panels.forEach(p => p.classList.remove('active'));
      
      // Add active class to clicked panel
      this.classList.add('active');
      
      console.log('Panel ' + (index + 1) + ' activated');
    });

    // Add keyboard support
    panel.setAttribute('tabindex', '0');
    panel.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        panel.click();
      }
    });
  });

  // Set first panel as active on load
  if (panels.length > 0) {
    panels[0].classList.add('active');
    console.log('First panel set as active');
  }

  // ============================================
  // TOUCH SUPPORT (Mobile Swipe)
  // ============================================
  let touchStartX = 0;
  let touchEndX = 0;
  const servicesExpand = document.querySelector('.services-expand');

  if (servicesExpand) {
    servicesExpand.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    }, false);

    servicesExpand.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    }, false);
  }

  function handleSwipe() {
    const activePanel = document.querySelector('.service-panel.active');
    const activeIndex = Array.from(panels).indexOf(activePanel);

    if (touchEndX < touchStartX - 50) {
      // Swiped left - go to next panel
      const nextIndex = (activeIndex + 1) % panels.length;
      panels[nextIndex].click();
    } else if (touchEndX > touchStartX + 50) {
      // Swiped right - go to previous panel
      const prevIndex = (activeIndex - 1 + panels.length) % panels.length;
      panels[prevIndex].click();
    }
  }

  // ============================================
  // KEYBOARD NAVIGATION (Arrow Keys)
  // ============================================
  document.addEventListener('keydown', (e) => {
    const activePanel = document.querySelector('.service-panel.active');
    
    if (!activePanel) return;
    
    const activeIndex = Array.from(panels).indexOf(activePanel);

    if (e.key === 'ArrowRight') {
      e.preventDefault();
      const nextIndex = (activeIndex + 1) % panels.length;
      panels[nextIndex].click();
    } else if (e.key === 'ArrowLeft') {
      e.preventDefault();
      const prevIndex = (activeIndex - 1 + panels.length) % panels.length;
      panels[prevIndex].click();
    }
  });

  // ============================================
  // PREVENT CONTENT CLICK FROM CLOSING PANEL
  // ============================================
  document.querySelectorAll('.service-content').forEach(content => {
    content.addEventListener('click', (e) => {
      e.stopPropagation();
    });
  });

  // ============================================
  // PREVENT LINK CLICK FROM CLOSING PANEL
  // ============================================
  document.querySelectorAll('.service-content a').forEach(link => {
    link.addEventListener('click', (e) => {
      e.stopPropagation();
      // You can add navigation logic here if needed
      // e.g., window.location.href = link.href;
    });
  });

  console.log('Service panels initialized successfully!');
});

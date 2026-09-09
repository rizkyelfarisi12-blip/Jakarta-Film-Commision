/* =========================================================
   JAKARTA FILM COMMISSION
   CINEMATIC EFFECTS - JS
   =========================================================

   File terpisah, tidak menyentuh script lain yang sudah ada
   (custom.js, services-script.js, dll). Cukup tambahkan:

   <script src="assets/js/cinematic-effects.js"></script>

   di index.html, setelah script lain.
========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    /* =====================================================
       SCROLL REVEAL

       Elemen dengan class "reveal" akan fade-up saat masuk
       viewport. Elemen dengan class "reveal-stagger" akan
       otomatis menandai SEMUA anak langsungnya sebagai
       "reveal" dengan delay berurutan (lihat --reveal-index
       di CSS).
    ===================================================== */

    document.querySelectorAll(".reveal-stagger").forEach(function (container) {

        Array.from(container.children).forEach(function (child, index) {

            child.style.setProperty("--reveal-index", index);
            child.classList.add("reveal");

        });

    });

    const revealElements =
        document.querySelectorAll(".reveal");

    if (revealElements.length && "IntersectionObserver" in window) {

        const revealObserver = new IntersectionObserver(
            function (entries, observer) {

                entries.forEach(function (entry) {

                    if (entry.isIntersecting) {

                        entry.target.classList.add("reveal-visible");
                        observer.unobserve(entry.target);

                    }

                });

            },
            {
                threshold: 0.15,
                rootMargin: "0px 0px -60px 0px"
            }
        );

        revealElements.forEach(function (el) {
            revealObserver.observe(el);
        });

    } else if (revealElements.length) {

        /*
        | Fallback untuk browser sangat lama tanpa
        | IntersectionObserver - langsung tampilkan saja,
        | jangan sampai konten hilang/tidak pernah muncul.
        */

        revealElements.forEach(function (el) {
            el.classList.add("reveal-visible");
        });

    }


    /* =====================================================
       NAVBAR SCROLL STATE
    ===================================================== */

    const navbar =
        document.getElementById("navbar");

    if (navbar) {

        const SCROLL_THRESHOLD = 80;

        function updateNavbarState() {

            if (window.scrollY > SCROLL_THRESHOLD) {
                navbar.classList.add("navbar-scrolled");
            } else {
                navbar.classList.remove("navbar-scrolled");
            }

        }

        // set state awal (kalau halaman di-refresh dalam kondisi sudah discroll)
        updateNavbarState();

        window.addEventListener("scroll", updateNavbarState, { passive: true });

    }

});

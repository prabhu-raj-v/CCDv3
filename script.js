document.addEventListener("DOMContentLoaded", function () {

    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const dropdowns = document.querySelectorAll('.dropdown');

    // ===========================
    // MOBILE MENU TOGGLE
    // ===========================
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();

            navLinks.classList.toggle('active');

            // Change icon
            menuToggle.textContent = navLinks.classList.contains('active') ? '✕' : '☰';

            // Close all dropdowns when menu closes
            if (!navLinks.classList.contains('active')) {
                dropdowns.forEach(d => d.classList.remove('active'));
            }
        });
    }

    // ===========================
    // MOBILE DROPDOWN FIX
    // ===========================
    dropdowns.forEach(dropdown => {

        const toggle = dropdown.querySelector('.dropdown-toggle');
        const submenu = dropdown.querySelector('.submenu');

        if (!toggle || !submenu) return;

        toggle.addEventListener('click', function (e) {

            const href = toggle.getAttribute('href');
            const isPureParent = !href || href === '#';   // MEDIA / QUICK LINKS / OTHER LINKS
            const isMobile = window.innerWidth <= 859;

            // On mobile, and for pure parent toggles, the click only opens/closes the
            // submenu — it must NOT navigate or jump to the top of the page.
            if (isMobile || isPureParent) {
                e.preventDefault();
                e.stopPropagation();
            }

            const isOpen = dropdown.classList.contains('active');

            // Close all dropdowns
            dropdowns.forEach(d => d.classList.remove('active'));

            // Open clicked one only (works on desktop AND mobile)
            if (!isOpen) {
                dropdown.classList.add('active');
            }
        });

        // Prevent submenu click from closing menu
        submenu.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });

    // ===========================
    // CLOSE MENU ON OUTSIDE CLICK
    // ===========================
    document.addEventListener('click', (e) => {
        // On desktop, close a click-opened dropdown when clicking outside the menu.
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(d => d.classList.remove('active'));
        }

        if (window.innerWidth <= 859) {
            if (!e.target.closest('nav')) {
                navLinks.classList.remove('active');
                if (menuToggle) menuToggle.textContent = '☰';
                dropdowns.forEach(d => d.classList.remove('active'));
            }
        }
    });

    // ===========================
    // SMOOTH SCROLL
    // ===========================
    const headerEl = document.querySelector("header");
    // Header is two rows tall now; offset by its full height + breathing room
    const headerOffset = () => (headerEl ? headerEl.offsetHeight + 10 : 90);

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {

            // Skip dropdown toggle click
            if (this.classList.contains('dropdown-toggle') && window.innerWidth <= 859) {
                return;
            }

            const href = this.getAttribute('href');
            const target = document.querySelector(href);

            if (target) {
                e.preventDefault();

                navLinks.classList.remove('active');
                if (menuToggle) menuToggle.textContent = '☰';

                window.scrollTo({
                    top: target.getBoundingClientRect().top + window.scrollY - headerOffset(),
                    behavior: "smooth"
                });
            }
        });
    });

    // ===========================
    // SCROLL ANIMATION
    // ===========================
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = "translateY(0)";
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.card, .news-card, .about p').forEach(el => {
        el.style.opacity = 0;
        el.style.transform = "translateY(25px)";
        el.style.transition = "0.6s";
        observer.observe(el);
    });

    // ===========================
    // HEADER SHADOW ON SCROLL
    // ===========================
    const header = document.querySelector("header");

    window.addEventListener("scroll", () => {
        if (!header) return;

        if (window.scrollY > 50) {
            header.style.boxShadow = "0 4px 20px rgba(0,0,0,0.3)";
        } else {
            header.style.boxShadow = "0 4px 12px rgba(0,0,0,0.15)";
        }
    });

    // ===========================
    // RESET ON RESIZE
    // ===========================
    window.addEventListener("resize", () => {
        if (window.innerWidth > 859) {
            navLinks?.classList.remove("active");
            if (menuToggle) menuToggle.textContent = "☰";
            dropdowns.forEach(d => d.classList.remove("active"));
        }
    });

    // ===========================
    // ACTIVE LINK ON SCROLL
    // ===========================
    const sections = document.querySelectorAll("section[id]");

    window.addEventListener("scroll", () => {
        const scrollY = window.scrollY;

        sections.forEach(section => {
            const height = section.offsetHeight;
            const top = section.getBoundingClientRect().top + window.scrollY - headerOffset();
            const id = section.getAttribute("id");

            const link = document.querySelector(`.nav-links a[href="#${id}"]`);
            if (!link) return;

            if (scrollY > top && scrollY <= top + height) {
                document.querySelectorAll(".nav-links a").forEach(a => a.classList.remove("active"));
                link.classList.add("active");
            }
        });
    });

    // ===========================
    // ACCESSIBILITY (ENTER KEY)
    // ===========================
    document.querySelectorAll(".dropdown-toggle").forEach(link => {
        link.addEventListener("keydown", e => {
            if (e.key === "Enter" && window.innerWidth <= 859) {
                e.preventDefault();
                const dropdown = link.parentElement;
                dropdown.classList.toggle("active");
            }
        });
    });

    // ===========================
    // CONSOLE MESSAGE
    // ===========================
    console.log(
        "%c NLC India Limited - Corporate Communication Department ",
        "background:#1e3a8a;color:#fff;padding:10px;font-size:16px;font-weight:bold"
    );

});

/* ===========================
   MAIN 16:9 SLIDER
=========================== */
let slideIndex = 0;

const slides = document.querySelectorAll(".slide");
const dots = document.querySelectorAll(".dot");

function showSlide(index) {

    if (index >= slides.length) {
        slideIndex = 0;
    }

    if (index < 0) {
        slideIndex = slides.length - 1;
    }

    slides.forEach((slide, i) => {

        slide.classList.remove("active");
        slide.classList.remove("previous");

        if (i < slideIndex) {
            slide.classList.add("previous");
        }

    });

    dots.forEach(dot => {
        dot.classList.remove("active");
    });


    slides[slideIndex].classList.add("active");

    dots[slideIndex].classList.add("active");
}


/* Next / Previous */

function changeSlide(direction) {

    slideIndex += direction;

    showSlide(slideIndex);
}


/* Specific slide */

function currentSlide(index) {

    slideIndex = index;

    showSlide(slideIndex);
}


/* Automatic Slide */

setInterval(() => {

    slideIndex++;

    showSlide(slideIndex);

}, 4000);


/* Initial */

showSlide(slideIndex);


/* ===========================
   PORTRAIT SLIDER (LEFT)
   =========================== */
let portraitIndex = 0;

const pSlides = document.querySelectorAll(".p-slide");
const pDots = document.querySelectorAll(".p-dot");

function showPortrait(index) {

    if (index >= pSlides.length) {
        portraitIndex = 0;
    }

    if (index < 0) {
        portraitIndex = pSlides.length - 1;
    }

    pSlides.forEach((slide, i) => {

        slide.classList.remove("active");
        slide.classList.remove("previous");

        if (i < portraitIndex) {
            slide.classList.add("previous");
        }

    });

    pDots.forEach(dot => {
        dot.classList.remove("active");
    });


    pSlides[portraitIndex].classList.add("active");

    pDots[portraitIndex].classList.add("active");
}


/* Next / Previous portrait */

function changePortrait(direction) {

    portraitIndex += direction;

    showPortrait(portraitIndex);
}


/* Specific portrait */

function currentPortrait(index) {

    portraitIndex = index;

    showPortrait(portraitIndex);
}


/* Automatic portrait slide */

setInterval(() => {

    portraitIndex++;

    showPortrait(portraitIndex);

}, 7000);


/* Initial */

showPortrait(portraitIndex);

document.addEventListener('DOMContentLoaded', () => {
  const cards = Array.from(document.querySelectorAll('.card'));
  const indicatorsContainer = document.getElementById('indicators');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const engine = document.getElementById('carouselEngine');

  let currentIndex = 0;
  const total = cards.length;
  const autoPlayInterval = 5000;
  let autoPlayTimer;

  // Build Pagination Dots
  cards.forEach((_, idx) => {
    const dot = document.createElement('div');
    dot.classList.add('dot-pill');
    if (idx === 0) dot.classList.add('active');
    dot.addEventListener('click', () => goTo(idx));
    indicatorsContainer.appendChild(dot);
  });

  const dots = Array.from(indicatorsContainer.children);

  function updateCarousel() {
    cards.forEach((card, index) => {
      card.classList.remove('active', 'prev-card', 'next-card', 'hidden');

      if (index === currentIndex) {
        card.classList.add('active');
      } else if (index === (currentIndex - 1 + total) % total) {
        card.classList.add('prev-card');
      } else if (index === (currentIndex + 1) % total) {
        card.classList.add('next-card');
      } else {
        card.classList.add('hidden');
      }
    });

    dots.forEach((dot, index) => {
      dot.classList.toggle('active', index === currentIndex);
    });
  }

  function goTo(index) {
    currentIndex = index;
    updateCarousel();
    resetAutoplay();
  }

  function next() {
    currentIndex = (currentIndex + 1) % total;
    updateCarousel();
  }

  function prev() {
    currentIndex = (currentIndex - 1 + total) % total;
    updateCarousel();
  }

  function startAutoplay() {
    autoPlayTimer = setInterval(next, autoPlayInterval);
  }

  function resetAutoplay() {
    clearInterval(autoPlayTimer);
    startAutoplay();
  }

  // Touch & Swipe Support
  let startX = 0;
  engine.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
  engine.addEventListener('touchend', e => {
    const diffX = startX - e.changedTouches[0].clientX;
    if (Math.abs(diffX) > 40) {
      if (diffX > 0) { next(); } else { prev(); }   
      resetAutoplay();
    }
  }, { passive: true });

  // Navigation Click Handlers
  nextBtn.addEventListener('click', () => { next(); resetAutoplay(); });
  prevBtn.addEventListener('click', () => { prev(); resetAutoplay(); });

  // Hover Pause Behavior
  engine.addEventListener('mouseenter', () => clearInterval(autoPlayTimer));
  engine.addEventListener('mouseleave', startAutoplay);

  // Initialize
  updateCarousel();
  startAutoplay();
});

/**
 * NLC INDIA LIMITED - GLOBAL SITE NAVIGATION
 */
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.querySelector('.menu-toggle');
  const navLinks = document.querySelector('.nav-links');
  const dropdowns = document.querySelectorAll('.dropdown');

  // Mobile Menu Toggle
  if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      navLinks.classList.toggle('active');
      menuToggle.textContent = navLinks.classList.contains('active') ? '✕' : '☰';
      if (!navLinks.classList.contains('active')) {
        dropdowns.forEach(d => d.classList.remove('active'));
      }
    });
  }

  // Dropdown Handling
  dropdowns.forEach(dropdown => {
    const toggle = dropdown.querySelector('.dropdown-toggle');
    const submenu = dropdown.querySelector('.submenu');
    if (!toggle || !submenu) return;

    toggle.addEventListener('click', function (e) {
      const href = toggle.getAttribute('href');
      const isPureParent = !href || href === '#';
      const isMobile = window.innerWidth <= 859;

      if (isMobile || isPureParent) {
        e.preventDefault();
        e.stopPropagation();
      }

      const isOpen = dropdown.classList.contains('active');
      dropdowns.forEach(d => d.classList.remove('active'));
      if (!isOpen) dropdown.classList.add('active');
    });

    submenu.addEventListener('click', e => e.stopPropagation());
  });

  // Close Menus on Click Outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
      dropdowns.forEach(d => d.classList.remove('active'));
    }
    if (window.innerWidth <= 859 && !e.target.closest('nav')) {
      navLinks?.classList.remove('active');
      if (menuToggle) menuToggle.textContent = '☰';
      dropdowns.forEach(d => d.classList.remove('active'));
    }
  });
});
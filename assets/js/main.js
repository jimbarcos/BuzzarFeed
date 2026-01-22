/*
PROGRAM NAME: Main JavaScript File (main.js)

PROGRAMMER: Frontend and Backend Team

SYSTEM CONTEXT:
This script is part of the BuzzarFeed platform.
It manages interactive features across the website, including carousel navigation, mobile menu toggling, smooth scrolling, scroll-triggered animations, and back-to-top functionality.
The file follows a modular, component-oriented approach to ensure reusability, maintainability, and responsiveness across desktop and mobile devices.
It integrates with HTML components and relies on DOM APIs, IntersectionObserver, and utility functions to enhance the user experience.

DATE CREATED: October 5, 2025
LAST MODIFIED: October 5, 2025

PURPOSE:
The purpose of this program is to provide dynamic and engaging frontend interactions for BuzzarFeed users.
It allows users to:
- Navigate featured carousel items with buttons or dots, including auto-play support.
- Open and close the mobile menu with smooth toggling and body scroll management.
- Smoothly scroll to page sections with anchor links and header offset handling.
- Trigger animations when elements enter the viewport.
- Use a back-to-top button for quick navigation.
- Utilize utility functions for debouncing, throttling, form validation, loading indicators, and toast notifications.
- Improve overall website interactivity and user engagement.

DATA STRUCTURES:
- currentSlide (number): Index of the currently visible carousel slide.
- isAnimating (boolean): Flag to prevent overlapping carousel transitions.
- DOM elements (NodeList / HTMLElement) for:
  - carousel items, dots, prev/next buttons
  - navigation menu, menu toggle, links
  - anchor links for smooth scrolling
  - elements for scroll-triggered animations
- autoPlayInterval (number): Interval ID for carousel auto-play.
- backToTopBtn (HTMLElement): Button element for scrolling back to top.

ALGORITHM / LOGIC:
1. Wait for DOMContentLoaded to initialize all interactive components:
   a. Mobile menu
   b. Carousel
   c. Smooth scrolling
   d. Scroll-triggered animations
   e. Back-to-top button
2. Mobile menu:
   a. Toggle visibility on menu button click.
   b. Close when clicking a link or outside the menu.
   c. Prevent body scrolling when open.
3. Carousel:
   a. Navigate slides with prev/next buttons and dots.
   b. Auto-play slides every 5 seconds, pause on hover.
   c. Reset current slide on window resize.
   d. Calculate slide offset based on items per slide and item width.
   e. Update dot indicators to reflect current slide.
4. Smooth scrolling:
   a. Detect clicks on anchor links.
   b. Compute target offset considering fixed header.
   c. Animate scrolling behavior to target.
5. Scroll animations:
   a. Observe elements with IntersectionObserver.
   b. Add fade-in-up class when elements enter viewport.
6. Back-to-top button:
   a. Create and append button to DOM.
   b. Show button when scrolled past threshold.
   c. Smooth scroll to top on click.
7. Utility functions:
   a. debounce(): limit frequency of function execution.
   b. throttle(): enforce minimum time between function calls.
   c. validateEmail() / validatePassword() for future form validation.
   d. showLoading() / hideLoading() for future async operations.
   e. showToast() to display notifications.
8. Development logging:
   a. Output styled console messages for branding and debugging.

NOTES:
- Responsive design is implemented for mobile, tablet, and desktop viewports.
- Future enhancements may include AJAX integration, dynamic carousels, and improved user notifications.
- Strict mode ('use strict') ensures safer JavaScript practices.
- All animations and UI effects are non-blocking to maintain smooth interactions.
*/

"use strict";

// ===============================================
// GLOBAL VARIABLES
// ===============================================

let currentSlide = 0;
let isAnimating = false;

// ===============================================
// DOM READY INITIALIZATION
// ===============================================

document.addEventListener("DOMContentLoaded", function () {
  initMobileMenu();
  initCarousel();
  initSmoothScroll();
  initScrollAnimations();
  initBackToTop();
});

// ===============================================
// MOBILE MENU FUNCTIONALITY
// ===============================================

/**
 * Initialize mobile menu toggle functionality
 */
function initMobileMenu() {
  const menuToggle = document.querySelector(".mobile-menu-toggle");
  const navMenu = document.querySelector(".nav-menu");
  const navLinks = document.querySelectorAll(".nav-link");

  if (!menuToggle || !navMenu) return;

  // Toggle menu on button click
  menuToggle.addEventListener("click", function () {
    navMenu.classList.toggle("active");
    menuToggle.classList.toggle("active");

    // Prevent body scroll when menu is open
    document.body.style.overflow = navMenu.classList.contains("active")
      ? "hidden"
      : "";
  });

  // Close menu when clicking on a nav link
  navLinks.forEach((link) => {
    link.addEventListener("click", function () {
      navMenu.classList.remove("active");
      menuToggle.classList.remove("active");
      document.body.style.overflow = "";
    });
  });

  // Close menu when clicking outside
  document.addEventListener("click", function (event) {
    if (!navMenu.contains(event.target) && !menuToggle.contains(event.target)) {
      navMenu.classList.remove("active");
      menuToggle.classList.remove("active");
      document.body.style.overflow = "";
    }
  });
}

// ===============================================
// CAROUSEL FUNCTIONALITY
// ===============================================

/**
 * Initialize carousel navigation and auto-play
 */
function initCarousel() {
  const carouselTrack = document.querySelector(".carousel-track");
  const prevBtn = document.querySelector(".carousel-prev");
  const nextBtn = document.querySelector(".carousel-next");
  const dots = document.querySelectorAll(".dot");

  if (!carouselTrack || !prevBtn || !nextBtn) return;

  const items = document.querySelectorAll(".carousel-item");
  if (items.length === 0) return;

  const totalSlides = Math.ceil(items.length / getItemsPerSlide());

  // Previous button - prevent default and stop propagation
  prevBtn.addEventListener("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (isAnimating) return;
    navigateCarousel("prev");
  });

  // Next button - prevent default and stop propagation
  nextBtn.addEventListener("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (isAnimating) return;
    navigateCarousel("next");
  });

  // Dots navigation
  dots.forEach((dot, index) => {
    dot.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      if (isAnimating) return;
      currentSlide = index;
      updateCarousel();
    });
  });

  // Auto-play carousel
  let autoPlayInterval = setInterval(() => {
    navigateCarousel("next");
  }, 5000);

  // Pause auto-play on hover
  carouselTrack.addEventListener("mouseenter", function () {
    clearInterval(autoPlayInterval);
  });

  carouselTrack.addEventListener("mouseleave", function () {
    autoPlayInterval = setInterval(() => {
      navigateCarousel("next");
    }, 5000);
  });

  // Handle window resize
  let resizeTimeout;
  window.addEventListener("resize", function () {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      currentSlide = 0; // Reset to first slide on resize
      updateCarousel();
    }, 250);
  });

  // Initialize carousel position
  updateCarousel();
}

/**
 * Navigate carousel in specified direction
 * @param {string} direction - 'prev' or 'next'
 */
function navigateCarousel(direction) {
  const items = document.querySelectorAll(".carousel-item");
  const totalSlides = Math.ceil(items.length / getItemsPerSlide());

  if (direction === "next") {
    currentSlide = (currentSlide + 1) % totalSlides;
  } else {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
  }

  updateCarousel();
}

/**
 * Update carousel position and active indicators
 */
function updateCarousel() {
  const carouselTrack = document.querySelector(".carousel-track");
  const dots = document.querySelectorAll(".dot");
  const items = document.querySelectorAll(".carousel-item");

  if (!carouselTrack || items.length === 0) return;

  isAnimating = true;

  const itemsPerSlide = getItemsPerSlide();
  const itemWidth = 250; // Width of each item
  const gap = 48; // Gap between items (3rem = 48px)

  const offset = currentSlide * (itemWidth + gap) * itemsPerSlide;
  carouselTrack.style.transform = `translateX(-${offset}px)`;

  // Update dots
  dots.forEach((dot, index) => {
    dot.classList.toggle("active", index === currentSlide);
  });

  setTimeout(() => {
    isAnimating = false;
  }, 300);
}

/**
 * Get number of items to show per slide based on viewport width
 * @returns {number} Items per slide
 */
function getItemsPerSlide() {
  const width = window.innerWidth;
  if (width < 768) return 1;
  if (width < 1024) return 2;
  return 3;
}

// ===============================================
// SMOOTH SCROLLING
// ===============================================

/**
 * Initialize smooth scrolling for anchor links
 */
function initSmoothScroll() {
  const links = document.querySelectorAll('a[href^="#"]');

  links.forEach((link) => {
    link.addEventListener("click", function (e) {
      const href = this.getAttribute("href");

      // Skip empty anchors
      if (href === "#" || href === "#!") return;

      const target = document.querySelector(href);

      if (target) {
        e.preventDefault();

        const headerOffset = 80;
        const elementPosition = target.getBoundingClientRect().top;
        const offsetPosition =
          elementPosition + window.pageYOffset - headerOffset;

        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth",
        });
      }
    });
  });
}

// ===============================================
// SCROLL ANIMATIONS
// ===============================================

/**
 * Initialize scroll-triggered animations
 */
function initScrollAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("fade-in-up");
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  // Observe elements for animation
  const animatedElements = document.querySelectorAll(
    ".stall-card, .review-card, .join-card, .section-title"
  );

  animatedElements.forEach((element) => {
    observer.observe(element);
  });
}

// ===============================================
// BACK TO TOP BUTTON
// ===============================================

/**
 * Initialize back to top button functionality
 */
function initBackToTop() {
  // Create back to top button
  const backToTopBtn = document.createElement("button");
  backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
  backToTopBtn.className = "back-to-top";
  backToTopBtn.setAttribute("aria-label", "Back to top");
  document.body.appendChild(backToTopBtn);

  // Show/hide button based on scroll position
  window.addEventListener("scroll", function () {
    if (window.pageYOffset > 300) {
      backToTopBtn.classList.add("visible");
    } else {
      backToTopBtn.classList.remove("visible");
    }
  });

  // Scroll to top on click
  backToTopBtn.addEventListener("click", function () {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
}

// ===============================================
// UTILITY FUNCTIONS
// ===============================================

/**
 * Debounce function to limit function execution rate
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Throttle function to limit function execution rate
 * @param {Function} func - Function to throttle
 * @param {number} limit - Time limit in milliseconds
 * @returns {Function} Throttled function
 */
function throttle(func, limit) {
  let inThrottle;
  return function (...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(() => (inThrottle = false), limit);
    }
  };
}

// ===============================================
// FORM VALIDATION (FOR FUTURE USE)
// ===============================================

/**
 * Validate email format
 * @param {string} email - Email address to validate
 * @returns {boolean} True if valid
 */
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

/**
 * Validate password strength
 * @param {string} password - Password to validate
 * @returns {object} Validation result with isValid and message
 */
function validatePassword(password) {
  const minLength = 8;
  const hasUpperCase = /[A-Z]/.test(password);
  const hasLowerCase = /[a-z]/.test(password);
  const hasNumber = /[0-9]/.test(password);

  if (password.length < minLength) {
    return {
      isValid: false,
      message: `Password must be at least ${minLength} characters long`,
    };
  }

  if (!hasUpperCase) {
    return {
      isValid: false,
      message: "Password must contain at least one uppercase letter",
    };
  }

  if (!hasLowerCase) {
    return {
      isValid: false,
      message: "Password must contain at least one lowercase letter",
    };
  }

  if (!hasNumber) {
    return {
      isValid: false,
      message: "Password must contain at least one number",
    };
  }

  return {
    isValid: true,
    message: "Password is strong",
  };
}

// ===============================================
// LOADING INDICATOR (FOR FUTURE USE)
// ===============================================

/**
 * Show loading indicator
 */
function showLoading() {
  const loader = document.createElement("div");
  loader.className = "loading-overlay";
  loader.innerHTML = '<div class="spinner"></div>';
  document.body.appendChild(loader);
}

/**
 * Hide loading indicator
 */
function hideLoading() {
  const loader = document.querySelector(".loading-overlay");
  if (loader) {
    loader.remove();
  }
}

// ===============================================
// TOAST NOTIFICATIONS (FOR FUTURE USE)
// ===============================================

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of toast (success, error, info)
 */
function showToast(message, type = "info") {
  const toast = document.createElement("div");
  toast.className = `toast toast-${type}`;
  toast.textContent = message;

  document.body.appendChild(toast);

  // Trigger animation
  setTimeout(() => toast.classList.add("show"), 100);

  // Remove after 3 seconds
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ===============================================
// CONSOLE LOG (DEVELOPMENT ONLY)
// ===============================================

console.log(
  "%c BuzzarFeed ",
  "background: #4A8B4F; color: white; font-size: 16px; font-weight: bold; padding: 8px;"
);
console.log("%c Welcome to BuzzarFeed! ", "color: #E8663E; font-size: 14px;");
console.log("Discover the flavors of BGC Night Market 🍔🍕🍜");

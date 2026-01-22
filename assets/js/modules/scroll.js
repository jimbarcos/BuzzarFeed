/*
PROGRAM NAME: Scroll Module (scroll.js)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform and manages smooth scrolling, scroll-triggered animations, and the back-to-top button functionality.
It is designed as a reusable ES6 class that can be imported into the main application or other modules.
The module follows a component-based architecture and ISO 9241 principles to ensure modularity, maintainability, and consistency in UX behavior.

DATE CREATED: December 4, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to enhance the user experience by:
- Enabling smooth scrolling for anchor links
- Animating elements as they enter the viewport
- Providing a back-to-top button for easy navigation

DATA STRUCTURES:
- ScrollManager (class): Main class encapsulating scroll-related functionality
  - init(): Initializes all scroll behaviors
  - initSmoothScroll(): Adds smooth scrolling to anchor links
  - initScrollAnimations(): Observes elements and triggers animations on intersection
  - initBackToTop(): Creates and manages a back-to-top button
- DOM Elements: Anchor links, elements with classes `.stall-card`, `.review-card`, `.join-card`, `.section-title`, and dynamically created back-to-top button

ALGORITHM / LOGIC:
1. On instantiation, call init() to set up all scroll behaviors.
2. Smooth Scroll:
   a. Select all anchor links starting with '#'.
   b. Add click event listeners to scroll smoothly to target elements, adjusting for a fixed header offset.
3. Scroll Animations:
   a. Use IntersectionObserver to detect when animated elements enter the viewport.
   b. Add 'fade-in-up' class and stop observing once triggered.
4. Back-to-Top Button:
   a. Dynamically create a button element and append it to the DOM.
   b. Toggle visibility based on scroll position.
   c. Scroll smoothly to top when clicked.

NOTES:
- The module does not rely on external libraries for scrolling or animations.
- All dynamically created elements are appended to the document body.
- IntersectionObserver is used for efficient, performant animations.
- Header offset can be adjusted if the fixed header height changes.
- Future enhancements may include configurable animation classes, dynamic offsets, and customizable back-to-top button styling.
*/

export class ScrollManager {
  constructor() {
    this.init();
  }

  init() {
    this.initSmoothScroll();
    this.initScrollAnimations();
    this.initBackToTop();
  }

  initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');

    links.forEach((link) => {
      link.addEventListener("click", (e) => {
        const href = link.getAttribute("href");

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

  initScrollAnimations() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("fade-in-up");
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    const animatedElements = document.querySelectorAll(
      ".stall-card, .review-card, .join-card, .section-title"
    );

    animatedElements.forEach((element) => {
      observer.observe(element);
    });
  }

  initBackToTop() {
    const backToTopBtn = document.createElement("button");
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopBtn.className = "back-to-top";
    backToTopBtn.setAttribute("aria-label", "Back to top");
    document.body.appendChild(backToTopBtn);

    // Show/hide button based on scroll position
    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 300) {
        backToTopBtn.classList.add("visible");
      } else {
        backToTopBtn.classList.remove("visible");
      }
    });

    // Scroll to top on click
    backToTopBtn.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }
}

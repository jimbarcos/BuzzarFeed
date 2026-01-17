/*
PROGRAM NAME: Carousel Module (carousel.js)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform and handles carousel functionality for displaying items such as featured stalls, brands, or promotional content.
It is implemented as a reusable ES6 class that can be instantiated on any container element with carousel items.
The module follows ISO 9241 principles of modularity and maintainability, supporting auto-play, navigation buttons, dots indicators, responsive layout, and smooth transitions.

DATE CREATED: December 4, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to provide a dynamic, interactive carousel component that allows:
- Sliding through multiple items with previous/next buttons
- Navigation using clickable dots
- Automatic sliding with configurable interval (auto-play)
- Pause on hover functionality
- Responsive display adapting items per slide based on viewport width
- Smooth CSS transitions for enhanced user experience

DATA STRUCTURES:
- container (DOM element): Main carousel container
- track (DOM element): Carousel track containing items
- items (NodeList): Individual carousel items
- prevBtn, nextBtn (DOM elements): Navigation buttons
- dots (NodeList): Dot indicators for slides
- options (object): Configuration options such as autoPlay, autoPlayInterval, itemsPerSlide
- currentSlide (number): Index of the currently visible slide
- totalSlides (number): Computed total number of slides
- isAnimating (boolean): Flag to prevent overlapping animations
- autoPlayTimer (number): Interval ID for auto-play functionality

ALGORITHM / LOGIC:
1. Initialize carousel by selecting container, track, items, buttons, and dots.
2. Calculate total slides based on itemsPerSlide.
3. Bind event listeners:
   a. Previous/Next buttons navigate carousel.
   b. Dot buttons navigate to specific slides.
   c. Mouse enter/leave events pause and resume auto-play.
   d. Window resize event recalculates items per slide and resets carousel position.
4. Implement navigation logic for 'prev' and 'next' directions, updating currentSlide.
5. Update carousel transform and active dot states.
6. Implement auto-play functionality with configurable interval.
7. Debounce resize events to prevent excessive recalculation.
8. Ensure smooth sliding transitions and prevent multiple overlapping animations.

NOTES:
- Default item width is 250px and gap is 48px; can be adapted via CSS.
- Items per slide are responsive: 1 item (<768px), 2 items (<1024px), 3 items (>=1024px).
- Debounce function is used for efficient resize handling.
- Module is fully reusable and supports multiple instances on the same page.
- Future enhancements may include touch/swipe gestures or lazy-loaded images.
*/

export class Carousel {
  constructor(selector, options = {}) {
    this.container = document.querySelector(selector);
    if (!this.container) return;

    this.options = {
      autoPlay: true,
      autoPlayInterval: 5000,
      itemsPerSlide: this.getItemsPerSlide(),
      ...options,
    };

    this.currentSlide = 0;
    this.isAnimating = false;
    this.autoPlayTimer = null;

    this.init();
  }

  init() {
    this.track = this.container.querySelector(".carousel-track");
    this.prevBtn = this.container.querySelector(".carousel-prev");
    this.nextBtn = this.container.querySelector(".carousel-next");
    this.dots = this.container.querySelectorAll(".dot");
    this.items = this.container.querySelectorAll(".carousel-item");

    if (!this.track || this.items.length === 0) return;

    this.totalSlides = Math.ceil(
      this.items.length / this.options.itemsPerSlide
    );

    this.bindEvents();
    this.updateCarousel();

    if (this.options.autoPlay) {
      this.startAutoPlay();
    }
  }

  bindEvents() {
    // Previous button
    if (this.prevBtn) {
      this.prevBtn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (!this.isAnimating) {
          this.navigate("prev");
        }
      });
    }

    // Next button
    if (this.nextBtn) {
      this.nextBtn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (!this.isAnimating) {
          this.navigate("next");
        }
      });
    }

    // Dots navigation
    this.dots.forEach((dot, index) => {
      dot.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (!this.isAnimating) {
          this.currentSlide = index;
          this.updateCarousel();
        }
      });
    });

    // Pause on hover
    this.track.addEventListener("mouseenter", () => this.stopAutoPlay());
    this.track.addEventListener("mouseleave", () => {
      if (this.options.autoPlay) {
        this.startAutoPlay();
      }
    });

    // Handle resize
    window.addEventListener(
      "resize",
      this.debounce(() => {
        this.options.itemsPerSlide = this.getItemsPerSlide();
        this.currentSlide = 0;
        this.updateCarousel();
      }, 250)
    );
  }

  navigate(direction) {
    if (direction === "next") {
      this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
    } else {
      this.currentSlide =
        (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
    }
    this.updateCarousel();
  }

  updateCarousel() {
    this.isAnimating = true;

    const itemWidth = 250;
    const gap = 48;
    const offset =
      this.currentSlide * (itemWidth + gap) * this.options.itemsPerSlide;

    this.track.style.transform = `translateX(-${offset}px)`;

    // Update dots
    this.dots.forEach((dot, index) => {
      dot.classList.toggle("active", index === this.currentSlide);
    });

    setTimeout(() => {
      this.isAnimating = false;
    }, 300);
  }

  getItemsPerSlide() {
    const width = window.innerWidth;
    if (width < 768) return 1;
    if (width < 1024) return 2;
    return 3;
  }

  startAutoPlay() {
    this.stopAutoPlay();
    this.autoPlayTimer = setInterval(() => {
      this.navigate("next");
    }, this.options.autoPlayInterval);
  }

  stopAutoPlay() {
    if (this.autoPlayTimer) {
      clearInterval(this.autoPlayTimer);
      this.autoPlayTimer = null;
    }
  }

  debounce(func, wait) {
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
}

/**
 * BuzzarFeed - Carousel Module
 *
 * Carousel functionality module
 * Following ISO 9241: Modularity and Maintainability
 *
 * @package BuzzarFeed
 * @version 2.0
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
}

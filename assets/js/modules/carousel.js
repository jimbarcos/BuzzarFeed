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
}

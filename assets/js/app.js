/*
PROGRAM NAME: Main Application (BuzzarFeedApp.js)

PROGRAMMER: Frontend and Backend Team

SYSTEM CONTEXT:
This script serves as the main entry point for the BuzzarFeed platform using ES6 modules.
It orchestrates key interactive components including navigation, carousel, and scroll-based animations.
The application follows a modular architecture (ISO 9241: Modularity and Maintainability) to allow independent development, testing, and reuse of submodules such as Carousel, Navigation, ScrollManager, and Utils.
It integrates seamlessly with DOM elements and external modules to provide a dynamic, responsive, and engaging user interface.

DATE CREATED: December 4, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to initialize and manage core frontend functionality of BuzzarFeed:
- Navigation menu interactions and mobile responsiveness.
- Brand/featured carousels with autoplay and manual navigation.
- Scroll-triggered behaviors and smooth scrolling.
- Utility functions for reusable tasks (debouncing, throttling, validation, etc.).
- Logging of initialization messages to the console for developer feedback.

DATA STRUCTURES:
- this.navigation (Navigation instance): Handles menu toggling, link behavior, and mobile support.
- this.carousel (Carousel instance): Manages carousel slides, auto-play, and navigation controls.
- this.scrollManager (ScrollManager instance): Handles scroll-based animations and back-to-top functionality.
- Utils (class): Provides helper functions for DOM manipulation, validation, and other reusable utilities.
- DOM Elements: Query selectors for carousel and other interactive components.
- app (BuzzarFeedApp instance): Main application instance exported for external access or testing.

ALGORITHM / LOGIC:
1. Check if DOM is ready; defer initialization until ready.
2. Instantiate BuzzarFeedApp:
   a. Initialize navigation menu via Navigation module.
   b. Initialize carousel if element exists, with configurable auto-play interval.
   c. Initialize ScrollManager to handle animations and scroll-related features.
   d. Log welcome message to the console.
3. Export app instance for external modules or testing purposes.

NOTES:
- The file uses ES6 class syntax and module imports for modularity.
- Each feature is encapsulated in its own module to improve maintainability.
- Future enhancements may include dynamic data fetching for carousels or user-specific behavior.
- Logging is intended for development; production builds may remove or silence console outputs.
*/

import { Carousel } from "./modules/carousel.js";
import { Navigation } from "./modules/navigation.js";
import { ScrollManager } from "./modules/scroll.js";
import { Utils } from "./modules/utils.js";

class BuzzarFeedApp {
  constructor() {
    this.init();
  }

  init() {
    // Wait for DOM to be ready
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", () => this.onReady());
    } else {
      this.onReady();
    }
  }

  onReady() {
    // Initialize navigation
    this.navigation = new Navigation();

    // Initialize carousel
    const carouselElement = document.querySelector(".brands-carousel");
    if (carouselElement) {
      this.carousel = new Carousel(".brands-carousel", {
        autoPlay: true,
        autoPlayInterval: 5000,
      });
    }

    // Initialize scroll manager
    this.scrollManager = new ScrollManager();

    // Log initialization
    this.logWelcome();
  }

  logWelcome() {
    console.log(
      "%c BuzzarFeed ",
      "background: #4A8B4F; color: white; font-size: 16px; font-weight: bold; padding: 8px;"
    );
    console.log(
      "%c Welcome to BuzzarFeed! ",
      "color: #E8663E; font-size: 14px;"
    );
    console.log("Discover the flavors of BGC Night Market 🍔🍕🍜");
  }
}

// Initialize application
const app = new BuzzarFeedApp();

// Export for testing and external access
export default app;

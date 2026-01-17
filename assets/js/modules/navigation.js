/*
PROGRAM NAME: Navigation Module (navigation.js)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform and manages mobile menu interactions and navigation behavior.
It is implemented as a reusable ES6 class and can be imported into the main application or other frontend modules.
The module adheres to ISO 9241 principles for modularity, maintainability, and consistent user experience.

DATE CREATED: December 4, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to provide intuitive mobile and responsive navigation functionality:
- Toggle the mobile menu on button click
- Close the menu when a navigation link is clicked
- Close the menu when clicking outside of the menu
- Prevent body scrolling when the menu is open

DATA STRUCTURES:
- Navigation (class): Encapsulates all navigation functionality
  - menuToggle: DOM element for the mobile menu toggle button
  - navMenu: DOM element representing the navigation menu
  - navLinks: NodeList of navigation link elements
  - init(): Initializes DOM references and binds events
  - bindEvents(): Attaches event listeners for menu interactions
  - toggleMenu(): Toggles menu visibility and body scroll
  - closeMenu(): Closes menu and restores body scroll

ALGORITHM / LOGIC:
1. On instantiation, call init() to select necessary DOM elements.
2. Verify that menuToggle and navMenu exist; exit if not found.
3. Bind events:
   a. Click on menu toggle button calls toggleMenu().
   b. Click on any nav link calls closeMenu().
   c. Click outside menu and toggle button calls closeMenu().
4. toggleMenu():
   a. Toggle 'active' class on navMenu and menuToggle.
   b. Prevent or restore body scroll based on menu state.
5. closeMenu():
   a. Remove 'active' class from navMenu and menuToggle.
   b. Restore body scroll.

NOTES:
- The module does not rely on external libraries.
- All event listeners are bound during initialization for efficiency.
- Body scroll is disabled when the mobile menu is open to prevent background scrolling.
- Future enhancements may include submenus, animated transitions, and configurable CSS classes.
*/

export class Navigation {
  constructor() {
    this.init();
  }

  init() {
    this.menuToggle = document.querySelector(".mobile-menu-toggle");
    this.navMenu = document.querySelector(".nav-menu");
    this.navLinks = document.querySelectorAll(".nav-link");

    if (!this.menuToggle || !this.navMenu) return;

    this.bindEvents();
  }

  bindEvents() {
    // Toggle menu on button click
    this.menuToggle.addEventListener("click", () => {
      this.toggleMenu();
    });

    // Close menu when clicking on a nav link
    this.navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        this.closeMenu();
      });
    });

    // Close menu when clicking outside
    document.addEventListener("click", (event) => {
      if (
        !this.navMenu.contains(event.target) &&
        !this.menuToggle.contains(event.target)
      ) {
        this.closeMenu();
      }
    });
  }

  toggleMenu() {
    this.navMenu.classList.toggle("active");
    this.menuToggle.classList.toggle("active");
    document.body.style.overflow = this.navMenu.classList.contains("active")
      ? "hidden"
      : "";
  }

  closeMenu() {
    this.navMenu.classList.remove("active");
    this.menuToggle.classList.remove("active");
    document.body.style.overflow = "";
  }
}

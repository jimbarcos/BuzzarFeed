/*
PROGRAM NAME: User Dropdown Module (userDropdown.js)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform and manages the user account dropdown menu interactions.
It is implemented as an immediately-invoked function expression (IIFE) to encapsulate its scope and prevent global namespace pollution.
The module ensures consistent dropdown behavior for desktop and mobile navigation within the platform.

DATE CREATED: December 4, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to provide intuitive user dropdown menu functionality:
- Toggle the dropdown menu when the user clicks the account button
- Close the dropdown when clicking outside the menu
- Prevent closing when interacting with the dropdown content
- Close the dropdown when pressing the Escape key

DATA STRUCTURES:
- dropdownBtn (DOM element): Button that triggers the dropdown
- dropdownMenu (DOM element): Menu that is toggled
- initUserDropdown(): Initializes event listeners and DOM references
- IIFE scope ensures no pollution of the global namespace

ALGORITHM / LOGIC:
1. On DOM ready, select dropdownBtn and dropdownMenu.
2. Exit early if either element is not found.
3. Bind events:
   a. Click on dropdownBtn toggles 'show' class on dropdownMenu and 'active' class on dropdownBtn.
   b. Click outside dropdown closes menu and resets button state.
   c. Click inside dropdown prevents propagation to avoid closing.
   d. Pressing Escape key closes dropdown if open.
4. Initialize the dropdown functionality immediately or after DOMContentLoaded.

NOTES:
- The module uses vanilla JavaScript with no external dependencies.
- The dropdown state is controlled via CSS classes ('show' and 'active').
- Future enhancements could include animations, keyboard navigation, or configurable menu targets.
*/

// User dropdown functionality
(function () {
  "use strict";

  // Initialize dropdown when DOM is ready
  function initUserDropdown() {
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdownMenu = document.getElementById("userDropdownMenu");

    if (!dropdownBtn || !dropdownMenu) {
      return;
    }

    // Toggle dropdown
    dropdownBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      dropdownMenu.classList.toggle("show");
      dropdownBtn.classList.toggle("active");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (e) {
      if (!dropdownMenu.contains(e.target) && !dropdownBtn.contains(e.target)) {
        dropdownMenu.classList.remove("show");
        dropdownBtn.classList.remove("active");
      }
    });

    // Prevent dropdown from closing when clicking inside
    dropdownMenu.addEventListener("click", function (e) {
      e.stopPropagation();
    });

    // Close dropdown on escape key
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && dropdownMenu.classList.contains("show")) {
        dropdownMenu.classList.remove("show");
        dropdownBtn.classList.remove("active");
      }
    });
  }

  // Initialize on DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initUserDropdown);
  } else {
    initUserDropdown();
  }
})();

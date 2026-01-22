/*
PROGRAM NAME: Utilities Module (utils.js)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform and provides reusable utility functions for common frontend tasks.
It supports modular development by encapsulating debounce, throttle, validation, loading indicators, toast notifications, and modal toggling.
The module is designed to be imported into other application components or accessed globally for backward compatibility.
It follows ISO 9241 principles emphasizing reusability, maintainability, and consistency across the platform.

DATE CREATED: December 4, 2025
LAST MODIFIED: December 4, 2025

PURPOSE:
The purpose of this program is to provide a centralized set of utilities for:
- Limiting execution rates (debounce and throttle)
- Validating user input (email format and password strength)
- Displaying UI feedback (loading overlays and toast notifications)
- Toggling password visibility
- Managing modal display

DATA STRUCTURES:
- Utils (class): Contains static methods for reusable utilities
  - debounce(func, wait)
  - throttle(func, limit)
  - validateEmail(email)
  - validatePassword(password)
  - showLoading()
  - hideLoading()
  - showToast(message, type)
- window.togglePassword (function): Toggle password field visibility
- window.closeModal / window.openModal (functions): Handle modal show/hide behavior
- DOM Elements: Targeted via IDs and CSS classes for interactivity
- Toast and loading overlay elements dynamically appended to the DOM

ALGORITHM / LOGIC:
1. Provide class-based static methods for general utility functions.
2. Debounce and throttle functions limit the rate of function execution.
3. Input validation methods verify email formats and password strength rules.
4. Loading overlay methods manage a global spinner display during asynchronous tasks.
5. Toast notification method dynamically creates, displays, and removes feedback messages.
6. Global functions for backward compatibility handle modal visibility and password toggling.
7. All DOM manipulation is safely performed with existence checks to prevent errors.

NOTES:
- The module is designed for both import via ES6 modules and global browser access.
- Toast and loading elements are automatically removed to avoid DOM clutter.
- Password toggling and modals are tied to HTML IDs for simplicity.
- Future enhancements may include customizable toast styles, multiple concurrent loaders, and integration with frontend frameworks.
*/

export class Utils {
  /**
   * Debounce function to limit function execution rate
   */
  static debounce(func, wait) {
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
   */
  static throttle(func, limit) {
    let inThrottle;
    return function (...args) {
      if (!inThrottle) {
        func.apply(this, args);
        inThrottle = true;
        setTimeout(() => (inThrottle = false), limit);
      }
    };
  }

  /**
   * Validate email format
   */
  static validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  /**
   * Validate password strength
   */
  static validatePassword(password) {
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

  /**
   * Show loading indicator
   */
  static showLoading() {
    const loader = document.createElement("div");
    loader.className = "loading-overlay";
    loader.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(loader);
  }

  /**
   * Hide loading indicator
   */
  static hideLoading() {
    const loader = document.querySelector(".loading-overlay");
    if (loader) {
      loader.remove();
    }
  }

  /**
   * Show toast notification
   */
  static showToast(message, type = "info") {
    const toast = document.createElement("div");
    toast.className = `toast toast-${type}`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 100);

    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }
}

// Global utility functions for backward compatibility
window.togglePassword = function (fieldId) {
  const field = document.getElementById(fieldId);
  const icon = document.getElementById(fieldId + "-icon");
  if (field.type === "password") {
    field.type = "text";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  } else {
    field.type = "password";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  }
};

window.closeModal = function (modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("show");
  }
};

window.openModal = function (modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add("show");
  }
};

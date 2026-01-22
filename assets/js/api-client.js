/*
PROGRAM NAME: API Client Module (api-client.js)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is the JavaScript API client for the BuzzarFeed platform.
It provides a centralized interface for making HTTP requests to the backend API from the browser.
The ApiClient class encapsulates all API communication logic, standardizes request/response handling,
and provides convenient methods for interacting with various API endpoints including authentication,
users, stalls, reviews, applications, amendments, closures, and admin functions.

This client is used throughout the frontend application to communicate with the server-side API
and is designed to work with the RESTful API architecture of BuzzarFeed.

DATE CREATED: January 23, 2026
LAST MODIFIED: January 23, 2026

PURPOSE:
The purpose of this program is to provide a clean, reusable, and maintainable interface for frontend
code to interact with the BuzzarFeed API. It abstracts away the complexities of HTTP requests,
error handling, and data formatting, allowing developers to make API calls with simple method
invocations. By centralizing API logic, this module ensures consistency across the application
and simplifies future API changes.

DATA STRUCTURES:
- ApiClient class:
  - baseUrl (string): Base URL for API endpoints (default: '/api').
- Request configuration objects:
  - method (string): HTTP method (GET, POST, PUT, DELETE).
  - headers (object): HTTP headers including Content-Type.
  - body (string): JSON-stringified request data.
- Response objects:
  - success (boolean): Indicates if request was successful.
  - message (string): Response message.
  - data (any): Response payload.
  - errors (array): Validation or error details.
- Query parameters:
  - params (object): Key-value pairs for URL query strings.
  - page (number): Pagination page number.
  - limit (number): Items per page.
  - search (string): Search query.
  - category (string): Filter by category.

ALGORITHM / LOGIC:
1. Initialize ApiClient with optional base URL.
2. Provide generic request method:
   a. Construct full URL by appending endpoint to base URL.
   b. Configure fetch options with method, headers, and body.
   c. Add JSON body for non-GET requests.
   d. Make fetch request to server.
   e. Parse JSON response.
   f. Check response status and throw error if not OK.
   g. Return result or throw error.
3. Provide authentication methods:
   a. login: POST credentials to /auth/login.
   b. register: POST user data to /auth/register.
   c. logout: POST to /auth/logout.
   d. checkAuth: GET authentication status from /auth/check.
   e. forgotPassword: POST email to /auth/forgot-password.
   f. resetPassword: POST token and password to /auth/reset-password.
4. Provide user management methods:
   a. getProfile: GET current user profile.
   b. updateProfile: PUT updated profile data.
   c. changePassword: PUT password change request.
   d. getUsers: GET paginated list of users.
5. Provide stall methods:
   a. getStalls: GET stalls with filtering and pagination.
   b. getStall: GET single stall details.
   c. getStallMenu: GET menu items for a stall.
   d. getStallReviews: GET reviews for a stall.
   e. createStall, updateStall, deleteStall: Manage stall data.
6. Provide review methods:
   a. getReviews: GET reviews with pagination.
   b. createReview, updateReview, deleteReview: Manage reviews.
   c. reactToReview: POST reaction to a review.
   d. reportReview: POST report for inappropriate review.
7. Provide application, amendment, and closure methods for admin workflows.
8. Provide admin methods for dashboard stats, logs, and reports.
9. Create global API instance for convenient access.
10. Export for CommonJS module systems.

NOTES:
- All requests use the Fetch API for modern browser compatibility.
- JSON is the standard data format for requests and responses.
- Error handling logs errors to console and re-throws for caller handling.
- Query parameters are automatically encoded using URLSearchParams.
- The global 'api' instance allows direct usage without instantiation.
- This client assumes the API follows RESTful conventions.
- Authentication state is managed server-side via sessions.
- Future enhancements may include:
  - Request interceptors for token injection
  - Response interceptors for global error handling
  - Request cancellation support
  - Automatic retry logic for failed requests
  - Request caching mechanisms
  - Progress tracking for file uploads
*/

class ApiClient {
    constructor(baseUrl = '/api') {
        this.baseUrl = baseUrl;
    }

    /**
     * Make an API request
     * @param {string} endpoint - API endpoint
     * @param {string} method - HTTP method
     * @param {object|null} data - Request data
     * @param {object} options - Additional fetch options
     * @returns {Promise<object>}
     */
    async request(endpoint, method = 'GET', data = null, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        
        const config = {
            method,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        };

        if (data && method !== 'GET') {
            config.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, config);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Request failed');
            }

            return result;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Authentication methods
    async login(email, password) {
        return this.request('/auth/login', 'POST', { email, password });
    }

    async register(userData) {
        return this.request('/auth/register', 'POST', userData);
    }

    async logout() {
        return this.request('/auth/logout', 'POST');
    }

    async checkAuth() {
        return this.request('/auth/check');
    }

    async forgotPassword(email) {
        return this.request('/auth/forgot-password', 'POST', { email });
    }

    async resetPassword(token, password) {
        return this.request('/auth/reset-password', 'POST', { token, password });
    }

    // User methods
    async getProfile() {
        return this.request('/users/profile');
    }

    async updateProfile(data) {
        return this.request('/users/profile', 'PUT', data);
    }

    async changePassword(currentPassword, newPassword) {
        return this.request('/users/password', 'PUT', {
            current_password: currentPassword,
            new_password: newPassword
        });
    }

    async getUsers(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/users?${query}`);
    }

    // Stall methods
    async getStalls(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/stalls?${query}`);
    }

    async getStall(id) {
        return this.request(`/stalls/${id}`);
    }

    async getStallMenu(id) {
        return this.request(`/stalls/${id}/menu`);
    }

    async getStallReviews(id, params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/stalls/${id}/reviews?${query}`);
    }

    async createStall(data) {
        return this.request('/stalls', 'POST', data);
    }

    async updateStall(id, data) {
        return this.request(`/stalls/${id}`, 'PUT', data);
    }

    async deleteStall(id) {
        return this.request(`/stalls/${id}`, 'DELETE');
    }

    // Review methods
    async getReviews(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/reviews?${query}`);
    }

    async getReview(id) {
        return this.request(`/reviews/${id}`);
    }

    async createReview(data) {
        return this.request('/reviews', 'POST', data);
    }

    async updateReview(id, data) {
        return this.request(`/reviews/${id}`, 'PUT', data);
    }

    async deleteReview(id) {
        return this.request(`/reviews/${id}`, 'DELETE');
    }

    async reactToReview(reviewId, reactionType) {
        return this.request('/reviews/react', 'POST', {
            review_id: reviewId,
            reaction_type: reactionType
        });
    }

    async reportReview(reviewId, reason, details = '') {
        return this.request('/reviews/report', 'POST', {
            review_id: reviewId,
            reason,
            details
        });
    }

    // Application methods
    async getApplications(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/applications?${query}`);
    }

    async getApplication(id) {
        return this.request(`/applications/${id}`);
    }

    async createApplication(data) {
        return this.request('/applications', 'POST', data);
    }

    async updateApplication(id, data) {
        return this.request(`/applications/${id}`, 'PUT', data);
    }

    async approveApplication(id) {
        return this.request(`/applications/${id}/approve`, 'POST');
    }

    async rejectApplication(id, reason) {
        return this.request(`/applications/${id}/reject`, 'POST', { reason });
    }

    // Amendment methods
    async getAmendments(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/amendments?${query}`);
    }

    async getAmendment(id) {
        return this.request(`/amendments/${id}`);
    }

    async createAmendment(data) {
        return this.request('/amendments', 'POST', data);
    }

    async approveAmendment(id) {
        return this.request(`/amendments/${id}/approve`, 'POST');
    }

    async rejectAmendment(id) {
        return this.request(`/amendments/${id}/reject`, 'POST');
    }

    // Closure methods
    async getClosures(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/closures?${query}`);
    }

    async getClosure(id) {
        return this.request(`/closures/${id}`);
    }

    async createClosure(data) {
        return this.request('/closures', 'POST', data);
    }

    async approveClosure(id) {
        return this.request(`/closures/${id}/approve`, 'POST');
    }

    async rejectClosure(id) {
        return this.request(`/closures/${id}/reject`, 'POST');
    }

    // Admin methods
    async getDashboardStats() {
        return this.request('/admin/dashboard');
    }

    async getAdminLogs(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/admin/logs?${query}`);
    }

    async getReports(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/admin/reports?${query}`);
    }
}

// Create a global instance
const api = new ApiClient();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ApiClient, api };
}

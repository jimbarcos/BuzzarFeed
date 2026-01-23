# BuzzarFeed

A comprehensive web-based platform for discovering and reviewing food stalls at the BGC Night Market Bazaar. BuzzarFeed enables customers to explore food options, read reviews, and interact with vendors while providing stall owners with registration and management capabilities.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Schema](#database-schema)
- [Usage](#usage)
- [File Structure and Purpose](#file-structure-and-purpose)
- [Development](#development)
- [License](#license)

## Overview

BuzzarFeed is a night market discovery platform designed specifically for the BGC (Bonifacio Global City) Night Market Bazaar. The application follows ISO 9241 principles emphasizing maintainability, reusability, and extensibility through a modular architecture.

**Version:** 2.0  
**Last Updated:** January 2026  
**PHP Version Required:** >= 7.4

## Features

### Customer Features
- Browse and search food stalls by category and location
- Interactive map view of stall locations
- Read and write detailed reviews with ratings
- React to reviews (helpful/not helpful)
- Report inappropriate reviews
- User account management
- Password recovery system

### Stall Owner Features
- Register new food stalls
- Manage stall information and menu items
- Upload stall and menu images
- Track application status
- Respond to customer reviews

### Admin Features
- Review and approve/reject stall applications
- Moderate user reviews and handle reports
- Convert users to admin status
- View comprehensive admin activity logs
- Manage stall and user data

## Technology Stack

### Backend
- **PHP** (>= 7.4) - Server-side scripting
- **MySQL** - Database management
- **PHPMailer** - Email functionality (if configured)

### Frontend
- **HTML5** - Structure
- **CSS3** - Styling with custom variables and component-based architecture
- **JavaScript (ES6+)** - Client-side interactivity
- **Modular JS** - Component-based JavaScript architecture

### Architecture
- **MVC Pattern** - Model-View-Controller separation
- **Service Layer** - Business logic encapsulation
- **Component-based UI** - Reusable UI components
- **RESTful API** - Centralized API endpoints

## Project Structure

```
BuzzarFeed/
├── api/                   # API router and endpoints
├── assets/                # Static assets (CSS, JS, images)
├── config/                # Configuration files
├── error/                 # Error page templates
├── includes/              # Reusable page components (header, footer)
├── sections/              # Page-specific sections
├── src/                   # Application source code
│   ├── Api/              # API controllers
│   ├── components/       # UI components
│   ├── Services/         # Business logic services
│   └── utils/            # Utility classes
├── *.php                  # Main application pages
├── bootstrap.php          # Application initialization
└── README.md             # This file
```

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx with mod_rewrite enabled)

### Steps

1. **Clone or download the repository**
   ```bash
   git clone <repository-url>
   cd BuzzarFeed
   ```

2. **Configure database connection**
   Edit `config/config.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Import database**
   - Create a MySQL database
   - Import the database schema (contact administrator for schema file)

4. **Configure web server**
   - Point document root to the BuzzarFeed folder
   - Ensure `.htaccess` is enabled for Apache (mod_rewrite)
   - Verify PHP version >= 7.4

5. **Set up file permissions** (Linux/Mac)
   ```bash
   chmod -R 755 assets/
   ```

## Configuration

Configuration is managed in `config/config.php`. Key settings include:

### Database Configuration
- `DB_HOST` - Database host (default: localhost)
- `DB_NAME` - Database name
- `DB_USER` - Database username
- `DB_PASS` - Database password

### Application Settings
- Site URL and paths
- Session configuration
- Error handling
- API endpoint routing
- Security settings

## Database Schema

The database consists of several interconnected tables:

### Core Tables
- **users** - User accounts and authentication
- **user_types** - User role definitions (customer, stall_owner, admin)
- **food_stalls** - Active food stall listings
- **stall_applications** - Pending stall registration applications
- **menu_items** - Food items offered by stalls
- **reviews** - Customer reviews and ratings
- **review_reactions** - User reactions to reviews
- **review_reports** - Flagged inappropriate reviews

### Support Tables
- **approval_statuses** - Application status tracking
- **menu_categories** - Food category classifications
- **password_reset_tokens** - Password recovery tokens
- **admin_logs** - Admin activity audit trail

## Usage

### For Customers
1. Visit the homepage to browse featured stalls
2. Use the map view to locate stalls by position
3. Click on stalls to view details, menus, and reviews
4. Register an account to write reviews and save favorites
5. Rate and react to existing reviews

### For Stall Owners
1. Create a customer account first
2. Navigate to "Register Your Stall"
3. Fill out the application form with stall details
4. Upload required documents and images
5. Wait for admin approval
6. Manage stall details and menu items after approval

### For Administrators
1. Log in with admin credentials
2. Access the Admin Panel
3. Review pending stall applications
4. Approve or reject with feedback
5. Moderate flagged reviews
6. View activity logs and statistics

## File Structure and Purpose

### Root Level Files

| File | Purpose |
|------|---------|
| `index.php` | Homepage with featured stalls and reviews |
| `bootstrap.php` | Application initialization and autoloading |
| `composer.json` | PHP dependency management configuration |
| `about.php` | About page with team information and CTA |
| `login.php` | User authentication page |
| `signup.php` | New user registration |
| `logout.php` | Session termination handler |
| `forgot-password.php` | Password recovery request |
| `reset-password.php` | Password reset with token |
| `my-account.php` | User profile management |
| `my-reviews.php` | User's review history |
| `stalls.php` | Browse all food stalls |
| `stall-detail.php` | Individual stall information |
| `register-stall.php` | Stall owner application form |
| `registration-pending.php` | Application status page |
| `manage-stall.php` | Stall owner dashboard |
| `map.php` | Interactive stall location map |
| `admin-panel.php` | Admin dashboard and management |
| `convert-to-admin.php` | Utility to promote users to admin |
| `terms.php` | Terms of service page |

### Directory Structure

#### `/assets/`
Static frontend resources

**`/assets/css/`** - Stylesheet files
- `base.css` - Base styles and resets
- `variables.css` - CSS custom properties for theming
- `styles.css` - Global styles
- `components/` - Component-specific styles
- Page-specific stylesheets (e.g., `login.css`, `stalls.css`)

**`/assets/js/`** - JavaScript files
- `main.js` - Core JavaScript functionality
- `app.js` - Application-level logic
- `modules/` - Modular JavaScript components

**`/assets/images/`** - Static images
- `about/` - About page images

#### `/config/`
Application configuration

| File | Purpose |
|------|---------|
| `config.php` | Central configuration file with database, paths, and app settings |

#### `/api/`
RESTful API router and endpoints

| File | Purpose |
|------|---------|
| `index.php` | API router - routes all /api/* requests to appropriate controllers |

#### `/error/`
HTTP error page templates

| File | Purpose |
|------|---------|
| `400.php` | Bad Request error page |
| `401.php` | Unauthorized access page |
| `403.php` | Forbidden access page |
| `404.php` | Page not found error |
| `500.php` | Internal server error page |
| `503.php` | Service unavailable page |

#### `/includes/`
Reusable page components

| File | Purpose |
|------|---------|
| `header.php` | Site header with navigation |
| `footer.php` | Site footer with links |

#### `/sections/`
Page-specific section components

**`/sections/about/`** - About page sections
- `HeroSection.php` - About hero banner
- `AboutSection.php` - Mission and vision content
- `TeamSection.php` - Team member profiles
- `CTASection.php` - Call-to-action component

**`/sections/home/`** - Homepage sections
- `HeroSection.php` - Homepage hero banner
- `FeaturedStallsSection.php` - Featured stalls showcase
- `ReviewsSection.php` - Recent reviews display

**`/sections/map/`** - Map page sections
- `MapSection.php` - Interactive map component
- `ExploreSection.php` - Map exploration interface

#### `/src/`
Application source code (PSR-4 structure)

**`/src/components/`** - Reusable UI components
- `BaseComponent.php` - Abstract base component class
- `ComponentFactory.php` - Component instantiation factory
- `common/` - Common UI components (buttons, cards, etc.)

**`/src/Services/`** - Business logic layer

| File | Purpose |
|------|---------|
| `UserService.php` | User account operations (auth, profile, password) |
| `StallService.php` | Food stall data management |
| `StallRegistrationService.php` | New stall application processing |
| `ApplicationService.php` | Application approval workflow |
| `ReviewService.php` | Review CRUD operations and moderation |
| `ReviewReportService.php` | Review flagging and reporting |
| `AdminLogService.php` | Admin activity logging and audit trail |

**`/src/utils/`** - Utility classes

| File | Purpose |
|------|---------|
| `Database.php` | PDO database connection wrapper |
| `Session.php` | Session management and flash messages |
| `Helpers.php` | General helper functions (redirects, sanitization) |
| `ApplicationHelper.php` | Application-specific helper functions |
| `ReviewHelper.php` | Review-related helper functions |
| `ApiResponse.php` | Standardized JSON API responses |

**`/src/Api/`** - RESTful API Controllers

| File | Purpose |
|------|---------|
| `BaseController.php` | Base controller with common API functionality |
| `AuthController.php` | Authentication endpoints (login, register, logout) |
| `StallController.php` | Stall browsing and management endpoints |
| `ReviewController.php` | Review CRUD and reaction endpoints |
| `UserController.php` | User profile and account management |
| `ApplicationController.php` | Stall application workflow |
| `AmendmentController.php` | Amendment request handling |
| `ClosureController.php` | Account closure requests |
| `AdminController.php` | Admin dashboard and statistics |

## Development

### Code Standards
- Follow PSR-4 autoloading standards
- Use meaningful variable and function names
- Comment complex logic and business rules
- Maintain separation of concerns (MVC pattern)
- Keep components modular and reusable

### Architecture Principles
The application follows ISO 9241 guidelines:
- **Maintainability** - Clean code structure, clear documentation
- **Reusability** - Component-based architecture, service layer
- **Extensibility** - Modular design, dependency injection ready

### Adding New Features

1. **Database Changes**
   - Modify database schema as needed
   - Document changes in configuration

2. **Business Logic**
   - Add service class in `src/Services/`
   - Follow existing service patterns

3. **API Endpoints**
   - Add methods to existing controllers in `src/Api/Controllers/`
   - Or create new controller extending `BaseController`
   - Update API client in `assets/js/api-client.js`

4. **UI Components**
   - Create component in `src/components/`
   - Add corresponding CSS in `assets/css/components/`

5. **New Pages**
   - Create PHP file in root directory
   - Create corresponding CSS in `assets/css/`
   - Add section components in `sections/` if needed

### Testing
- Test all features in development environment first
- Verify API endpoints return correct responses
- Test authentication and authorization
- Validate form inputs and security measures
- Check cross-browser compatibility

## Security Considerations

- All user inputs are sanitized and validated
- Prepared statements used for database queries
- Password hashing using PHP's password_hash()
- CSRF protection on forms
- Session security with httponly and secure flags
- File upload validation and type checking
- Admin routes protected with role-based access control

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

Copyright 2026 BuzzarFeed Development Team. All rights reserved.

## Contact

For support or inquiries, please contact the BuzzarFeed Development Team.

---

# API Documentation

## API Overview
BuzzarFeed features a RESTful API architecture with centralized endpoints. All API requests are routed through `/api/` and handled by dedicated controllers.

## Architecture

### File Structure
```
api/
  index.php                    # API router - entry point for all API requests
src/
  Api/
    Controllers/
      BaseController.php       # Base class for all controllers
      AuthController.php       # Authentication endpoints
      StallController.php      # Stall-related endpoints
      ReviewController.php     # Review endpoints
      UserController.php       # User management endpoints
      ApplicationController.php # Stall application endpoints
      AmendmentController.php   # Amendment request endpoints
      ClosureController.php     # Account closure endpoints
      AdminController.php       # Admin-specific endpoints
  Utils/
    ApiResponse.php            # Standardized JSON response utility
assets/
  js/
    api-client.js              # JavaScript client for API requests
```

## API Endpoints

### Authentication (`/api/auth/`)
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/register` - User registration
- `POST /api/auth/verify-email` - Email verification
- `POST /api/auth/forgot-password` - Request password reset
- `POST /api/auth/reset-password` - Reset password with token
- `GET /api/auth/check` - Check authentication status

### Stalls (`/api/stalls/`)
- `GET /api/stalls` - List all stalls (with pagination and filters)
  - Query params: `search`, `category`, `page`, `limit`
- `GET /api/stalls/{id}` - Get specific stall details
- `GET /api/stalls/{id}/menu` - Get stall menu items
- `GET /api/stalls/{id}/reviews` - Get stall reviews
- `POST /api/stalls` - Create new stall (auth required)
- `PUT /api/stalls/{id}` - Update stall (auth required)
- `DELETE /api/stalls/{id}` - Delete stall (admin only)

### Reviews (`/api/reviews/`)
- `GET /api/reviews` - List reviews
  - Query params: `stall_id`, `user_id`, `page`, `limit`
- `GET /api/reviews/{id}` - Get specific review
- `POST /api/reviews` - Create review (auth required)
- `PUT /api/reviews/{id}` - Update review (auth required, owner only)
- `DELETE /api/reviews/{id}` - Delete review (auth required, owner only)
- `POST /api/reviews/react` - React to review (like/helpful)
- `POST /api/reviews/report` - Report a review

### Users (`/api/users/`)
- `GET /api/users` - List users (admin only)
- `GET /api/users/{id}` - Get user details
- `GET /api/users/profile` - Get current user profile
- `PUT /api/users/profile` - Update current user profile
- `PUT /api/users/password` - Change password
- `PUT /api/users/{id}` - Update user (admin only)
- `DELETE /api/users/{id}` - Delete user (admin only)

### Applications (`/api/applications/`)
- `GET /api/applications` - List applications
- `GET /api/applications/{id}` - Get application details
- `POST /api/applications` - Create application
- `PUT /api/applications/{id}` - Update application
- `POST /api/applications/{id}/approve` - Approve application (admin)
- `POST /api/applications/{id}/reject` - Reject application (admin)

### Amendments (`/api/amendments/`)
- `GET /api/amendments` - List amendment requests
- `GET /api/amendments/{id}` - Get amendment details
- `POST /api/amendments` - Create amendment request
- `POST /api/amendments/{id}/approve` - Approve amendment (admin)
- `POST /api/amendments/{id}/reject` - Reject amendment (admin)

### Closures (`/api/closures/`)
- `GET /api/closures` - List closure requests
- `GET /api/closures/{id}` - Get closure details
- `POST /api/closures` - Create closure request
- `POST /api/closures/{id}/approve` - Approve closure (admin)
- `POST /api/closures/{id}/reject` - Reject closure (admin)

### Admin (`/api/admin/`)
- `GET /api/admin/dashboard` - Get dashboard statistics
- `GET /api/admin/logs` - Get admin activity logs
- `GET /api/admin/reports` - Get review reports

## Request/Response Format

### Request Format
All POST/PUT requests should send JSON data:
```json
{
  "field1": "value1",
  "field2": "value2"
}
```

### Response Format
All responses follow this standardized format:

**Success Response:**
```json
{
  "success": true,
  "message": "Success message",
  "data": { ... }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": []
}
```

**Paginated Response:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 100,
    "page": 1,
    "perPage": 10,
    "totalPages": 10
  }
}
```

## JavaScript Usage

### Include the API Client
```html
<script src="/assets/js/api-client.js"></script>
```

### Example: Login
```javascript
try {
    const response = await api.login('user@example.com', 'password');
    console.log('Logged in:', response.data.user);
} catch (error) {
    console.error('Login failed:', error.message);
}
```

### Example: Get Stalls with Filters
```javascript
try {
    const response = await api.getStalls({
        search: 'burger',
        category: 'Fast Food',
        page: 1,
        limit: 12
    });
    console.log('Stalls:', response.data);
    console.log('Total:', response.pagination.total);
} catch (error) {
    console.error('Error:', error.message);
}
```

### Example: Create a Review
```javascript
try {
    const response = await api.createReview({
        stall_id: 5,
        rating: 5,
        title: 'Amazing food!',
        comment: 'Best burger I ever had!'
    });
    console.log('Review created:', response.data);
} catch (error) {
    console.error('Error:', error.message);
}
```

### Example: Get Dashboard Stats (Admin)
```javascript
try {
    const response = await api.getDashboardStats();
    console.log('Stats:', response.data);
} catch (error) {
    console.error('Error:', error.message);
}
```

## PHP Server-Side Usage

### Services Still Work
The existing service classes continue to work for server-side rendering. The API controllers use these same services internally.

```php
// You can still use services in PHP pages for server-side rendering
use BuzzarFeed\Services\StallService;

$stallService = new StallService();
$stalls = $stallService->getAllActiveStalls();
```

### Making Internal API Calls (Optional)
For consistency, you can also make internal API calls from PHP:

```php
// Example internal API call helper
function callInternalApi($endpoint, $method = 'GET', $data = null) {
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/api' . $endpoint;
    
    $options = [
        'http' => [
            'method' => $method,
            'header' => 'Content-Type: application/json',
            'content' => $data ? json_encode($data) : null
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    return json_decode($response, true);
}

// Usage
$result = callInternalApi('/stalls?limit=10');
$stalls = $result['data'];
```

## Authentication

### Session-Based Auth
The API uses PHP sessions for authentication. When you log in via `/api/auth/login`, a session is created.

### Checking Auth Status
```javascript
const { data } = await api.checkAuth();
if (data.authenticated) {
    console.log('User:', data.user);
}
```

### Protected Endpoints
Endpoints marked with "auth required" will return 401 if not authenticated.
Admin-only endpoints will return 403 if user is not an admin.

## Error Handling

### HTTP Status Codes
- `200` - Success
- `201` - Created (for POST requests)
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (not logged in)
- `403` - Forbidden (insufficient privileges)
- `404` - Not Found
- `405` - Method Not Allowed
- `500` - Server Error

### Example Error Handling
```javascript
try {
    const response = await api.createReview(data);
} catch (error) {
    if (error.message.includes('Authentication required')) {
        // Redirect to login
        window.location.href = '/login';
    } else {
        // Show error message
        alert(error.message);
    }
}
```

## Migration Guide

### For Frontend JavaScript
**Before (direct form submission):**
```html
<form action="login.php" method="POST">
    <input name="email" type="email">
    <input name="password" type="password">
    <button type="submit">Login</button>
</form>
```

**After (API call):**
```html
<form id="loginForm">
    <input id="email" type="email">
    <input id="password" type="password">
    <button type="submit">Login</button>
</form>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    try {
        const response = await api.login(email, password);
        window.location.href = '/dashboard';
    } catch (error) {
        alert(error.message);
    }
});
</script>
```

### For PHP Pages
**Before:**
```php
use BuzzarFeed\Services\StallService;

$stallService = new StallService();
$stalls = $stallService->getAllActiveStalls();
```

**After (still works!):**
```php
// Services still work for server-side rendering
use BuzzarFeed\Services\StallService;

$stallService = new StallService();
$stalls = $stallService->getAllActiveStalls();

// OR use API for client-side data loading
// Let JavaScript fetch data via API
```

## Benefits

1. **Separation of Concerns**: Business logic in services, API in controllers, presentation in views
2. **Flexibility**: Can build mobile apps, SPAs, or other clients using the same API
3. **Consistency**: Standardized JSON responses across all endpoints
4. **Security**: Centralized authentication and authorization checks
5. **Testability**: API endpoints can be tested independently
6. **Scalability**: Easy to add caching, rate limiting, or move to microservices

## Notes

- Existing PHP pages with service classes continue to work for server-side rendering
- API is designed to support both traditional and modern (SPA) architectures
- All API responses use JSON format
- CORS is enabled for cross-origin requests
- Session cookies are used for authentication

# API Migration Summary

## What Was Created

### 1. API Infrastructure
- **[api/index.php](api/index.php)** - Central API router that handles all /api/* requests
- **[src/Utils/ApiResponse.php](src/Utils/ApiResponse.php)** - Standardized JSON response utility
- **[src/Api/Controllers/BaseController.php](src/Api/Controllers/BaseController.php)** - Base class for all controllers

### 2. API Controllers
All controllers provide RESTful endpoints for their respective resources:
- **[src/Api/Controllers/AuthController.php](src/Api/Controllers/AuthController.php)** - Authentication (login, register, password reset)
- **[src/Api/Controllers/StallController.php](src/Api/Controllers/StallController.php)** - Stall management and browsing
- **[src/Api/Controllers/ReviewController.php](src/Api/Controllers/ReviewController.php)** - Review CRUD and reactions
- **[src/Api/Controllers/UserController.php](src/Api/Controllers/UserController.php)** - User profile and management
- **[src/Api/Controllers/ApplicationController.php](src/Api/Controllers/ApplicationController.php)** - Stall applications
- **[src/Api/Controllers/AmendmentController.php](src/Api/Controllers/AmendmentController.php)** - Amendment requests
- **[src/Api/Controllers/ClosureController.php](src/Api/Controllers/ClosureController.php)** - Account closure requests
- **[src/Api/Controllers/AdminController.php](src/Api/Controllers/AdminController.php)** - Admin dashboard and logs

### 3. JavaScript API Client
- **[assets/js/api-client.js](assets/js/api-client.js)** - Complete client library for making API calls from JavaScript

### 4. Updated Configuration
- **[.htaccess](.htaccess)** - Routing rules for /api/* endpoints

## API Endpoints Summary

### Authentication
- POST /api/auth/login
- POST /api/auth/logout
- POST /api/auth/register
- GET /api/auth/check

### Stalls
- GET /api/stalls (with search, category filters)
- GET /api/stalls/{id}
- GET /api/stalls/{id}/menu
- GET /api/stalls/{id}/reviews
- POST /api/stalls
- PUT /api/stalls/{id}
- DELETE /api/stalls/{id}

### Reviews
- GET /api/reviews
- POST /api/reviews
- PUT /api/reviews/{id}
- DELETE /api/reviews/{id}
- POST /api/reviews/react
- POST /api/reviews/report

### Users
- GET /api/users/profile
- PUT /api/users/profile
- PUT /api/users/password

### Admin
- GET /api/admin/dashboard
- GET /api/admin/logs
- GET /api/admin/reports

## How to Use

### JavaScript (Frontend)
```javascript
// Include the API client
<script src="/assets/js/api-client.js"></script>

// Use the global 'api' object
const response = await api.getStalls({ category: 'Fast Food' });
console.log(response.data);
```

### PHP (Backend - Still Works)
```php
// Existing service-based code continues to work
use BuzzarFeed\Services\StallService;

$stallService = new StallService();
$stalls = $stallService->getAllActiveStalls();
```

## Key Benefits

1. **RESTful Architecture** - Standard HTTP methods and endpoints
2. **JSON Responses** - Consistent data format across all endpoints
3. **Authentication** - Session-based auth with proper security checks
4. **Pagination** - Built-in support for large datasets
5. **Error Handling** - Standardized error responses with HTTP codes
6. **Flexibility** - Can build SPAs, mobile apps, or keep traditional architecture

## Usage Patterns

The application supports both server-side and client-side data loading:

### Server-Side Rendering (Traditional)
PHP pages can use service classes directly for server-side rendering:
```php
use BuzzarFeed\Services\StallService;
$stallService = new StallService();
$stalls = $stallService->getAllActiveStalls();
```

### Client-Side API Calls (Modern)
JavaScript can fetch data via the API for dynamic loading:
```javascript
const response = await api.getStalls({ category: 'Fast Food' });
console.log(response.data);
```

## Testing API

```bash
# Test login
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# Test get stalls
curl http://localhost/api/stalls?category=Fast%20Food&limit=5

# Test get dashboard (requires admin login)
curl http://localhost/api/admin/dashboard
```

## Important Notes

- Services are reused by API controllers for consistency
- Session-based authentication is maintained across both traditional and API endpoints
- .htaccess routes all /api/* requests to the API router
- API responses follow a standardized JSON format

---

**Note:** This is an educational project designed for the BGC Night Market Bazaar food stall discovery platform.

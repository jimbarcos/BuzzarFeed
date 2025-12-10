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
**Created:** November 2025  
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
- **Composer** - Dependency management
- **PHPMailer** (^6.9) - Email functionality

### Frontend
- **HTML5** - Structure
- **CSS3** - Styling with custom variables and component-based architecture
- **JavaScript (ES6+)** - Client-side interactivity
- **Modular JS** - Component-based JavaScript architecture

### Architecture
- **PSR-4 Autoloading** - Standard PHP autoloading
- **MVC Pattern** - Model-View-Controller separation
- **Service Layer** - Business logic encapsulation
- **Component-based UI** - Reusable UI components

## Project Structure

```
htdocs/
├── assets/                 # Static assets (CSS, JS, images)
├── config/                 # Configuration files
├── database/              # Database schema and migrations
├── error/                 # Error page templates
├── includes/              # Reusable page components (header, footer)
├── sections/              # Page-specific sections
├── src/                   # Application source code
├── uploads/               # User-uploaded files
├── *.php                  # Main application pages
├── bootstrap.php          # Application initialization
├── composer.json          # PHP dependencies
└── README.md             # This file
```

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx)

### Steps

1. **Clone or download the repository**
   ```bash
   git clone <repository-url>
   cd htdocs
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Create environment file**
   ```bash
   cp .env.example .env
   ```

4. **Configure environment variables**
   Edit `.env` file with your database credentials and settings:
   ```
   APP_ENV=development
   APP_DEBUG=true
   DB_HOST=localhost
   DB_NAME=if0_40016301_db_buzzarfeed
   DB_USER=your_username
   DB_PASS=your_password
   SMTP_HOST=your_smtp_host
   SMTP_USER=your_email
   SMTP_PASS=your_password
   ```

5. **Import database schema**
   ```bash
   mysql -u your_username -p < database/schema.sql
   ```

6. **Run database migrations**
   ```bash
   mysql -u your_username -p if0_40016301_db_buzzarfeed < database/migrations/create_password_reset_tokens.sql
   mysql -u your_username -p if0_40016301_db_buzzarfeed < database/migrations/add_review_reactions.sql
   mysql -u your_username -p if0_40016301_db_buzzarfeed < database/migrations/add_title_to_reviews.sql
   mysql -u your_username -p if0_40016301_db_buzzarfeed < database/migrations/create_review_reports.sql
   mysql -u your_username -p if0_40016301_db_buzzarfeed < database/migrations/add_food_categories_to_food_stalls.sql
   mysql -u your_username -p if0_40016301_db_buzzarfeed < database/migrations/add_map_coordinates_to_applications.sql
   ```

7. **Set proper permissions**
   ```bash
   chmod -R 755 uploads/
   chmod -R 755 assets/
   ```

8. **Configure web server**
   - Point document root to `htdocs/`
   - Enable `.htaccess` for Apache (mod_rewrite)

## Configuration

### Environment Variables
The application uses environment variables for configuration. Key settings include:

- `APP_ENV` - Application environment (development/production)
- `APP_DEBUG` - Enable debug mode
- `DB_HOST` - Database host
- `DB_NAME` - Database name
- `DB_USER` - Database username
- `DB_PASS` - Database password
- `SMTP_HOST` - SMTP server for emails
- `SMTP_USER` - SMTP username
- `SMTP_PASS` - SMTP password

### File Configuration
Additional configuration is available in `config/config.php` for:
- Site URL and paths
- Upload directories
- Session settings
- Error handling
- Email templates

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

#### `/database/`
Database structure and migrations

| File/Folder | Purpose |
|-------------|---------|
| `schema.sql` | Complete database schema definition |
| `migrations/` | Database migration files for schema updates |
| `migrations/create_password_reset_tokens.sql` | Password reset functionality |
| `migrations/add_review_reactions.sql` | Review reaction system |
| `migrations/add_title_to_reviews.sql` | Review title field addition |
| `migrations/create_review_reports.sql` | Review reporting system |
| `migrations/add_food_categories_to_food_stalls.sql` | Category system for stalls |
| `migrations/add_map_coordinates_to_applications.sql` | Geolocation support |

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
| `Env.php` | Environment variable loading and management |
| `EmailService.php` | Email sending via PHPMailer |
| `ApplicationHelper.php` | Application-specific helper functions |
| `ReviewHelper.php` | Review-related helper functions |

**`/src/libs/`** - Third-party libraries
- `phpmailer/` - PHPMailer email library

#### `/uploads/`
User-uploaded content

| Folder | Purpose |
|--------|---------|
| `applications/` | Stall application documents and images |
| `stalls/` | Approved stall images |
| `menu_items/` | Menu item photos |

Each application creates a subdirectory named with the pattern: `stallname_timestamp/`

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
   - Create migration file in `database/migrations/`
   - Update `schema.sql` for fresh installations

2. **Business Logic**
   - Add service class in `src/Services/`
   - Follow existing service patterns

3. **UI Components**
   - Create component in `src/components/`
   - Add corresponding CSS in `assets/css/components/`

4. **New Pages**
   - Create PHP file in root directory
   - Create corresponding CSS in `assets/css/`
   - Add section components in `sections/` if needed

### Testing
- Test all features in development environment first
- Verify database migrations run successfully
- Check file upload functionality
- Test email sending capabilities
- Validate form inputs and security measures

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

Copyright 2025 BuzzarFeed Development Team. All rights reserved.

## Contact

For support or inquiries, please contact the BuzzarFeed Development Team.

---

**Note:** This is a prototype/educational project designed for the BGC Night Market Bazaar food stall discovery platform.

<?php
/**
 * BuzzarFeed - Header Component
 * 
 * Reusable header navigation component for all pages
 * Includes logo, main navigation, and user authentication links
 * 
 * @package BuzzarFeed
 * @version 1.0
 * @author BuzzarFeed Development Team
 */

// Check if user is logged in (session management)
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$userType = $isLoggedIn ? $_SESSION['user_type'] : ''; // 'user' or 'owner'
?>

<header class="header">
    <div class="container">
        <nav class="nav-container">
            <!-- Logo -->
            <a href="index.php" class="logo">
                BuzzarFeed
            </a>
            
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <!-- Centered Navigation Menu -->
            <ul class="nav-menu">
                <li>
                    <a href="stalls.php" class="nav-link">
                        Stalls
                    </a>
                </li>
                <li>
                    <a href="about.php" class="nav-link">
                        About
                    </a>
                </li>
                <li>
                    <a href="map.php" class="nav-link">
                        Map
                    </a>
                </li>
            </ul>
            
            <!-- Authentication Links on Right -->
            <div class="nav-actions">
                    <?php if ($isLoggedIn): ?>
                        <!-- Logged In User -->
                        <div class="user-dropdown">
                            <button class="btn btn-outline" id="userDropdownBtn">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($username); ?>
                            </button>
                            <ul class="dropdown-menu" id="userDropdownMenu">
                                <li>
                                    <a href="profile.php">
                                        <i class="fas fa-user-circle"></i> My Profile
                                    </a>
                                </li>
                                <?php if ($userType === 'owner'): ?>
                                    <li>
                                        <a href="my-stall.php">
                                            <i class="fas fa-store"></i> My Stall
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a href="my-reviews.php">
                                        <i class="fas fa-star"></i> My Reviews
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="logout.php" class="logout-link">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Not Logged In -->
                        <a href="login.php" class="btn btn-outline">
                            Log In
                        </a>
                        <a href="signup.php" class="btn btn-secondary">
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
</header>

<?php if ($isLoggedIn): ?>
<style>
/* User Dropdown Styles */
.user-dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    min-width: 200px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-menu li {
    list-style: none;
}

.dropdown-menu a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    color: #333;
    transition: background-color 0.2s ease;
}

.dropdown-menu a:hover {
    background-color: #f5f5f5;
}

.dropdown-menu .divider {
    height: 1px;
    background-color: #e0e0e0;
    margin: 8px 0;
}

.dropdown-menu .logout-link {
    color: #E8663E;
}

@media (max-width: 768px) {
    .user-dropdown {
        width: 100%;
    }
    
    .dropdown-menu {
        position: static;
        opacity: 1;
        visibility: visible;
        transform: none;
        box-shadow: none;
        margin-top: 10px;
    }
}
</style>

<script>
// User dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.getElementById('userDropdownBtn');
    const dropdownMenu = document.getElementById('userDropdownMenu');
    
    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
        });
        
        // Prevent dropdown from closing when clicking inside
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>
<?php endif; ?>

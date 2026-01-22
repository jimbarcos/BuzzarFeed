<?php
/*
PROGRAM NAME: Food Stall Management Service (StallService.php)

PROGRAMMER: Backend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed platform.
It provides business logic for managing food stalls, including retrieval, search, categorization, and featured selection.
The StallService class interacts with the Database utility to ensure consistent access to stall-related data and formatted output.
It is typically used by controllers, API endpoints, and other service layers requiring stall information for display, search, or analytics purposes.

DATE CREATED: Novemeber 29, 2025
LAST MODIFIED: Novemeber 29, 2025

PURPOSE:
The purpose of this program is to centralize food stall operations into a reusable and maintainable service.
It provides methods for retrieving active stalls, searching by name or description, filtering by categories, obtaining featured/random stalls, and fetching stall metadata.
By abstracting database interactions, it enforces consistent data formatting and simplifies application logic related to stalls.

DATA STRUCTURES:
- $db (Database): Database instance used for queries.
- Stall data arrays:
  - stall_id (int): Unique identifier of the stall.
  - name (string): Stall name.
  - description (string): Stall description.
  - logo_path (string): Path to the stall’s logo image.
  - food_categories (array|string JSON): Categories associated with the stall.
  - hours (string): Operating hours.
  - average_rating (float): Average review rating.
  - total_reviews (int): Number of reviews.
  - address (string): Stall location address.
  - latitude (float|null): Latitude coordinate.
  - longitude (float|null): Longitude coordinate.
  - owner_name (string|null): Stall owner’s name.
  - owner_email (string|null): Stall owner’s email.
- $searchTerm (string): Search query for stall names/descriptions.
- $limit (int): Number of stalls to fetch for featured/random selection.
- Category variations arrays: Maps standard category names to possible database representations.

ALGORITHM / LOGIC:
1. Initialize Database instance on service construction.
2. Retrieve all active stalls:
   a. Join with stall locations and reviews.
   b. Calculate average ratings and review counts.
   c. Format and return results.
3. Filter stalls by category:
   a. Handle multiple variations of category names.
   b. Build dynamic JSON_CONTAINS conditions for search.
   c. Retrieve and format matching stalls.
4. Search stalls by name or description:
   a. Use LIKE queries for flexible matching.
   b. Format and return results.
5. Retrieve stall by ID:
   a. Include owner information.
   b. Format stall data for consistent structure.
6. Fetch random stalls for featured sections:
   a. Use ORDER BY RAND() with limit.
   b. Format and return results.
7. Retrieve all unique food categories:
   a. Decode JSON-encoded categories.
   b. Merge and deduplicate into a unique array.
8. Format stall data consistently:
   a. Decode JSON categories safely.
   b. Ensure default values for missing fields.
   c. Return standardized array structure for application use.

NOTES:
- All public methods ensure data is formatted consistently before returning to controllers or endpoints.
- JSON-encoded food categories are handled safely to accommodate variations in storage.
- Average ratings and review counts are computed dynamically for display purposes.
- Category filtering supports multiple variations to ensure flexible search.
- This service abstracts database operations to maintain separation of concerns and modularity.
- Future enhancements may include pagination support, location-based filtering, or caching for performance optimization.
*/

namespace BuzzarFeed\Services;

use BuzzarFeed\Utils\Database;

class StallService
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all active stalls with their details
     * 
     * @return array
     */
    public function getAllActiveStalls(): array
    {
        $query = "SELECT 
                    fs.stall_id,
                    fs.name,
                    fs.description,
                    fs.logo_path,
                    fs.food_categories,
                    fs.hours,
                    COALESCE(AVG(r.rating), 0) as average_rating,
                    COUNT(r.review_id) as total_reviews,
                    sl.address,
                    sl.latitude,
                    sl.longitude
                  FROM food_stalls fs
                  LEFT JOIN stall_locations sl ON fs.stall_id = sl.stall_id
                  LEFT JOIN reviews r ON fs.stall_id = r.stall_id
                  WHERE fs.is_active = 1
                  GROUP BY fs.stall_id, fs.name, fs.description, fs.logo_path, fs.food_categories, fs.hours, sl.address, sl.latitude, sl.longitude
                  ORDER BY fs.created_at DESC";
        
        $stalls = $this->db->query($query);
        
        // Process the results
        return array_map(function($stall) {
            return $this->formatStallData($stall);
        }, $stalls);
    }
    
    /**
     * Get stalls by category
     * 
     * @param string $category
     * @return array
     */
    public function getStallsByCategory(string $category): array
    {
        // Handle special cases for category names that might be stored differently
        $categoryVariations = [
            'Beverages' => ['Beverages', 'beverages', 'Beverage', 'beverage'],
            'Street Food' => ['Street Food', 'street_food', 'Streetfood', 'streetfood', 'Street food'],
            'Rice Meals' => ['Rice Meals', 'rice_meals', 'Rice meals', 'rice meals', 'RiceMeals'],
            'Fast Food' => ['Fast Food', 'fast_food', 'Fastfood', 'fastfood', 'Fast food'],
            'Snacks' => ['Snacks', 'snacks', 'Snack', 'snack'],
            'Pastries' => ['Pastries', 'pastries', 'Pastry', 'pastry'],
            'Others' => ['Others', 'others', 'Other', 'other']
        ];
        
        // Get possible variations for the category
        $searchCategories = $categoryVariations[$category] ?? [$category];
        
        // Build WHERE conditions for all variations
        $conditions = [];
        $params = [];
        foreach ($searchCategories as $variation) {
            $conditions[] = "JSON_CONTAINS(fs.food_categories, ?, '$')";
            $params[] = json_encode($variation);
        }
        
        $whereClause = '(' . implode(' OR ', $conditions) . ')';
        
        $query = "SELECT 
                    fs.stall_id,
                    fs.name,
                    fs.description,
                    fs.logo_path,
                    fs.food_categories,
                    fs.hours,
                    COALESCE(AVG(r.rating), 0) as average_rating,
                    COUNT(r.review_id) as total_reviews,
                    sl.address,
                    sl.latitude,
                    sl.longitude
                  FROM food_stalls fs
                  LEFT JOIN stall_locations sl ON fs.stall_id = sl.stall_id
                  LEFT JOIN reviews r ON fs.stall_id = r.stall_id
                  WHERE fs.is_active = 1
                  AND {$whereClause}
                  GROUP BY fs.stall_id, fs.name, fs.description, fs.logo_path, fs.food_categories, fs.hours, sl.address, sl.latitude, sl.longitude
                  ORDER BY fs.created_at DESC";
        
        $stalls = $this->db->query($query, $params);
        
        return array_map(function($stall) {
            return $this->formatStallData($stall);
        }, $stalls);
    }
    
    /**
     * Search stalls by name or description
     * 
     * @param string $searchTerm
     * @return array
     */
    public function searchStalls(string $searchTerm): array
    {
        $query = "SELECT 
                    fs.stall_id,
                    fs.name,
                    fs.description,
                    fs.logo_path,
                    fs.food_categories,
                    fs.hours,
                    COALESCE(AVG(r.rating), 0) as average_rating,
                    COUNT(r.review_id) as total_reviews,
                    sl.address,
                    sl.latitude,
                    sl.longitude
                  FROM food_stalls fs
                  LEFT JOIN stall_locations sl ON fs.stall_id = sl.stall_id
                  LEFT JOIN reviews r ON fs.stall_id = r.stall_id
                  WHERE fs.is_active = 1
                  AND (fs.name LIKE ? OR fs.description LIKE ?)
                  GROUP BY fs.stall_id, fs.name, fs.description, fs.logo_path, fs.food_categories, fs.hours, sl.address, sl.latitude, sl.longitude
                  ORDER BY fs.created_at DESC";
        
        $searchPattern = "%{$searchTerm}%";
        $stalls = $this->db->query($query, [$searchPattern, $searchPattern]);
        
        return array_map(function($stall) {
            return $this->formatStallData($stall);
        }, $stalls);
    }
    
    /**
     * Get stall by ID
     * 
     * @param int $stallId
     * @return array|null
     */
    public function getStallById(int $stallId): ?array
    {
        $query = "SELECT 
                    fs.stall_id,
                    fs.name,
                    fs.description,
                    fs.logo_path,
                    fs.food_categories,
                    fs.hours,
                    fs.average_rating,
                    fs.total_reviews,
                    sl.address,
                    sl.latitude,
                    sl.longitude,
                    u.name as owner_name,
                    u.email as owner_email
                  FROM food_stalls fs
                  LEFT JOIN stall_locations sl ON fs.stall_id = sl.stall_id
                  LEFT JOIN users u ON fs.owner_id = u.user_id
                  WHERE fs.stall_id = ? AND fs.is_active = 1";
        
        $stall = $this->db->querySingle($query, [$stallId]);
        
        return $stall ? $this->formatStallData($stall) : null;
    }
    
    /**
     * Get random stalls for featured section
     * 
     * @param int $limit
     * @return array
     */
    public function getRandomStalls(int $limit = 6): array
    {
        $query = "SELECT 
                    fs.stall_id,
                    fs.name,
                    fs.description,
                    fs.logo_path,
                    fs.food_categories,
                    fs.hours,
                    COALESCE(AVG(r.rating), 0) as average_rating,
                    COUNT(r.review_id) as total_reviews,
                    sl.address,
                    sl.latitude,
                    sl.longitude
                  FROM food_stalls fs
                  LEFT JOIN stall_locations sl ON fs.stall_id = sl.stall_id
                  LEFT JOIN reviews r ON fs.stall_id = r.stall_id
                  WHERE fs.is_active = 1
                  GROUP BY fs.stall_id, fs.name, fs.description, fs.logo_path, fs.food_categories, fs.hours, sl.address, sl.latitude, sl.longitude
                  ORDER BY RAND()
                  LIMIT ?";
        
        $stalls = $this->db->query($query, [$limit]);
        
        return array_map(function($stall) {
            return $this->formatStallData($stall);
        }, $stalls);
    }
    
    /**
     * Get all unique food categories from active stalls
     * 
     * @return array
     */
    public function getAllCategories(): array
    {
        $query = "SELECT DISTINCT food_categories 
                  FROM food_stalls 
                  WHERE is_active = 1 
                  AND food_categories IS NOT NULL";
        
        $results = $this->db->query($query);
        $categories = [];
        
        foreach ($results as $result) {
            if (!empty($result['food_categories'])) {
                $cats = json_decode($result['food_categories'], true);
                if (is_array($cats)) {
                    $categories = array_merge($categories, $cats);
                }
            }
        }
        
        return array_values(array_unique($categories));
    }
    
    /**
     * Format stall data for display
     * 
     * @param array $stall
     * @return array
     */
    private function formatStallData(array $stall): array
    {
        // Decode JSON categories
        $categories = !empty($stall['food_categories']) 
            ? json_decode($stall['food_categories'], true) 
            : [];
        
        // Ensure categories is an array
        if (!is_array($categories)) {
            $categories = [];
        }
        
        return [
            'id' => $stall['stall_id'],
            'name' => $stall['name'],
            'description' => $stall['description'] ?? '',
            'categories' => $categories,
            'rating' => (float)($stall['average_rating'] ?? 0),
            'reviews' => (int)($stall['total_reviews'] ?? 0),
            'hours' => $stall['hours'] ?? 'Hours not specified',
            'image' => $stall['logo_path'] ?? null,
            'address' => $stall['address'] ?? '',
            'latitude' => $stall['latitude'] ?? null,
            'longitude' => $stall['longitude'] ?? null,
            'owner_name' => $stall['owner_name'] ?? null,
            'owner_email' => $stall['owner_email'] ?? null,
        ];
    }
}

<?php
/*
PROGRAM NAME: Base Component (BaseComponent.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed Components library.
It serves as the base abstract class for all UI components, providing common properties, methods, and rendering utilities.

DATE CREATED: Decemeber 4, 2025
LAST MODIFIED: Decemeber 4, 2025

PURPOSE:
The purpose of this program is to:
- Provide a base class for all UI components to ensure consistency.
- Handle common properties such as ID, CSS classes, and HTML attributes.
- Provide helper methods for rendering, attribute building, and class merging.
- Facilitate reusability, extensibility, and maintainability of UI components.

DATA STRUCTURES:
- $props (array): Stores component properties passed during instantiation.
- $attributes (array): Stores additional HTML attributes for the component.
- $classes (string): Stores CSS classes for the component.
- $id (string): Stores the component ID.

ALGORITHM / LOGIC:
1. __construct(array $props = []):
   - Stores properties and calls initialize() for extraction.
2. initialize():
   - Extracts common properties like id, classes, and attributes.
3. prop(string $key, $default = null):
   - Retrieves a property value with optional default.
4. buildAttributes():
   - Builds a string of HTML attributes including id, class, and additional attributes.
5. mergeClasses(string ...$classes):
   - Merges multiple CSS class strings into one.
6. render(): abstract
   - Must be implemented by child classes to render component HTML.
7. __toString():
   - Allows the component to be echoed as a string.
8. make(array $props = []):
   - Static factory method for easier instantiation.
9. display():
   - Renders and echoes the component HTML directly.

NOTES:
- Designed to support dynamic, reusable UI components in BuzzarFeed.
- Can be extended by other components to standardize property handling and rendering.
*/

namespace BuzzarFeed\Components;

use BuzzarFeed\Utils\Helpers;

abstract class BaseComponent {
    
    /**
     * @var array Component properties
     */
    protected array $props = [];
    
    /**
     * @var array Component attributes (HTML attributes)
     */
    protected array $attributes = [];
    
    /**
     * @var string Component CSS classes
     */
    protected string $classes = '';
    
    /**
     * @var string Component ID
     */
    protected string $id = '';
    
    /**
     * Constructor
     * 
     * @param array $props Component properties
     */
    public function __construct(array $props = []) {
        $this->props = $props;
        $this->initialize();
    }
    
    /**
     * Initialize component (override in child classes)
     * 
     * @return void
     */
    protected function initialize(): void {
        // Extract common properties
        $this->id = $this->props['id'] ?? '';
        $this->classes = $this->props['class'] ?? '';
        $this->attributes = $this->props['attributes'] ?? [];
    }
    
    /**
     * Render the component (must be implemented by child classes)
     * 
     * @return string Rendered HTML
     */
    abstract public function render(): string;
    
    /**
     * Get property value
     * 
     * @param string $key Property key
     * @param mixed $default Default value
     * @return mixed Property value or default
     */
    protected function prop(string $key, $default = null) {
        return $this->props[$key] ?? $default;
    }
    
    /**
     * Build HTML attributes string
     * 
     * @return string HTML attributes
     */
    protected function buildAttributes(): string {
        $attrs = [];
        
        if (!empty($this->id)) {
            $attrs[] = 'id="' . Helpers::escape($this->id) . '"';
        }
        
        if (!empty($this->classes)) {
            $attrs[] = 'class="' . Helpers::escape($this->classes) . '"';
        }
        
        foreach ($this->attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $attrs[] = Helpers::escape($key);
                }
            } else {
                $attrs[] = Helpers::escape($key) . '="' . Helpers::escape($value) . '"';
            }
        }
        
        return implode(' ', $attrs);
    }
    
    /**
     * Merge CSS classes
     * 
     * @param string ...$classes Classes to merge
     * @return string Merged classes
     */
    protected function mergeClasses(string ...$classes): string {
        return implode(' ', array_filter($classes));
    }
    
    /**
     * Render component to string
     * 
     * @return string Rendered HTML
     */
    public function __toString(): string {
        return $this->render();
    }
    
    /**
     * Static factory method for easier instantiation
     * 
     * @param array $props Component properties
     * @return static Component instance
     */
    public static function make(array $props = []): static {
        return new static($props);
    }
    
    /**
     * Render and echo component
     * 
     * @return void
     */
    public function display(): void {
        echo $this->render();
    }
}

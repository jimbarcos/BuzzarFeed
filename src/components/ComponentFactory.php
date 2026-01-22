<?php
/*
PROGRAM NAME: Component Factory (ComponentFactory.php)

PROGRAMMER: Frontend Team 

SYSTEM CONTEXT:
This module is part of the BuzzarFeed Components library.
It implements a factory pattern to create reusable components dynamically.
It provides a central registry for all component classes, allowing consistent instantiation and management.

DATE CREATED: Decemeber 4, 2025
LAST MODIFIED: Decemeber 4, 2025

PURPOSE:
The purpose of this program is to:
- Register reusable UI components by name.
- Dynamically create instances of registered components with optional properties.
- Provide a central access point to check or retrieve all registered components.

DATA STRUCTURES:
- $registry (array): Static array holding component names as keys and class names as values.
- Props (array): Optional associative array of properties passed to a component on creation.

ALGORITHM / LOGIC:
1. register(string $name, string $class):
   - Adds a component class to the registry under the given name.
2. create(string $name, array $props = []):
   - Checks if the component is registered.
   - Throws an exception if not found.
   - Instantiates and returns a new component object, passing $props to the constructor.
3. has(string $name):
   - Returns true if the component name exists in the registry.
4. all():
   - Returns the entire registry array of registered components.
5. Auto-registration:
   - Automatically registers common components: Button, Input, Card, Modal.

NOTES:
- Facilitates consistent component creation and promotes code reuse across BuzzarFeed.
- Supports adding custom components dynamically by calling ComponentFactory::register().
*/

namespace BuzzarFeed\Components;

class ComponentFactory {
    
    /**
     * @var array Component registry
     */
    private static array $registry = [];
    
    /**
     * Register a component
     * 
     * @param string $name Component name
     * @param string $class Component class
     * @return void
     */
    public static function register(string $name, string $class): void {
        self::$registry[$name] = $class;
    }
    
    /**
     * Create a component instance
     * 
     * @param string $name Component name
     * @param array $props Component properties
     * @return BaseComponent Component instance
     * @throws \Exception If component not found
     */
    public static function create(string $name, array $props = []): BaseComponent {
        if (!isset(self::$registry[$name])) {
            throw new \Exception("Component '{$name}' not found");
        }
        
        $class = self::$registry[$name];
        return new $class($props);
    }
    
    /**
     * Check if component is registered
     * 
     * @param string $name Component name
     * @return bool True if registered
     */
    public static function has(string $name): bool {
        return isset(self::$registry[$name]);
    }
    
    /**
     * Get all registered components
     * 
     * @return array Component registry
     */
    public static function all(): array {
        return self::$registry;
    }
}

// Auto-register common components
ComponentFactory::register('button', 'BuzzarFeed\\Components\\Common\\Button');
ComponentFactory::register('input', 'BuzzarFeed\\Components\\Common\\Input');
ComponentFactory::register('card', 'BuzzarFeed\\Components\\Common\\Card');
ComponentFactory::register('modal', 'BuzzarFeed\\Components\\Common\\Modal');

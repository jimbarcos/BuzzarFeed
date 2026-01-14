<?php
/*
PROGRAM NAME: Button Component (Button.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed UI Components library.
It provides a reusable button component that supports different variants, sizes, icons, links, and behaviors.

DATE CREATED: Decemeber 4, 2025
LAST MODIFIED: Decemeber 4, 2025

PURPOSE:
The purpose of this program is to:
- Render a flexible, reusable button that can be used as a regular button or as a link.
- Support multiple variants: primary, secondary, success, outline.
- Support multiple sizes: small, medium, large.
- Optionally display an icon and handle full-width buttons.
- Follow ISO 9241 principles: Reusability, Consistency, and Accessibility.

DATA STRUCTURES:
- $text (string): Button text content.
- $variant (string): Button variant (primary, secondary, success, outline).
- $size (string): Button size (sm, md, lg).
- $icon (string|null): Optional icon class (FontAwesome or other).
- $href (string|null): Optional URL for link buttons.
- $type (string): Button type (button, submit, reset).
- $disabled (bool): Whether the button is disabled.
- $fullWidth (bool): Whether the button stretches to full width.
- $id (string): Button DOM ID.
- $classes (string): Custom CSS classes.
- $attributes (array): Optional HTML attributes.

ALGORITHM / LOGIC:
1. initialize():
   - Extracts component properties from $props.
   - Sets default values for variant, size, icon, type, disabled, and fullWidth.
2. render():
   - Determines whether the element is a <button> or <a> based on the presence of href.
   - Builds CSS classes and HTML attributes.
   - Renders optional icon and button text.
3. buildClasses():
   - Builds button CSS classes based on variant, size, full-width, and custom classes.

NOTES:
- Designed to be reused consistently across BuzzarFeed UI sections.
- Supports accessibility features via proper attributes.
- Can be extended for additional styles, behaviors, or event handling.
- Complies with ISO 9241 usability and modularity guidelines.
*/

namespace BuzzarFeed\Components\Common;

use BuzzarFeed\Components\BaseComponent;
use BuzzarFeed\Utils\Helpers;

class Button extends BaseComponent {
    
    /**
     * Button variants
     */
    const VARIANT_PRIMARY = 'primary';
    const VARIANT_SECONDARY = 'secondary';
    const VARIANT_SUCCESS = 'success';
    const VARIANT_OUTLINE = 'outline';
    
    /**
     * Button sizes
     */
    const SIZE_SMALL = 'sm';
    const SIZE_MEDIUM = 'md';
    const SIZE_LARGE = 'lg';
    
    /**
     * @var string Button text
     */
    protected string $text = '';
    
    /**
     * @var string Button variant
     */
    protected string $variant = self::VARIANT_PRIMARY;
    
    /**
     * @var string Button size
     */
    protected string $size = self::SIZE_MEDIUM;
    
    /**
     * @var string|null Button icon
     */
    protected ?string $icon = null;
    
    /**
     * @var string|null Button href for link buttons
     */
    protected ?string $href = null;
    
    /**
     * @var string Button type (button, submit, reset)
     */
    protected string $type = 'button';
    
    /**
     * @var bool Whether button is disabled
     */
    protected bool $disabled = false;
    
    /**
     * @var bool Whether button is full width
     */
    protected bool $fullWidth = false;
    
    /**
     * Initialize component
     */
    protected function initialize(): void {
        parent::initialize();
        
        $this->text = $this->prop('text', '');
        $this->variant = $this->prop('variant', self::VARIANT_PRIMARY);
        $this->size = $this->prop('size', self::SIZE_MEDIUM);
        $this->icon = $this->prop('icon');
        $this->href = $this->prop('href');
        $this->type = $this->prop('type', 'button');
        $this->disabled = $this->prop('disabled', false);
        $this->fullWidth = $this->prop('fullWidth', false);
    }
    
    /**
     * Render the button
     * 
     * @return string Rendered HTML
     */
    public function render(): string {
        $classes = $this->buildClasses();
        $tag = $this->href ? 'a' : 'button';
        
        $attributes = [];
        
        if (!empty($this->id)) {
            $attributes[] = 'id="' . Helpers::escape($this->id) . '"';
        }
        
        $attributes[] = 'class="' . Helpers::escape($classes) . '"';
        
        if ($tag === 'a') {
            $attributes[] = 'href="' . Helpers::escape($this->href) . '"';
        } else {
            $attributes[] = 'type="' . Helpers::escape($this->type) . '"';
            if ($this->disabled) {
                $attributes[] = 'disabled';
            }
        }
        
        // Add custom attributes
        foreach ($this->attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $attributes[] = Helpers::escape($key);
                }
            } else {
                $attributes[] = Helpers::escape($key) . '="' . Helpers::escape($value) . '"';
            }
        }
        
        $attrs = implode(' ', $attributes);
        
        $iconHtml = '';
        if ($this->icon) {
            $iconHtml = '<i class="' . Helpers::escape($this->icon) . '"></i> ';
        }
        
        return "<{$tag} {$attrs}>{$iconHtml}" . Helpers::escape($this->text) . "</{$tag}>";
    }
    
    /**
     * Build button CSS classes
     * 
     * @return string CSS classes
     */
    protected function buildClasses(): string {
        $classes = ['btn'];
        
        // Add variant class
        $classes[] = 'btn-' . $this->variant;
        
        // Add size class
        if ($this->size !== self::SIZE_MEDIUM) {
            $classes[] = 'btn-' . $this->size;
        }
        
        // Add full width class
        if ($this->fullWidth) {
            $classes[] = 'btn-full';
        }
        
        // Add custom classes
        if (!empty($this->classes)) {
            $classes[] = $this->classes;
        }
        
        return implode(' ', $classes);
    }
}

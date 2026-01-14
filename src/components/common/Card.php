<?php
/*
PROGRAM NAME: Card Component (Card.php)

PROGRAMMER: Frontend Team 

SYSTEM CONTEXT:
This module is part of the BuzzarFeed UI Components library.
It provides a reusable card component that can display content for stalls, reviews, join prompts, or any generic content.

DATE CREATED: Decemeber 4, 2025
LAST MODIFIED: Decemeber 4, 2025

PURPOSE:
The purpose of this program is to:
- Render a flexible, reusable card with optional title, subtitle, content, image, footer, and actions.
- Support multiple variants: default, stall, review, and join.
- Allow hover effects and custom styling via props.
- Follow ISO 9241 principles: Reusability and Flexibility.

DATA STRUCTURES:
- $variant (string): Card variant type (default, stall, review, join).
- $title (string|null): Card title.
- $subtitle (string|null): Card subtitle.
- $content (string|null): Card main content (HTML allowed).
- $image (string|null): Card image URL.
- $footer (string|null): Optional footer content.
- $actions (array): Array of buttons or links for card actions.
- $hoverable (bool): Whether the card has a hover effect.
- $id (string): Card DOM ID.
- $classes (string): Custom CSS classes.
- $attributes (array): Optional HTML attributes.

ALGORITHM / LOGIC:
1. initialize():
   - Extracts and sets component properties from $props.
   - Sets default values for variant, hoverable, and actions.
2. render():
   - Builds the card HTML including image, body (title, subtitle, content), and footer (actions or footer content).
3. renderImage():
   - Generates HTML for the card image if provided.
4. renderActions():
   - Renders actions if they exist, supports BaseComponent instances or raw HTML.
5. buildClasses():
   - Builds the card CSS classes based on variant, hoverable, and custom classes.

NOTES:
- Designed to be reusable across multiple sections in the BuzzarFeed UI.
- Supports dynamic customization through props and attributes.
- Can be extended for specialized card types or additional behaviors.
- Complies with ISO 9241 usability and modularity guidelines.
*/

namespace BuzzarFeed\Components\Common;

use BuzzarFeed\Components\BaseComponent;
use BuzzarFeed\Utils\Helpers;

class Card extends BaseComponent {
    
    /**
     * Card variants
     */
    const VARIANT_STALL = 'stall';
    const VARIANT_REVIEW = 'review';
    const VARIANT_JOIN = 'join';
    const VARIANT_DEFAULT = 'default';
    
    /**
     * @var string Card variant
     */
    protected string $variant = self::VARIANT_DEFAULT;
    
    /**
     * @var string|null Card title
     */
    protected ?string $title = null;
    
    /**
     * @var string|null Card subtitle
     */
    protected ?string $subtitle = null;
    
    /**
     * @var string|null Card content
     */
    protected ?string $content = null;
    
    /**
     * @var string|null Card image URL
     */
    protected ?string $image = null;
    
    /**
     * @var string|null Card footer content
     */
    protected ?string $footer = null;
    
    /**
     * @var array Card actions (buttons, links)
     */
    protected array $actions = [];
    
    /**
     * @var bool Whether card has hover effect
     */
    protected bool $hoverable = false;
    
    /**
     * Initialize component
     */
    protected function initialize(): void {
        parent::initialize();
        
        $this->variant = $this->prop('variant', self::VARIANT_DEFAULT);
        $this->title = $this->prop('title');
        $this->subtitle = $this->prop('subtitle');
        $this->content = $this->prop('content');
        $this->image = $this->prop('image');
        $this->footer = $this->prop('footer');
        $this->actions = $this->prop('actions', []);
        $this->hoverable = $this->prop('hoverable', false);
    }
    
    /**
     * Render the card
     * 
     * @return string Rendered HTML
     */
    public function render(): string {
        $classes = $this->buildClasses();
        $attributes = !empty($this->id) ? ' id="' . Helpers::escape($this->id) . '"' : '';
        
        $html = '<article class="' . Helpers::escape($classes) . '"' . $attributes . '>';
        
        // Render image
        if ($this->image) {
            $html .= $this->renderImage();
        }
        
        // Render card body
        $html .= '<div class="card-body">';
        
        if ($this->title) {
            $html .= '<h3 class="card-title">' . Helpers::escape($this->title) . '</h3>';
        }
        
        if ($this->subtitle) {
            $html .= '<p class="card-subtitle">' . Helpers::escape($this->subtitle) . '</p>';
        }
        
        if ($this->content) {
            $html .= '<div class="card-content">' . $this->content . '</div>';
        }
        
        $html .= '</div>';
        
        // Render footer
        if ($this->footer || !empty($this->actions)) {
            $html .= '<div class="card-footer">';
            
            if ($this->footer) {
                $html .= $this->footer;
            }
            
            if (!empty($this->actions)) {
                $html .= $this->renderActions();
            }
            
            $html .= '</div>';
        }
        
        $html .= '</article>';
        
        return $html;
    }
    
    /**
     * Render card image
     * 
     * @return string Rendered HTML
     */
    protected function renderImage(): string {
        return '<div class="card-image">' .
               '<img src="' . Helpers::escape($this->image) . '" alt="' . 
               Helpers::escape($this->title ?? 'Card image') . '">' .
               '</div>';
    }
    
    /**
     * Render card actions
     * 
     * @return string Rendered HTML
     */
    protected function renderActions(): string {
        $html = '<div class="card-actions">';
        
        foreach ($this->actions as $action) {
            if ($action instanceof BaseComponent) {
                $html .= $action->render();
            } else {
                $html .= $action;
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Build card CSS classes
     * 
     * @return string CSS classes
     */
    protected function buildClasses(): string {
        $classes = ['card'];
        
        // Add variant class
        if ($this->variant !== self::VARIANT_DEFAULT) {
            $classes[] = 'card-' . $this->variant;
        }
        
        // Add hoverable class
        if ($this->hoverable) {
            $classes[] = 'card-hoverable';
        }
        
        // Add custom classes
        if (!empty($this->classes)) {
            $classes[] = $this->classes;
        }
        
        return implode(' ', $classes);
    }
}

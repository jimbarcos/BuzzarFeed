<?php
/*
PROGRAM NAME: Modal Component (Modal.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed UI Components library.
It provides a reusable modal/dialog component for displaying dynamic content, alerts, or forms in a pop-up overlay.

DATE CREATED: Decemeber 4, 2025
LAST MODIFIED: Decemeber 4, 2025

PURPOSE:
The purpose of this program is to:
- Render a flexible, reusable modal component for BuzzarFeed.
- Support configurable properties such as title, content, size, closable, header, footer, and actions.
- Maintain accessibility standards (ARIA attributes, focus handling).
- Follow ISO 9241 principles: Reusability and Accessibility.

DATA STRUCTURES:
- $title (string): Modal title.
- $content (string): Modal body content (HTML/text).
- $size (string): Modal size; can be 'sm', 'md', or 'lg'.
- $closable (bool): Whether modal can be closed via a button or backdrop click.
- $showHeader (bool): Whether to show the modal header.
- $showFooter (bool): Whether to show the modal footer.
- $footer (string|null): Optional footer content.
- $actions (array): Array of action buttons or components.
- $id (string): Modal DOM ID.
- $classes (string): Custom CSS classes.
- $attributes (array): Optional HTML attributes.

ALGORITHM / LOGIC:
1. initialize():
   - Extracts and sets component properties from $props.
2. render():
   - Generates the modal HTML structure including backdrop, header, body, and footer.
   - Uses unique ID if none provided.
3. renderHeader(string $modalId):
   - Generates header HTML with title and close button (if closable).
4. renderFooter():
   - Generates footer HTML and renders any action buttons/components.
5. buildClasses():
   - Builds CSS classes string based on size and custom classes.

NOTES:
- Designed to be reusable across BuzzarFeed application pages.
- Supports dynamic actions and footer content.
- Complies with ISO 9241 accessibility recommendations.
- Can be extended for specialized modal variants.
*/

namespace BuzzarFeed\Components\Common;

use BuzzarFeed\Components\BaseComponent;
use BuzzarFeed\Utils\Helpers;

class Modal extends BaseComponent {
    
    /**
     * Modal sizes
     */
    const SIZE_SMALL = 'sm';
    const SIZE_MEDIUM = 'md';
    const SIZE_LARGE = 'lg';
    
    /**
     * @var string Modal title
     */
    protected string $title = '';
    
    /**
     * @var string Modal content
     */
    protected string $content = '';
    
    /**
     * @var string Modal size
     */
    protected string $size = self::SIZE_MEDIUM;
    
    /**
     * @var bool Whether modal is closable
     */
    protected bool $closable = true;
    
    /**
     * @var bool Whether to show header
     */
    protected bool $showHeader = true;
    
    /**
     * @var bool Whether to show footer
     */
    protected bool $showFooter = false;
    
    /**
     * @var string|null Footer content
     */
    protected ?string $footer = null;
    
    /**
     * @var array Modal actions (buttons)
     */
    protected array $actions = [];
    
    /**
     * Initialize component
     */
    protected function initialize(): void {
        parent::initialize();
        
        $this->title = $this->prop('title', '');
        $this->content = $this->prop('content', '');
        $this->size = $this->prop('size', self::SIZE_MEDIUM);
        $this->closable = $this->prop('closable', true);
        $this->showHeader = $this->prop('showHeader', true);
        $this->showFooter = $this->prop('showFooter', false);
        $this->footer = $this->prop('footer');
        $this->actions = $this->prop('actions', []);
    }
    
    /**
     * Render the modal
     * 
     * @return string Rendered HTML
     */
    public function render(): string {
        $modalId = $this->id ?: 'modal-' . uniqid();
        $classes = $this->buildClasses();
        
        $html = '<div class="' . Helpers::escape($classes) . '" id="' . 
                Helpers::escape($modalId) . '" role="dialog" aria-modal="true">';
        
        $html .= '<div class="modal-backdrop" onclick="closeModal(\'' . 
                 Helpers::escape($modalId) . '\')"></div>';
        
        $html .= '<div class="modal-content">';
        
        // Render header
        if ($this->showHeader) {
            $html .= $this->renderHeader($modalId);
        }
        
        // Render body
        $html .= '<div class="modal-body">';
        $html .= $this->content;
        $html .= '</div>';
        
        // Render footer
        if ($this->showFooter || !empty($this->actions)) {
            $html .= $this->renderFooter();
        }
        
        $html .= '</div></div>';
        
        return $html;
    }
    
    /**
     * Render modal header
     * 
     * @param string $modalId Modal ID
     * @return string Rendered HTML
     */
    protected function renderHeader(string $modalId): string {
        $html = '<div class="modal-header">';
        $html .= '<h3 class="modal-title">' . Helpers::escape($this->title) . '</h3>';
        
        if ($this->closable) {
            $html .= '<button type="button" class="modal-close" ' .
                     'onclick="closeModal(\'' . Helpers::escape($modalId) . '\')" ' .
                     'aria-label="Close">' .
                     '<i class="fas fa-times"></i>' .
                     '</button>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render modal footer
     * 
     * @return string Rendered HTML
     */
    protected function renderFooter(): string {
        $html = '<div class="modal-footer">';
        
        if ($this->footer) {
            $html .= $this->footer;
        }
        
        if (!empty($this->actions)) {
            foreach ($this->actions as $action) {
                if ($action instanceof BaseComponent) {
                    $html .= $action->render();
                } else {
                    $html .= $action;
                }
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Build modal CSS classes
     * 
     * @return string CSS classes
     */
    protected function buildClasses(): string {
        $classes = ['modal'];
        
        // Add size class
        if ($this->size !== self::SIZE_MEDIUM) {
            $classes[] = 'modal-' . $this->size;
        }
        
        // Add custom classes
        if (!empty($this->classes)) {
            $classes[] = $this->classes;
        }
        
        return implode(' ', $classes);
    }
}

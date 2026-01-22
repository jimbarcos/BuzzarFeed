<?php
/*
PROGRAM NAME: Input Component (Input.php)

PROGRAMMER: Frontend Team

SYSTEM CONTEXT:
This module is part of the BuzzarFeed UI Components library.
It provides a reusable form input component for text, email, password, number, tel, URL, date, and textarea fields.

DATE CREATED: Decemeber 4, 2025
LAST MODIFIED: Decemeber 4, 2025

PURPOSE:
The purpose of this program is to:
- Render a flexible, reusable input or textarea field with optional label, help text, and error message.
- Support input types including text, email, password (with optional toggle), number, tel, URL, date, and textarea.
- Maintain accessibility standards (label association, ARIA-friendly attributes).
- Follow ISO 9241 principles: Reusability and Accessibility.

DATA STRUCTURES:
- $type (string): Input type (text, email, password, number, tel, URL, date, textarea).
- $name (string): Input name attribute.
- $value (string): Current value of the input.
- $placeholder (string): Placeholder text.
- $label (string|null): Optional label text.
- $required (bool): Whether the input is required.
- $disabled (bool): Whether the input is disabled.
- $error (string|null): Error message for validation feedback.
- $help (string|null): Help text for guidance.
- $showPasswordToggle (bool): Whether to show password visibility toggle.
- $id (string): Input DOM ID.
- $classes (string): Custom CSS classes.
- $attributes (array): Optional HTML attributes.

ALGORITHM / LOGIC:
1. initialize():
   - Extracts and sets component properties from $props.
   - Ensures backward compatibility for 'showToggle' and 'showPasswordToggle'.
   - Auto-generates ID if needed for password toggle functionality.
2. render():
   - Builds HTML for label, input/textarea, password toggle, error message, and help text.
3. renderInput():
   - Generates HTML for standard input elements with proper attributes.
4. renderTextarea():
   - Generates HTML for textarea elements with proper attributes.
5. renderLabel():
   - Generates HTML for associated label element.
6. renderPasswordToggle():
   - Generates button HTML for toggling password visibility.

NOTES:
- Designed to be reusable across all BuzzarFeed forms.
- Supports dynamic customization through props and attributes.
- Complies with ISO 9241 accessibility and usability guidelines.
- Can be extended for specialized input types or validation behaviors.
*/

namespace BuzzarFeed\Components\Common;

use BuzzarFeed\Components\BaseComponent;
use BuzzarFeed\Utils\Helpers;

class Input extends BaseComponent {
    
    /**
     * Input types
     */
    const TYPE_TEXT = 'text';
    const TYPE_EMAIL = 'email';
    const TYPE_PASSWORD = 'password';
    const TYPE_NUMBER = 'number';
    const TYPE_TEL = 'tel';
    const TYPE_URL = 'url';
    const TYPE_DATE = 'date';
    const TYPE_TEXTAREA = 'textarea';
    
    /**
     * @var string Input type
     */
    protected string $type = self::TYPE_TEXT;
    
    /**
     * @var string Input name
     */
    protected string $name = '';
    
    /**
     * @var string Input value
     */
    protected string $value = '';
    
    /**
     * @var string Input placeholder
     */
    protected string $placeholder = '';
    
    /**
     * @var string|null Input label
     */
    protected ?string $label = null;
    
    /**
     * @var bool Whether input is required
     */
    protected bool $required = false;
    
    /**
     * @var bool Whether input is disabled
     */
    protected bool $disabled = false;
    
    /**
     * @var string|null Error message
     */
    protected ?string $error = null;
    
    /**
     * @var string|null Help text
     */
    protected ?string $help = null;
    
    /**
     * @var bool Whether to show password toggle
     */
    protected bool $showPasswordToggle = false;
    
    /**
     * Initialize component
     */
    protected function initialize(): void {
        parent::initialize();
        
        $this->type = $this->prop('type', self::TYPE_TEXT);
        $this->name = $this->prop('name', '');
        $this->value = $this->prop('value', '');
        $this->placeholder = $this->prop('placeholder', '');
        $this->label = $this->prop('label');
        $this->required = $this->prop('required', false);
        $this->disabled = $this->prop('disabled', false);
        $this->error = $this->prop('error');
        $this->help = $this->prop('help');
        
        // Accept both 'showToggle' and 'showPasswordToggle' for backward compatibility
        $this->showPasswordToggle = $this->prop('showPasswordToggle', $this->prop('showToggle', false));
        
        // Auto-generate ID if not provided and showPasswordToggle is true
        if ($this->showPasswordToggle && empty($this->id)) {
            $this->id = $this->name;
        }
    }
    
    /**
     * Render the input
     * 
     * @return string Rendered HTML
     */
    public function render(): string {
        $html = '<div class="form-group">';
        
        // Render label
        if ($this->label) {
            $html .= $this->renderLabel();
        }
        
        // Render input wrapper
        if ($this->type === self::TYPE_PASSWORD && $this->showPasswordToggle) {
            $html .= '<div class="password-wrapper">';
        }
        
        // Render input
        $html .= $this->type === self::TYPE_TEXTAREA 
            ? $this->renderTextarea() 
            : $this->renderInput();
        
        // Render password toggle
        if ($this->type === self::TYPE_PASSWORD && $this->showPasswordToggle) {
            $html .= $this->renderPasswordToggle();
            $html .= '</div>';
        }
        
        // Render error message
        if ($this->error) {
            $html .= '<span class="form-error">' . Helpers::escape($this->error) . '</span>';
        }
        
        // Render help text
        if ($this->help) {
            $html .= '<span class="form-help">' . Helpers::escape($this->help) . '</span>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render input element
     * 
     * @return string Rendered HTML
     */
    protected function renderInput(): string {
        $classes = 'form-input';
        if ($this->error) {
            $classes .= ' form-input-error';
        }
        if (!empty($this->classes)) {
            $classes .= ' ' . $this->classes;
        }
        
        $attributes = [];
        $attributes[] = 'type="' . Helpers::escape($this->type) . '"';
        $attributes[] = 'name="' . Helpers::escape($this->name) . '"';
        $attributes[] = 'class="' . Helpers::escape($classes) . '"';
        
        if (!empty($this->id)) {
            $attributes[] = 'id="' . Helpers::escape($this->id) . '"';
        }
        
        if (!empty($this->value)) {
            $attributes[] = 'value="' . Helpers::escape($this->value) . '"';
        }
        
        if (!empty($this->placeholder)) {
            $attributes[] = 'placeholder="' . Helpers::escape($this->placeholder) . '"';
        }
        
        if ($this->required) {
            $attributes[] = 'required';
        }
        
        if ($this->disabled) {
            $attributes[] = 'disabled';
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
        
        return "<input {$attrs}>";
    }
    
    /**
     * Render textarea element
     * 
     * @return string Rendered HTML
     */
    protected function renderTextarea(): string {
        $classes = 'form-input';
        if ($this->error) {
            $classes .= ' form-input-error';
        }
        if (!empty($this->classes)) {
            $classes .= ' ' . $this->classes;
        }
        
        $attributes = [];
        $attributes[] = 'name="' . Helpers::escape($this->name) . '"';
        $attributes[] = 'class="' . Helpers::escape($classes) . '"';
        
        if (!empty($this->id)) {
            $attributes[] = 'id="' . Helpers::escape($this->id) . '"';
        }
        
        if (!empty($this->placeholder)) {
            $attributes[] = 'placeholder="' . Helpers::escape($this->placeholder) . '"';
        }
        
        if ($this->required) {
            $attributes[] = 'required';
        }
        
        if ($this->disabled) {
            $attributes[] = 'disabled';
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
        
        return "<textarea {$attrs}>" . Helpers::escape($this->value) . "</textarea>";
    }
    
    /**
     * Render label element
     * 
     * @return string Rendered HTML
     */
    protected function renderLabel(): string {
        $for = !empty($this->id) ? ' for="' . Helpers::escape($this->id) . '"' : '';
        return '<label class="form-label"' . $for . '>' . Helpers::escape($this->label) . '</label>';
    }
    
    /**
     * Render password toggle button
     * 
     * @return string Rendered HTML
     */
    protected function renderPasswordToggle(): string {
        $inputId = $this->id ?: $this->name;
        return '<button type="button" class="password-toggle" onclick="togglePassword(\'' . 
               Helpers::escape($inputId) . '\')"><i class="fas fa-eye-slash" id="' . 
               Helpers::escape($inputId) . '-icon"></i></button>';
    }
}

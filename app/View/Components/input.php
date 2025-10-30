<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public string $name;
    public string $label;
    public string $type;
    public string $value;
    public string $placeholder;
    public ?string $icon;
    public bool $required;
    public ?string $id;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name = '',
        string $label = '',
        string $type = 'text',
        string $value = '',
        string $placeholder = '',
        ?string $icon = null,
        bool $required = false,
        ?string $id = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->icon = $icon;
        $this->required = $required;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input');
    }
}
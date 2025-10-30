<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $name;
    public string $label;
    public string $value;
    public array $options;
    public string $placeholder;
    public bool $required;
    public ?string $id;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name = '',
        string $label = '',
        string $value = '',
        array $options = [],
        string $placeholder = '',
        bool $required = false,
        ?string $id = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->options = $options;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select');
    }
}
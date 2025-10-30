<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public string $name;
    public string $label;
    public string $value;
    public string $placeholder;
    public int $rows;
    public bool $required;
    public ?string $id;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name = '',
        string $label = '',
        string $value = '',
        string $placeholder = '',
        int $rows = 4,
        bool $required = false,
        ?string $id = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->rows = $rows;
        $this->required = $required;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.textarea');
    }
}
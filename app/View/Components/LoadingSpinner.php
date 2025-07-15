<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LoadingSpinner extends Component
{
    public string $size;
    public string $message;
    public bool $overlay;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $size = 'medium',
        string $message = 'Chargement...',
        bool $overlay = false
    ) {
        $this->size = $this->getSizeClasses($size);
        $this->message = $message;
        $this->overlay = $overlay;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.loading-spinner', [
            'message' => $this->message,
            'size' => $this->size,
            'overlay' => $this->overlay
        ]);
    }

    /**
     * Get spinner size classes
     */
    public function getSizeClasses(string $size): string
    {
        return match ($size) {
            'small' => 'w-4 h-4',
            'medium' => 'w-8 h-8',
            'large' => 'w-12 h-12',
            'xlarge' => 'w-16 h-16',
            default => 'w-8 h-8'
        };
    }
}

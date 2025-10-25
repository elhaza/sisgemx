<?php

namespace App\View\Components;

use App\Models\Settings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppLogo extends Component
{
    public ?string $logoPath;

    public ?string $fallbackImage;

    public ?string $height;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $fallbackImage = null, ?string $height = 'h-10')
    {
        $this->logoPath = Settings::getLogo();
        $this->fallbackImage = $fallbackImage;
        $this->height = $height;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.app-logo');
    }
}

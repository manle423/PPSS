<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\StoreInfo;
class Footer extends Component
{
    public $storeInfo;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
        $this->storeInfo = StoreInfo::firstOrFail();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.footer');
    }
}

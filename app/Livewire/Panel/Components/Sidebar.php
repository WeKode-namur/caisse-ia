<?php

namespace App\Livewire\Panel\Components;

use Livewire\Component;

class Sidebar extends Component
{
    public function render()
    {
        return view('panel.components.sidebar')->with([
            'refreshIcons' => true
        ]);
    }
}

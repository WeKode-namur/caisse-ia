<?php

namespace App\Livewire\Panel\Components;

use Livewire\Component;

class NavigationMenu extends Component
{
    public function toggleSidebar()
    {
        $this->dispatch('toggle-sidebar');
    }
    public function render()
    {
        return view('panel.components.navigation-menu');
    }
}

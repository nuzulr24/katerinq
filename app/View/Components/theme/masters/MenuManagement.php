<?php

namespace App\View\Components\theme\masters\MenuManagement;

use Illuminate\View\Component;

class MenuManagement extends Component
{
    
    /**
     * Creates a new instance of the class.
     *
     * @param mixed $role The role parameter.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.theme.pages.menu-management')
        ->with('is_role', user()['level']);
    }
}

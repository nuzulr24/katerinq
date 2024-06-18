<?php

namespace App\View\Components\theme\auth;

use Illuminate\View\Component;

class Authentication__Header extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $data;
    
    public function __construct()
    {
        $this->data = $data;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.theme.auth.authentication__-header');
    }
}

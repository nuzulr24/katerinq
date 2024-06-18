<?php

namespace App\View\Components\theme\masters\Setting;

use Illuminate\View\Component;

class Setting extends Component
{

    public $activeSetting;
    
    public function __construct($activeSetting)
    {
        $this->activeSetting = $activeSetting;
    }

    public function render()
    {
        return view('components.theme.pages.account-preference');
    }
}
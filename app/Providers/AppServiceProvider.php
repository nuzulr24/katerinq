<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

// model binding
use App\Models\Seller;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
        Blade::directive('canSell', function () {
            return "<?php if (App\Models\Seller::where('user_id', Auth::id())->exists()): ?>";
        });
    
        Blade::directive('elseCanSell', function () {
            return '<?php else: ?>';
        });
    
        Blade::directive('endCanSell', function () {
            return '<?php endif; ?>';
        });
    }
}

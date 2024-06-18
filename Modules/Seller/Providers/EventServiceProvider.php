<?php

namespace Modules\Seller\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Modules\Seller\Events\TicketCreated;
use Modules\Seller\Listeners\TicketCreatedListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TicketCreated::class => [
            TicketCreatedListener::class,
        ],
    ];
}

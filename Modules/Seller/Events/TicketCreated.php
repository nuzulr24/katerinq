<?php

namespace Modules\Seller\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Seller\Entities\TicketResponseModel;

class TicketCreated
{
    use Dispatchable, SerializesModels;
    public $ticketResponse;

    public function __construct(TicketResponseModel $ticketResponse)
    {
        $this->ticketResponse = $ticketResponse;
        dd($ticketResponse);
    }
}
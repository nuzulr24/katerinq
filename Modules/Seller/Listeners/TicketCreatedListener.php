<?php

namespace Modules\Seller\Listeners;

use Modules\Seller\Events\TicketCreated;
use Modules\Seller\Entities\TicketResponseModel;
use Illuminate\Support\Str;

class TicketCreatedListener
{
    public function handle(TicketCreated $event)
    {
        $ticket = $event->ticketResponse;

        // Simpan data ke dalam TicketResponseModel
        $ticketResponse = new TicketResponseModel([
            'id' => Str::uuid(), // Generate UUID baru
            'id_ticket' => $ticket->id, // Gunakan ID tiket yang baru saja dibuat
            'user_id' => $ticket->user_id, // Isi dengan nilai yang sesuai
            'message' => $ticket->message, // Sesuaikan dengan data yang diperlukan
        ]);

        $ticketResponse->save();
    }
}
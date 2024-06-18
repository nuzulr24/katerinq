<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\User\Entities\OrderModel as Order;
use App\Enums\GlobalEnum as Status;

class OrdersCommand extends Command
{
    protected $signature = 'orders:update';
    protected $description = 'Update status in orders every minutes';
    
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $orders = Order::where('is_status', Status::isOrderRequested)->get(); // Sesuaikan dengan logika pengambilan data
        $currentTimestamp = time(); // Get the current timestamp
        
        foreach ($orders as $order) {
            $createdAtTimestamp = strtotime($order->created_at);
            
            // Add 3 days to the creation date timestamp
            $expiryTimestamp = $createdAtTimestamp + (3 * 24 * 60 * 60); // 3 days in seconds
            
            // Compare the expiry timestamp with the current timestamp
            $isExpired = $expiryTimestamp < $currentTimestamp;
            $expired = $isExpired ? true : false;
            
            if($expired == true) {
                $findOrder = Order::find($order->id);
                $findOrder->is_status = Status::isOrderCancelled;
                $findOrder->last_buyer_message = 'Dibatalkan secara otomatis oleh sistem karena melebihi batas waktu 3 hari reseller tidak menanggapi permintaan pesanan';
                $findOrder->cancel_reason = 'Dibatalkan secara otomatis oleh sistem karena melebihi batas waktu 3 hari reseller tidak menanggapi permintaan pesanan';
                
                // save
                $findOrder->save();
            }
        }

        $this->info('Status updated successfully!');
    }
}

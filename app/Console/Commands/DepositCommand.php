<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Seller\Entities\PaymentModel as Payment;
use App\Enums\GlobalEnum as Status;

class DepositCommand extends Command
{
    protected $signature = 'deposit:update';
    protected $description = 'Update status in deposit every minutes';
    
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $orders = Payment::where('is_status', Status::isDepositPending)->get(); // Sesuaikan dengan logika pengambilan data
        $currentTimestamp = time(); // Get the current timestamp
        
        foreach ($orders as $order) {
            $createdAtTimestamp = strtotime($order->created_at);
            
            // Add 3 days to the creation date timestamp
            $expiryTimestamp = $createdAtTimestamp + (3 * 24 * 60 * 60); // 3 days in seconds
            
            // Compare the expiry timestamp with the current timestamp
            $isExpired = $expiryTimestamp < $currentTimestamp;
            $expired = $isExpired ? true : false;
            
            if($expired == true) {
                $findOrder = Payment::find($order->id);
                $findOrder->is_status = Status::isDepositCancel;
                
                // save
                $findOrder->save();
            }
        }

        $this->info('Status Expired updated successfully!');
    }
}

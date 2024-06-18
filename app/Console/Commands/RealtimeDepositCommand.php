<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Seller\Entities\PaymentModel as Payment;
use Modules\Seller\Entities\AccountModel as User;
use App\Enums\GlobalEnum as Status;

class RealtimeDepositCommand extends Command
{
    protected $signature = 'realtime-deposit:update';
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
            $merchantCode = app_info('duitku_merchant'); 
            $apiKey = app_info('duitku_client');
            $merchantOrderId = $order->deposit_number; // dari anda (merchant), bersifat unik
            $signature = md5($merchantCode . $merchantOrderId . $apiKey);
        
            $params = array(
                'merchantCode' => $merchantCode,
                'merchantOrderId' => $merchantOrderId,
                'signature' => $signature
            );
        
            $params_string = json_encode($params);
            if(app_info('duitku_sandbox') == 1) {
                $url = 'https://sandbox.duitku.com/webapi/api/merchant/transactionStatus'; // Sandbox
            } else {
                $url = 'https://passport.duitku.com/webapi/api/merchant/transactionStatus'; // Production
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($params_string))                                                                       
            );   
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
            //execute post
            $request = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
            if($httpCode == 200)
            {
                $results = json_decode($request, true);
                if($results['statusCode'] == 02) {
                    $findOrder = Payment::find($order->id);
                    $findOrder->is_status = Status::isDepositCancel;
                    $findOrder->urlRedirect = NULL;
                    
                    // save
                    $findOrder->save();
                } elseif($results['statusCode'] == 00) {
                    $findOrder = Payment::find($order->id);
                    $findOrder->is_status = Status::isDepositPaid;
                    $findOrder->urlRedirect = NULL;
                    
                    $findUser = User::where('id', $order->user_id)->first();
                    $findUser->balance = ($findUser->balance + $order->amount);
                    
                    // save
                    $findOrder->save();
                    $findUser->save();
                }
            }
        }

        $this->info('Status updated successfully!');
    }
}

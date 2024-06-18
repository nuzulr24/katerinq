<?php

namespace App\Providers;

use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Mail;

class AppMailServiceProvider extends MailServiceProvider
{
    public function boot()
    {
        // Retrieve mail configuration from the database
        $mailConfiguration = Mail::find(1);

        if (!is_null($mailConfiguration)) {
            $config = [
                'driver' => 'smtp',
                'host' => $mailConfiguration->host,
                'port' => $mailConfiguration->port,
                'username' => $mailConfiguration->username,
                'password' => $mailConfiguration->password, // Decrypt the stored password
                'encryption' => $mailConfiguration->protocol,
                'from' => ['address' => $mailConfiguration->username, 'name' => $mailConfiguration->name],
            ];

            // Set the mail configuration
            Config::set('mail', $config);
        }
    }
}

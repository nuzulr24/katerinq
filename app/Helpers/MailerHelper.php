<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Mail;

class MailerHelper
{
    /**
     * Sends an email to the specified email address.
     *
     * @param string $email The email address to send the email to.
     * @param bool $plain Whether to send the email as plain text or not.
     * @param string|null $view The view to use for the email content.
     * @param array $data Additional data to pass to the email view.
     * @throws \Exception If an error occurs while sending the email.
     * @return bool Whether the email was sent successfully or not.
     */
    public static function to($recipientEmail, $plain = false, $view = null, $data = [])
    {
        $message = $data['message'];
        $subject = $data['subject'];
        switch($plain)
        {
            case true:
                try {
                    Mail::raw($message, function ($message) use ($recipientEmail, $subject) {
                        $message->to($recipientEmail)->subject($subject);
                    });
                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            break;

            case false;
                try {
                    Mail::send($view, ['data' => $data], function ($message) use ($recipientEmail, $subject) {
                        $message->to($recipientEmail)
                            ->subject($subject);
                    });
                    return true;
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            break;
        }
    }
}

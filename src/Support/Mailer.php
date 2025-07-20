<?php

namespace App\Support;

use Postmark\PostmarkClient;

class Mailer
{
    private PostmarkClient $client;
    private string $from;

    public function __construct()
    {
        $token = config('email.postmark_token');
        $this->from = config('email.from_email');
        $this->client = new PostmarkClient($token);
    }

    public function send(string $to, string $subject, string $message): bool
    {
        try {
            $this->client->sendEmail(
                $this->from,
                $to,
                $subject,
                $message
            );
            return true;
        } catch (\Throwable $e) {
            Logger::error("Postmark Error: " . $e->getMessage());
            return false;
        }
    }
}

<?php

namespace App\Actions\Messages;

use App\Exceptions\ApiException;
use App\Models\Message;
use Lorisleiva\Actions\Concerns\AsAction;

class MarkMessageReadAction
{
    use AsAction;

    public function handle(Message $message, bool $failIfAlreadyRead = true): Message
    {
        if ($message->read_at !== null && $failIfAlreadyRead) {
            throw ApiException::conflict('Message is already read.');
        }

        if ($message->read_at === null) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return $message;
    }
}

<?php

namespace App\Actions\Messages;

use App\Exceptions\ApiException;
use App\Models\Message;
use Lorisleiva\Actions\Concerns\AsAction;

class MarkMessageUnreadAction
{
    use AsAction;

    public function handle(Message $message): Message
    {
        if ($message->read_at === null) {
            throw ApiException::conflict('Message is already unread.');
        }

        $message->forceFill(['read_at' => null])->save();

        return $message;
    }
}

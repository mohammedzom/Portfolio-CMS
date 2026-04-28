<?php

namespace App\Actions\Messages;

use App\Models\Message;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreMessageAction
{
    use AsAction;

    public function handle(Message $message): Message
    {
        $message->restore();

        return $message;
    }
}

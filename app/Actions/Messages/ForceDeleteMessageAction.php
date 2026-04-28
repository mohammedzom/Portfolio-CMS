<?php

namespace App\Actions\Messages;

use App\Models\Message;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteMessageAction
{
    use AsAction;

    public function handle(Message $message): void
    {
        $message->forceDelete();
    }
}

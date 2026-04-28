<?php

namespace App\Actions\Messages;

use App\Models\Message;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreMessageAction
{
    use AsAction;

    public function handle(array $data): Message
    {
        return Message::create($data);
    }
}

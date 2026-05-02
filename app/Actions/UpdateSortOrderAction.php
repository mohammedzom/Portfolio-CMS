<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSortOrderAction
{
    use AsAction;

    public function handle(string $modelClass, array $items): void
    {
        DB::transaction(function () use ($modelClass, $items) {
            foreach ($items as $item) {
                $modelClass::where('id', $item['id'])->update([
                    'sort_order' => $item['sort_order'],
                ]);
            }
        });
    }
}

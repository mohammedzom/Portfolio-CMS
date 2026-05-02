<?php

namespace App\Actions\Dashboard;

use App\Models\Visit;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAnalyticsAction
{
    use AsAction;

    public function handle(): array
    {
        $stats = Visit::selectRaw('visited_at, count(*) as count')
            ->where('visited_at', '>=', now()->subDays(30))
            ->groupBy('visited_at')
            ->orderBy('visited_at', 'asc')
            ->get();

        return [
            'total_visits' => Visit::count(),
            'chart_data' => $stats,
        ];
    }
}

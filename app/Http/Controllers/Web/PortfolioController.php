<?php

namespace App\Http\Controllers\Web;

use App\Actions\Portfolio\BuildPortfolioPageAction;
use App\Actions\Portfolio\GetPortfolioDataAction;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * The public portfolio website.
 *
 * Reads the same cached payload as GET /api/v1/portfolio through the shared
 * action, so the website never calls the REST API or needs its API key.
 */
class PortfolioController
{
    public function __invoke(GetPortfolioDataAction $getPortfolioData, BuildPortfolioPageAction $buildPortfolioPage): View
    {
        try {
            $portfolio = $getPortfolioData->handle();
        } catch (ModelNotFoundException) {
            // The site settings have not been created yet: render the page with its empty states.
            $portfolio = [];
        }

        return view('portfolio.index', ['page' => $buildPortfolioPage->handle($portfolio)]);
    }
}

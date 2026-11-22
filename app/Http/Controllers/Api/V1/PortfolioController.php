<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Portfolio\GetPortfolioDataAction;
use App\Http\Controllers\Api\Controller;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    public function index(GetPortfolioDataAction $getPortfolioData): JsonResponse
    {
        return $this->successResponse($getPortfolioData->handle(), 'Portfolio Data Retrieved Successfully.');
    }
}

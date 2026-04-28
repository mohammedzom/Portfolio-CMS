<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Services\DestroyServiceAction;
use App\Actions\Services\ForceDeleteServiceAction;
use App\Actions\Services\RestoreServiceAction;
use App\Actions\Services\StoreServiceAction;
use App\Actions\Services\UpdateServiceAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::query();
        $cache_key = 'services';

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
            $cache_key .= '_archived';
        }

        if ($request->filled('search')) {
            $cache_key = null;
            $search = $request->string('search')->toString();
            $query->where(function ($query) use ($search): void {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);
        if ($cache_key) {
            $services = Cache::remember($cache_key, $ttl, fn () => $this->resolveForCache(ServiceResource::collection($query->orderBy('sort_order')->get())));
        } else {
            $services = $this->resolveForCache(ServiceResource::collection($query->orderBy('sort_order')->get()));
        }

        return $this->successResponse(
            $services,
            'Services fetched successfully.'
        );
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = StoreServiceAction::run($request->validated());

        return $this->successResponse(
            new ServiceResource($service),
            'Service created successfully.',
            201
        );
    }

    public function show(Service $service): JsonResponse
    {
        return $this->successResponse(
            new ServiceResource($service),
            'Service fetched successfully.'
        );
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service = UpdateServiceAction::run($service, $request->validated());

        return $this->successResponse(
            new ServiceResource($service),
            'Service updated successfully.'
        );
    }

    public function destroy(Service $service): JsonResponse
    {
        DestroyServiceAction::run($service);

        return $this->successResponse(
            [],
            'Service Archived successfully.'
        );
    }

    public function restore(Service $service): JsonResponse
    {
        $service = RestoreServiceAction::run($service);

        return $this->successResponse(
            new ServiceResource($service),
            'Service restored successfully.'
        );
    }

    public function forceDelete(Service $service): JsonResponse
    {
        ForceDeleteServiceAction::run($service);

        return $this->successResponse(
            [],
            'Service deleted permanently.'
        );
    }
}

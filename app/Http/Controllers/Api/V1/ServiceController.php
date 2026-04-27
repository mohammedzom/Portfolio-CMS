<?php

namespace App\Http\Controllers\Api\V1;

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
        $cache_key = 'portfolio_services';
        if ($request->has('archived') && $request->input('archived') == true) {
            $query->onlyTrashed();
        }
        if ($request->filled('search')) {
            $cache_key = null;
            $query->where('title', 'like', '%'.$request->search.'%');
            $query->orWhere('description', 'like', '%'.$request->search.'%');
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
        $service = Service::create($request->validated());
        Cache::forget('portfolio_services');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ServiceResource($service),
            'Service created successfully.',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $service = Service::withoutTrashed()->findOrFail($id);

        return $this->successResponse(
            new ServiceResource($service),
            'Service fetched successfully.'
        );
    }

    public function update(UpdateServiceRequest $request, string $id): JsonResponse
    {
        $service = Service::withoutTrashed()->findOrFail($id);

        $service->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order,
            'tags' => $request->tags,
        ]);
        Cache::forget('portfolio_services');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ServiceResource($service),
            'Service updated successfully.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $service = Service::withoutTrashed()->findOrFail($id);
        $service->delete();
        Cache::forget('portfolio_services');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Service Archived successfully.'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $service = Service::onlyTrashed()->findOrFail($id);
        $service->restore();
        Cache::forget('portfolio_services');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ServiceResource($service),
            'Service restored successfully.'
        );
    }

    public function forceDelete(string $id): JsonResponse
    {
        $service = Service::withTrashed()->findOrFail($id);
        $service->forceDelete();

        Cache::forget('portfolio_services');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Service deleted successfully.'
        );
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query();
        if (request()->has('archived') && request('archived')) {
            $services->onlyTrashed();
        } else {
            $services->withoutTrashed();
        }
        if (request('search')) {
            $services->where('title', 'like', '%'.request('search').'%');
            $services->orWhere('description', 'like', '%'.request('search').'%');
        }
        $services = $services->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'message' => 'Services fetched successfully',
            'data' => ServiceResource::collection($services),
        ]);
    }

    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => new ServiceResource($service),
        ]);
    }

    public function show(Service $service)
    {
        return response()->json([
            'success' => true,
            'message' => 'Service fetched successfully',
            'data' => new ServiceResource($service),
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => new ServiceResource($service),
        ]);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service Archived successfully',
            'data' => [],
        ]);
    }

    public function restore(Service $service)
    {
        $service->restore();

        return response()->json([
            'success' => true,
            'message' => 'Service restored successfully',
            'data' => new ServiceResource($service),
        ]);
    }
}

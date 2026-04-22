<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::query();
        if ($request->has('archived') && $request->archived) {
            $services->onlyTrashed();
        } else {
            $services->withoutTrashed();
        }
        if ($request->filled('search')) {
            $services->where('title', 'like', '%'.$request->search.'%');
            $services->orWhere('description', 'like', '%'.$request->search.'%');
        }
        $services = $services->orderBy('sort_order')->get();

        return $this->successResponse(
            ServiceResource::collection($services),
            'Services fetched successfully.'
        );
    }

    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->validated());

        return $this->successResponse(
            new ServiceResource($service),
            'Service created successfully.',
            201
        );
    }

    public function show(string $id)
    {
        $service = Service::withoutTrashed()->findOrFail($id);

        return $this->successResponse(
            new ServiceResource($service),
            'Service fetched successfully.'
        );
    }

    public function update(UpdateServiceRequest $request, string $id)
    {
        $service = Service::withoutTrashed()->findOrFail($id);

        $service->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order,
            'tags' => $request->tags,
        ]);

        return $this->successResponse(
            new ServiceResource($service),
            'Service updated successfully.'
        );
    }

    public function destroy(string $id)
    {
        $service = Service::withoutTrashed()->findOrFail($id);
        $service->delete();

        return $this->successResponse(
            [],
            'Service Archived successfully.'
        );
    }

    public function restore(string $id)
    {
        $service = Service::onlyTrashed()->findOrFail($id);
        $service->restore();

        return $this->successResponse(
            new ServiceResource($service),
            'Service restored successfully.'
        );
    }

    public function forceDelete(string $id)
    {
        $service = Service::withTrashed()->findOrFail($id);
        $service->forceDelete();

        return $this->successResponse(
            [],
            'Service deleted successfully.'
        );
    }
}

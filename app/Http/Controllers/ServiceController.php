<?php

namespace App\Http\Controllers;

use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
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
        $services = $services->orderBy('sort_order')->paginate(10);

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(StoreServiceRequest $request)
    {
        Service::create($request->validated());

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully');
    }

    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully');
    }

    public function restore(Service $service)
    {
        $service->restore();

        return redirect()->route('admin.services.index')->with('success', 'Service restored successfully');
    }
}

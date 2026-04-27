<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ManageSoftDeletes
{
    public function restore(string $id): JsonResponse
    {
        $model = $this->modelClass::onlyTrashed()->findOrFail($id);
        $model->restore();
        if (method_exists($this, 'afterRestore')) {
            $this->afterRestore($model);
        }

        return $this->successResponse(
            new ($this->resourceClass)($model),
            class_basename($this->modelClass).' restored successfully.'
        );
    }

    public function forceDelete(string $id): JsonResponse
    {
        $model = $this->modelClass::withTrashed()->findOrFail($id);

        if (method_exists($this, 'beforeForceDelete')) {
            $this->beforeForceDelete($model);
        }

        $model->forceDelete();

        if (method_exists($this, 'afterForceDelete')) {
            $this->afterForceDelete($model);
        }

        return $this->successResponse(
            [],
            class_basename($this->modelClass).' deleted permanently.'
        );
    }
}

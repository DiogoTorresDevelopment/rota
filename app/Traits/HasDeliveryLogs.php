<?php

namespace App\Traits;

use App\Models\DeliveryLog;
use Illuminate\Support\Facades\Auth;

trait HasDeliveryLogs
{
    public function logAction($actionType, $entityType, $entityId = null, $oldData = null, $newData = null, $description = null)
    {
        return DeliveryLog::create([
            'delivery_id' => $this->id,
            'user_id' => Auth::id(),
            'action_type' => $actionType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function logs()
    {
        return $this->hasMany(DeliveryLog::class);
    }

    protected static function bootHasDeliveryLogs()
    {
        static::created(function ($model) {
            $model->logAction(
                'created',
                class_basename($model),
                $model->id,
                null,
                $model->toArray(),
                'Registro criado'
            );
        });

        static::updated(function ($model) {
            $model->logAction(
                'updated',
                class_basename($model),
                $model->id,
                $model->getOriginal(),
                $model->getChanges(),
                'Registro atualizado'
            );
        });

        static::deleted(function ($model) {
            $model->logAction(
                'deleted',
                class_basename($model),
                $model->id,
                $model->toArray(),
                null,
                'Registro excluído'
            );
        });
    }
} 
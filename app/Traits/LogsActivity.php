<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function logActivity($action, $model, $oldValues = null, $newValues = null, $description = null)
    {
        // Get model name for display
        $modelName = '';
        $facilityType = '';
        $modelId = '';
        
        if ($model instanceof \App\Models\Faskes) {
            $modelName = $model->nama;
            $facilityType = $model->fasilitas;
            $modelId = $model->id;
        } elseif ($model instanceof \App\Models\Apoteks) {
            $modelName = $model->nama_apotek;
            $facilityType = 'Apotek';
            $modelId = $model->id_apotek;
        } elseif ($model instanceof \App\Models\Kliniks) {
            $modelName = $model->nama_klinik;
            $facilityType = 'Klinik';
            $modelId = $model->id_klinik;
        }
        
        // Create activity log
        ActivityLog::create([
            'user_id' => auth()->id() ?? 'admin',
            'user_name' => auth()->user()->name ?? 'Admin',
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $modelId,
            'model_name' => $modelName,
            'facility_type' => $facilityType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description
        ]);
    }
    
    /**
     * Log create activity
     */
    public static function logCreate($model, $description = null)
    {
        self::logActivity('create', $model, null, $model->toArray(), $description);
    }
    
    /**
     * Log update activity
     */
    public static function logUpdate($model, $oldValues, $description = null)
    {
        $changes = [];
        
        // Handle regular model or merged values from multiple models
        if (is_array($oldValues)) {
            // This handles the case where oldValues is from multiple models (faskes + apotek/klinik)
            $currentValues = $model->toArray();
            
            foreach ($oldValues as $key => $oldValue) {
                if (isset($currentValues[$key]) && $currentValues[$key] != $oldValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $currentValues[$key]
                    ];
                }
            }
        } else {
            // Standard Laravel model dirty tracking
            foreach ($model->getDirty() as $key => $newValue) {
                if (isset($oldValues[$key])) {
                    $changes[$key] = [
                        'old' => $oldValues[$key],
                        'new' => $newValue
                    ];
                }
            }
        }
        
        if (!empty($changes)) {
            self::logActivity('update', $model, $oldValues, $changes, $description);
        }
    }
    
    /**
     * Log delete activity
     */
    public static function logDelete($model, $description = null)
    {
        self::logActivity('delete', $model, $model->toArray(), null, $description);
    }
}
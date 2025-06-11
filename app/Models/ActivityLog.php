<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'model_type',
        'model_id',
        'model_name',
        'facility_type',
        'old_values',
        'new_values',
        'description'
    ];
    
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Get the action name in Indonesian
     */
    public function getActionNameAttribute()
    {
        $actions = [
            'create' => 'dibuat',
            'update' => 'diperbarui',
            'delete' => 'dihapus'
        ];
        
        return $actions[$this->action] ?? $this->action;
    }
    
    /**
     * Get the formatted description
     */
    public function getFormattedDescriptionAttribute()
    {
        if ($this->description) {
            return $this->description;
        }
        
        $facilityType = $this->facility_type ?? 'Fasilitas';
        $actionName = $this->getActionNameAttribute();
        
        return "{$facilityType} {$this->model_name} telah {$actionName}";
    }
    
    /**
     * Get the display name with facility type
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->facility_type} {$this->model_name}";
    }
    
    /**
     * Get the action icon based on action type
     */
    public function getActionIconAttribute()
    {
        $icons = [
            'create' => 'fas fa-plus-circle',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash-alt'
        ];
        
        return $icons[$this->action] ?? 'fas fa-info-circle';
    }
    
    /**
     * Get the action color class based on action type
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger'
        ];
        
        return $colors[$this->action] ?? 'info';
    }
}
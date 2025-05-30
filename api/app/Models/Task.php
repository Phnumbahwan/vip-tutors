<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'order',
        'user_id'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($task) {
            $task->id = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeBelongsToUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

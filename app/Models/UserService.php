<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserService extends Model
{
    protected $fillable = [
        'user_id',
        'list_service_id',
        'description',
        'price',
    ];

    public function service()
    {
        return $this->belongsTo(ListService::class, 'list_service_id');
    }

    public function formatted()
    {
        return [
            'id' => $this->id,
            'service_id' => $this->list_service_id,
            'service_name' => $this->service?->name,
            'description' => $this->description,
            'price' => $this->price ? (float) $this->price : null,
        ];
    }
}

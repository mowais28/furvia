<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAvailability extends Model
{
    protected $fillable = [
        'user_id',
        'day',
        'start_time',
        'end_time',
        'slot_label',
        'month',
        'week',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

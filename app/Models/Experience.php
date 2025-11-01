<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'organization',
        'start_year',
        'end_year',
        'is_current',
        'description',
    ];

    public function formatted()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'organization' => $this->organization,
            'start_year' => $this->start_year,
            'end_year' => $this->is_current ? 'Present' : $this->end_year,
            'is_current' => (bool) $this->is_current,
            'description' => $this->description,
        ];
    }
}

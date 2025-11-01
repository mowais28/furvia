<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    protected $table = "educations";

    protected $fillable = [
        "user_id",
        "list_degree_id",
        "institution",
        "year",
        "honor",
    ];

    public function formatted()
    {
        return [
            'id'          => $this->id,
            'degree_id'   => $this->list_degree_id,
            'degree_name' => $this->degree?->name,
            'institution' => $this->institution,
            'year'        => $this->year,
            'honor'       => $this->honor,
        ];
    }


    /**
     * Get the user that owns the Education
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the degree that owns the Education
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function degree(): BelongsTo
    {
        return $this->belongsTo(ListDegree::class, "list_degree_id");
    }
}

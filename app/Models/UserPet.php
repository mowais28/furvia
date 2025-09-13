<?php

namespace App\Models;

use App\Traits\FileUploader;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserPet extends Model
{
    use FileUploader;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'breed',
        'gender',
        "gender_castration",
        "dob",
        'photo'
    ];

    public function fields()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'breed' => $this->breed,
            'gender' => $this->gender,
            'gender_castration' => $this->gender_castration,
            'dob' => $this->dob,
            'age' => Carbon::parse($this->dob)->age,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'created_at' => $this->created_at->toDateString(),
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

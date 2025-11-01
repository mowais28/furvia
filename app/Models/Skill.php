<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['user_id', 'list_skill_id', 'proficiency'];

    public function skill()
    {
        return $this->belongsTo(ListSkill::class, 'list_skill_id');
    }

    public function formatted()
    {
        return [
            'id' => $this->id,
            'skill_id' => $this->list_skill_id,
            'skill_name' => $this->skill?->name,
            'proficiency' => $this->proficiency,
        ];
    }
}

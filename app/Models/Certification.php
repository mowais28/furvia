<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'list_certification_id',
        'institution',
        'year',
        'credential_id',
        'credential_url',
    ];

    public function certification()
    {
        return $this->belongsTo(ListCertification::class, 'list_certification_id');
    }

    public function formatted()
    {
        $data = [
            'id' => $this->id,
            'certification_id' => $this->list_certification_id,
            'certification_name' => optional($this->certification)->name,
        ];
        if ($this->institution != "") {
            $data['institution'] = $this->institution;
        }
        if ($this->year != "") {
            $data['year'] = $this->year;
        }
        if ($this->credential_id != "") {
            $data['credential_id'] = $this->credential_id;
        }
        if ($this->credential_url != "") {
            $data['credential_url'] = $this->credential_url;
        }

        return $data;
    }
}

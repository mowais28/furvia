<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'title',
        'email',
        'email_verified_at',
        'password',
        'profile_picture',
        'status',
        'type',
        'location',
        'zip_code',
        'description',
        'fcm_token',
        "device_id",
        'verification_code',
        'verification_code_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'location' => 'array',
        ];
    }

    public function pets()
    {
        return $this->hasMany(UserPet::class);
    }

    public function services()
    {
        return $this->belongsToMany(ListService::class, 'user_services', 'user_id', 'list_service_id');
    }
}

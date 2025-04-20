<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'profile_image',
        'no_telf'
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
        ];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'coach_id');
    }

    public function inventoryManagements(): HasMany
    {
        return $this->hasMany(InventoryManagement::class, 'mastercoach_id');
    }

    public function coach(): HasOne
    {
        return $this->hasOne(Coaches::class, 'user_id');
    }

    public function student(): HasOne
    {
        return $this->hasOne(student::class, 'user_id');
    }

    public function announcements()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_user');
    }

    public function getProfileImageAttribute($value)
    {
        return $value ? url(Storage::url($value)) : null;
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'student_id');
    }



}

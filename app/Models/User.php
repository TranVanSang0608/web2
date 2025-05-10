<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // Đã xóa dòng 'password' => 'hashed',
    ];

    // Đảm bảo mật khẩu luôn được mã hóa khi lưu
    public function setPasswordAttribute($value)
    {
        // Chỉ mã hóa nếu mật khẩu chưa được mã hóa
        $this->attributes['password'] = \Hash::needsRehash($value) ? \Hash::make($value) : $value;
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function memberProjects()
    {
        return $this->belongsToMany(Project::class, 'project_members');
    }
}
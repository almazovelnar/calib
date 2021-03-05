<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {

    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'permission',
        'username'
    ];

    protected $casts = [
        'permission' => 'encrypted:array'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @param string $permission
     *
     * @return bool
     */
    public function hasPermissionFor(string $permission) : bool {
        return !empty($this->permission[$permission]) && (bool)$this->permission[$permission];
    }

    public function news(): HasMany {
        return $this->hasMany(News::class);
    }
}

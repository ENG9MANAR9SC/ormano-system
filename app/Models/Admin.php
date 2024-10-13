<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
  use HasApiTokens, HasFactory, HasRoles;
  protected $guard = "admin";

  protected $hidden = ['password'];

  protected $fillable = [
    "name",
    "email",
    "phone",
    "password",
    "enabled",
  ];

  protected $guarded = [
    'id'
  ];
  protected $appends = [
    'users_data',
    // 'agents_data',
  ];

  public static function boot()
  {
    parent::boot();
    static::deleting(function ($model) {
      User::deleteReferralHandler($model->id, 'admin');
    });
  }

  public function getUsersDataAttribute()
  {
  }
}

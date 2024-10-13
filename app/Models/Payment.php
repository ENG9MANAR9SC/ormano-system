<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'currency',
      'amount',
      'notes'
    ];

    protected $appends = [
      'user_name',
      'created_by_name',
    ];

    public function getUserNameAttribute() {
      $user = $this->user;

      return $user->full_name;
    }

    public function getCreatedByNameAttribute() {
      $admin_id = $this->created_by;
      $admin = Admin::find($admin_id);

      return $admin->name;
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $user = Auth::user();
            $model->created_by = $user->id;
            $model->updated_by = $user->id;
        });
        static::updating(function($model)
        {
            $user = Auth::user();
            $model->updated_by = $user->id;
        });
    }

    public function user() {
      return $this->belongsTo(User::class, 'user_id');
    }
}

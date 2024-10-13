<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
      'title',
      'notes'
    ];

    public static function boot() {
      parent::boot();
      static::deleting(function($model) {
        User::deleteReferralHandler($model->id, 'referral');
      });
    }
}

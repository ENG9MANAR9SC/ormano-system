<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    const TYPE_USER_MEDICAL_HISTORY = 1;
    const TYPE_USER_PHYSICAL_FINDING = 2;
    const TYPE_USER_DIAGNOSIS = 3;
    const TYPE_USER_MEDICINES = 4;

    protected $fillable = [
      "app_model",
      "text",
      "type",
    ];


    public function users() {
      return $this->belongsToMany(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
  use HasFactory;

  const PRICE_CURRENCY_TYPE = 2;

  
  protected $fillable = [
    'title',
    'slug',
    'description',
    'app_model_id',
    'created_by',
    'updated_by',
  ];

  
  public function appModel()
  {
    return $this->belongsTo(AppModel::class);
  }

  public function createdBy()
  {
    return $this->belongsTo(User::class);
  }

  public function updatedBy()
  {
    return $this->belongsTo(User::class);
  }
}

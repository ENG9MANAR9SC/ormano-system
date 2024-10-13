<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppModel extends Model
{
    use HasFactory;
    protected $fillable = [
      'title',
      'slug',
      'description',
      'path',
      'status',
      'created_by',
      'updated_by',
    ];
    protected $casts = [
      'id' => 'integer',
      'created_by' => 'integer',
      'updated_by' => 'integer',
  ];

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class);
    }
}

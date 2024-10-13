<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSheet extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'user_sheet';
    protected $fillable = [
      "user_id",
      "type",
      "date",
      "notes",
    ];

    protected $appends = [
      'type_title',
      'date_readable',
      'time_readable',
    ];

    const TYPE_ASSESSMENT = 1;
    const TYPE_FOLLOWUP   = 2;
    const TYPE_PATIENT   = 3;

    const TYPE_ARRAY = [
      self::TYPE_ASSESSMENT => 'Assessment sheet',
      self::TYPE_FOLLOWUP => 'Followup sheet',
      self::TYPE_PATIENT => 'Patient sheet',
    ];

    public function getTypeTitleAttribute() {
      return self::TYPE_ARRAY[$this->type];
    }

    // relationship
    public function files() {
      return $this->hasMany(Media::class);
    }
    public function getDateReadableAttribute() {
      return Carbon::parse($this->date)->diffForHumans();
    }
    public function getTimeReadableAttribute() {
      return Carbon::parse($this->time)->format('h:i A');
    }
}

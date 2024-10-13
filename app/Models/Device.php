<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Treatment;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
  use HasFactory, HasTranslations;
  protected $fillable = [
    'category_id',
    'is-common',
    'price',
    'status',
    'currency',
    'created_by',
    'updated_by',
  ];
  public $translatable = [
    'titles',
    'descriptions',
  ];
  protected $appends = [
    'title',
    'description',
  ];

  const CURRENCY_IQD = 1;
  const CURRENCY_USD = 2;

  const CURRENCY_ARRAY = [
    self::CURRENCY_IQD => 'IQD',
    self::CURRENCY_USD => 'USD',
  ];


  public function getTitleAttribute()
  {
    $titlesKeys = $this->getTranslations('titles');
    if (array_key_exists('eng', $titlesKeys)) {
      return $this->getTranslations('titles')['eng'];
    } elseif ($langKey = array_key_first($titlesKeys)) {
      return $this->getTranslations('titles')[$langKey];
    }
  }

  public function getDescriptionAttribute()
  {
    $titlesKeys = $this->getTranslations('descriptions');
    if (array_key_exists('eng', $titlesKeys)) {
      return $this->getTranslations('descriptions')['eng'];
    } elseif ($langKey = array_key_first($titlesKeys)) {
      return $this->getTranslations('descriptions')[$langKey];
    }
  }

  public function getTitleTranslation($langKey = 'eng')
  {
    if (isset($this->getTranslations('titles')[$langKey]) && $this->getTranslations('titles')[$langKey] != ' ') {
      return $this->getTranslations('titles')[$langKey];
    } else if (isset($this->getTranslations('titles')['eng'])) {
      return $this->getTranslations('titles')['eng'];
    } else {
      return '';
    }
  }

  public function getDescriptionTranslation($langKey = 'eng')
  {
    if (isset($this->getTranslations('descriptions')[$langKey]) && $this->getTranslations('descriptions')[$langKey] != ' ') {
      return $this->getTranslations('descriptions')[$langKey];
    } else if (isset($this->getTranslations('descriptions')['eng'])) {
      return $this->getTranslations('descriptions')['eng'];
    } else {
      return '';
    }
  }

  public function category()
  {
    return $this->belongsTo(Category::class, 'category_id');
  }

  public function appointments()
  {
    return $this->belongsToMany(Appointment::class);
  }
  public static function createOrUpdate($input, $id = null)
  {
    $user = auth()->guard('admin')->user();
    // update
    if ($id) {
      $device = Device::findOrFail($id);

      $device->update([
        'category_id' => $input['category_id'],
        'price' => $input['price'],
        'updated_by' => $user->id,
        'status' => $input['status'],
      ]);

      $contentTranslations = json_decode($input['translations']);

      foreach ($contentTranslations as $lang => $contentTranslationsTitle) {
        $device->setTranslation('titles', $lang, $contentTranslationsTitle->title);
        $device->setTranslation('descriptions', $lang, $contentTranslationsTitle->description);
      }

      $device->save();
    }
    // store
    else {
      $device = Device::create([
        'category_id' => $input['category_id'],
        'price' => $input['price'],
        'currency' => $input['currency'],
        'created_by' => $user->id,
        'updated_by' => $user->id,
        'status' => $input['status'],
      ]);

      $contentTranslations = json_decode($input['translations']);

      foreach ($contentTranslations as $lang => $contentTranslationsTitle) {
        $device->setTranslation('titles', $lang, $contentTranslationsTitle->title);
        $device->setTranslation('descriptions', $lang, $contentTranslationsTitle->description);
      }

      $device->save();
    }

    return $device;
  }
}

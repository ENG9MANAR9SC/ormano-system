<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, HasTranslations;

    //
    const STATUS_DISABLED = 0;
    const STATUS_ACTIVE = 1;

    public $translatable = [
      'titles',
      'descriptions',
    ];

    protected $fillable = [
      'is_common',
      'model_id',
      'created_by',
      'updated_by',
      'status',
    ];

    protected $appends = [
      'title',
      'description',
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

    public static function createOrUpdate($input, $id = null) {
      $user = auth()->guard('admin')->user();
      // update
      if($id) {
        $category = Category::findOrFail($id);

        $category->update([
          'model_id' => $input['model_id'],
          'status' => $input['status'],
          'updated_by' => $user->id,
        ]);

        $contentTranslations = json_decode($input['translations']);

        foreach ($contentTranslations as $lang => $contentTranslationsTitle) {
          $category->setTranslation('titles', $lang, $contentTranslationsTitle->title);
          $category->setTranslation('descriptions', $lang, $contentTranslationsTitle->description);
        }

        $category->save();
      }
      // store
      else {
        $category = Category::create([
          'model_id' => $input['model_id'],
          'status' => $input['status'],
          'created_by' => $user->id,
          'updated_by' => $user->id,
        ]);

        $contentTranslations = json_decode($input['translations']);

        foreach ($contentTranslations as $lang => $contentTranslationsTitle) {
          $category->setTranslation('titles', $lang, $contentTranslationsTitle->title);
          $category->setTranslation('descriptions', $lang, $contentTranslationsTitle->description);
        }

        $category->save();
      }

      return $category;
    }
  }

<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Files extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    const TYPE_ASSESSMENT_SHEET = 1;
    const TYPE_FOLLOWUP_SHEET = 2;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Country
 *

 * @mixin \Eloquent
 */
class Country extends Model
{
	use HasFactory;
	protected $guarded = ['id'];
	/**
	 * @var string[]
	 */
	protected $appends = [
		'title',
		'title_official',
		'flag_url',
		'currency',
		'language',
		'language_key',
	];
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'names' => 'array',
		'currencies' => 'array',
		'languages' => 'array',
		'properties' => 'array',
		'data' => 'array',
		'flag' => 'array',
		'created_by' => 'integer',
		'updated_by' => 'integer',
	];

	const GLOBAL_REGION = 1;
	const STATUS_ENABLED = 1;
	const Local_country = 105;
	/*
	 |--------------------------------------------------------------------------
	 | Appends
	 |--------------------------------------------------------------------------
	 */
	/** get title
	 * @return mixed|string
	 */
	public function getFlagUrlAttribute()
	{
		if (!empty($this->iso_a2) && $this->iso_a2 != '-99'){
			return 'https://flagicons.lipis.dev/flags/4x3/'.strtolower($this->iso_a2).'.svg';
		} else {
			return url('assets/images/default_flag.svg');
		}
	}

	public function getTitleAttribute()
	{
		$name = !empty($this->names) ? $this->names['common'] : 'Untitled';
		return $name;
	}
	public function getTitleOfficialAttribute()
	{
		$name = !empty($this->names) ? $this->names['official'] : 'Untitled';
		return $name;
	}
	public function getCurrencyAttribute()
	{
		$items = $this->currencies;
		if (!empty($items)){
			$firstKey = array_key_first($items);
			return $this->currencies[$firstKey];
		}
		return 'Global';
	}
	public function getLanguageAttribute()
	{
		$items = $this->languages;
		if (!empty($items)){
			$firstKey = array_key_first($items);
			return $this->languages[$firstKey];
		}
		return 'Global';

	}
	public function getLanguageKeyAttribute()
	{
		$items = $this->languages;
		if (!empty($items)){
			$firstKey = array_key_first($items);
			return $firstKey;
		}
		return null;

	}
	public function cities()
	{
		return $this->hasMany(City::class, 'country_id', 'id');
	}


	/*
	 |--------------------------------------------------------------------------
	 | Functions
	 |--------------------------------------------------------------------------
	 */
	public static function createOrUpdate($item, $data)
	{
		$input['system_status'] = $data['system_status'];
		$input['user_status'] = $data['user_status'];

		if (!empty($item)){
			$item->update($input);
		}
		return $item;
	}
	public function getCurrency()
	{
		$currencyKey = null;
		$currency = null;
		if ($this->currencies){
			$currencyKey =  $this->currencies[0];
			$currency = Currency::where('currency_key', $currencyKey)->first();
		} else {
			$currency = Currency::first();
		}
		return $currency;
	}

}


<?php

namespace App\Models;

use App\Services\SessionServiceManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Currency
 *
 * @property int $id
 * @property array|null $titles
 * @property array|null $descriptions
 * @property string|null $currency_key
 * @property string|null $currency_symbol
 * @property string|null $iso_a2
 * @property int $type_id
 * @property int $system_status
 * @property int $user_status
 * @property string|null $rate_to_usd
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed|string $flag_url
 * @property-read mixed $title
 * @method static \Database\Factories\CurrencyFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCurrencyKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereCurrencySymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereDescriptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereIsoA2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereRateToUsd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereSystemStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereTitles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereUserStatus($value)
 * @mixin \Eloquent
 */
class Currency extends Model
{
	use HasFactory, HasTranslations;

	/**
	 * @var string[]
	 */
	public $translatable = [
		'titles',
		'descriptions',
	];

	const USER_STATUS_ENABLED = 1;
	const SYSTEM_STATUS_ENABLED = 1;
	const PNT_CURRENCY = 4;
	const USD_CURRENCY = 3;
	const IQD_CURRENCY = 1;
	const USD_CURRENCY_ISO = 'USD';
	const IQD_CURRENCY_ISO = 'IQD';
	const PNT_CURRENCY_ISO = 'PNT';

	protected $guarded = ['id'];
	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id'           => 'integer',
		'titles'       => 'array',
		'descriptions' => 'array',
	];

	/**
	 * @var string[]
	 */
	protected $appends = [
		'title',
		'flag_url',
		'content_translations',
	];
	const MEDIA_COLLECTION_NAME = 'currency_images';

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
		if (!empty($this->iso_a2) && $this->id != 1) {
			return 'https://flagicons.lipis.dev/flags/4x3/' . strtolower($this->iso_a2) . '.svg';
		}
		return getNoAvailableImageUrl();
	}

	public function getTitleAttribute()
	{
		$titlesKeys = $this->getTranslations('titles');
		if (array_key_exists('eng', $titlesKeys)) {
			return $this->getTranslations('titles')['eng'];
		} elseif ($langKey = array_key_first($titlesKeys)) {
			return $this->getTranslations('titles')[$langKey];
		}
	}
	public function getContentTranslationsAttribute()
	{
		$translations = [];
		$languagesKeys =  array_keys($this->getTranslations('titles'));
		foreach ($languagesKeys as $language) {
			$translations[$language] = [
				'lang' => $language,
				'title' => $this->getTranslation('titles', $language),
				'description' => $this->getTranslation('descriptions', $language),
			];
		}
		return $translations;
	}
	/*
	 |--------------------------------------------------------------------------
	 | functions
	 |--------------------------------------------------------------------------
	 */

	public static function createOrUpdate($input, $item = null)
	{
		// update this based on seeder
		if (empty($item)) {
			$item = Currency::factory()->create();
		}
		// titles and description update
		$translations = []; // default array
		$contentTranslations = $input['content_translations'];
		// should remove all translations first take titles as indicator
		foreach ($item->getTranslations('titles') as $key => $currentTitle) {
			$item->forgetTranslation('titles', $key);
			$item->forgetTranslation('descriptions', $key);
		}

		//set all new translations.
		foreach ($contentTranslations as $lang => $contentTranslationsTitle) {
			$item->setTranslation('titles', $lang, $contentTranslationsTitle->title);
			$item->setTranslation('descriptions', $lang, $contentTranslationsTitle->description);
		}
		// update
		$item->update($input);
		return $item;
	}

	public static function getDefaultSystemCurrency()
	{
		return Currency::where('currency_key', 'USD')->first();
	}
	public static function getCurrent()
	{
		// $currencyTypeId = SessionServiceManager::getValue('store_currency_type_id',Type::PRICE_CURRENCY_TYPE);

		$user =  getAuthUser();
		if ($user) {
			$localUser = User::where('email', $user->email)->first();
			if ($localUser) {
				$currencyTypeId = $localUser->store_currency_id;
			}
		} else {
			$currencyTypeId = SessionServiceManager::getValue('store_currency_type_id', Type::PRICE_CURRENCY_TYPE);
		}

		$getCurrentPriceCurrency = SessionServiceManager::getValue('currency_id');
		Log::info('get currency price value!!!');
		Log::info($getCurrentPriceCurrency);
		if ($currencyTypeId == 1) {
			$getCurrentPriceCurrency = 1; // point
		}
		$currency = Currency::find($getCurrentPriceCurrency);
		if (empty($currency)) {
			$currency = self::getDefaultSystemCurrency();
		}

		return $currency;
	}
	public static function getCurrentExchangeRate()
	{
		$currency = self::getCurrent();
		return $currency ? $currency->exchange_rate : 1;
	}
}

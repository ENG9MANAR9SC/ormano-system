<?php

namespace App\Models;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    const GENDER_MALE = 0;
    const GENDER_FEMALE = 1;

    const GENDER_ARRAY = [
        self::GENDER_MALE => 'Male',
        self::GENDER_FEMALE => 'Female',
    ];

    const STATUS_ACTIVE = 0;
    const STATUS_PENDING = 1;
    const STATUS_DISABLED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "full_name",
        "phone_number",
        "email",
        "birthdate",
        "gender",
        "active",
        "referral_model",
        "referral_id",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'properties',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'properties' => 'array',
    ];

    protected $appends = [
        'occupation',
        'civil_status',
        'age',
        'gender_title',
        'address',
        'weight',
        'referral_hybrid_id',
        'referral_details',
    ];

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            User::deleteReferralHandler($model->id, 'user');
        });
    }

    public function getOccupationAttribute()
    {
        $properties = $this->properties;
        return isset($properties['occupation']) ? $properties['occupation'] : '';
    }

    public function getCivilStatusAttribute()
    {
        $properties = $this->properties;
        return isset($properties['civil_status']) ? $properties['civil_status'] : '';
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age;
    }

    public function getAddressAttribute()
    {
        $properties = $this->properties;
        return isset($properties['address']) ? $properties['address'] : '';
    }

    public function getWeightAttribute()
    {
        $properties = $this->properties;
        return isset($properties['weight']) ? $properties['weight'] : '';
    }

    public function getGenderTitleAttribute()
    {
        // return self::GENDER_ARRAY[$this->gender];
        $genderValue = $this->gender;

        if ($genderValue !== null && array_key_exists($genderValue, self::GENDER_ARRAY)) {
            return self::GENDER_ARRAY[$genderValue];
        } else {

            return 'Unknown Gender';
        }
    }

    public function getReferralHybridIdAttribute()
    {
        if (!$this->referral_model) return null;

        $referral_hybrid = $this->referral_model . '_' . $this->referral_id;

        return $referral_hybrid;
    }

    public function getReferralDetailsAttribute()
    {
        $app_model = AppModel::find($this->referral_model);

        if (!$app_model) return null;

        switch ($app_model->slug) {
            case 'user':
                $user = User::find($this->referral_id);
                return $user->full_name . ' (Patient)';
            case 'referral':
                $referral = Referral::find($this->referral_id);
                return $referral->title . ' (Custom)';
            case 'admin':
                $admin = Admin::find($this->referral_id);
                return $admin->name . ' (Employee)';
            default:
                return null;
        }
    }

    public static function createOrUpdate($input, $id = null)
    {
        // $admin = auth()->guard('admin')->user();
        if ($id) {
            $user = User::findOrFail($id);

            if (isset($input['referral_id'])) {
                list($referral_model, $referral_id) = explode('_', $input['referral_id']);
            }

            $user->update([
                "full_name"       => $input["full_name"],
                "phone_number"    => $input["phone_number"],
                "email"           => $input["email"] ?? null,
                "birthdate"       => Carbon::parse($input["birthdate"]),
                "gender"          => $input["gender"],
                "referral_model"  => $referral_model ?? null,
                "referral_id"     => $referral_id ?? null,
            ]);

            $properties["occupation"] = isset($input["occupation"]) ? $input["occupation"] : null;
            $properties["civil_status"] = isset($input["civil_status"]) ? $input["civil_status"] : null;
            $properties["address"] = isset($input["address"]) ? $input["address"] : null;
            $properties["weight"] = isset($input["weight"]) ? $input["weight"] : null;


            $user->properties = $properties;
            $user->save();
        } else {

            if (isset($input['referral_id'])) {
                list($referral_model, $referral_id) = explode('_', $input['referral_id']);
            }

            $user = User::create([
                "full_name"       => $input["full_name"],
                "phone_number"    => $input["phone_number"],
                "email"           => isset($input["email"]) ? $input["email"] : null,
                "birthdate"       => Carbon::parse($input["birthdate"]),
                "gender"          => $input["gender"],
                "active"          => self::STATUS_PENDING,
                "referral_model"  => $referral_model ?? null,
                "referral_id"     => $referral_id ?? null,
            ]);
            $properties = [];

            $properties["occupation"] = isset($input["occupation"]) ? $input["occupation"] : null;
            $properties["civil_status"] = isset($input["civil_status"]) ? $input["civil_status"] : null;
            $properties["address"] = isset($input["address"]) ? $input["address"] : null;
            $properties["weight"] = isset($input["weight"]) ? $input["weight"] : null;


            $user->properties = $properties;
            $user->save();
        }


        return $user;
    }

    public static function createOrAttachSheet($input, $id = null)
    {
        if ($id) {
            $sheet = UserSheet::find($id);

            $sheet->update([
                "user_id" => $input['user_id'],
                "type" => $input['type'],
                "date" => $input['date'],
                "notes" => isset($input['notes']) ? $input['notes'] : null,
            ]);
        } else {
            $sheet = UserSheet::create([
                "user_id" => $input['user_id'],
                "type" => $input['type'],
                "date" => $input['date'],
                "notes" => isset($input['notes']) ? $input['notes'] : null,
            ]);
        }

        return $sheet;
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function sheets()
    {
        return $this->hasMany(UserSheet::class)->orderBy('date', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function notes()
    {
        return $this->belongsToMany(Note::class);
    }

    public function recalculateBalance()
    {
        $payments = Payment::where('user_id', $this->id)->get();
        $orders = Order::where('user_id', $this->id)->get();

        $balance = 0;

        foreach ($payments as $payment) {
            $balance += $payment->amount;
        }

        foreach ($orders as $order) {
            if ($order->status == Order::STATUS_CANCELED) {
                // $balance += $order->total_paid;
            } else {
                $balance -= $order->grand_total;
            }
        }

        $this->balance = $balance;
        $this->save();
    }

    public static function deleteReferralHandler($model_id, $slug)
    {
        $app_model = AppModel::where('slug', $slug)->first();

        if (isset($app_model)) {
            User::where('referral_model', $app_model->id)
                ->where('referral_id', $model_id)
                ->update([
                    'referral_model' => null,
                    'referral_id' => null
                ]);
        }
    }
}

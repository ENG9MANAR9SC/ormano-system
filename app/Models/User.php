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

    const STATUS_UnActive = 0;
    const STATUS_ACTIVE = 1;
   

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


    public function getGenderTitleAttribute()
    {
        // return self::GENDER_ARRAY[$this->gender];
        $genderValue = $this->gender;

        if ($genderValue !== null && array_key_exists($genderValue, self::GENDER_ARRAY)) {
            return self::GENDER_ARRAY[$genderValue];
        } else {

            return 'Unknown';
        }
    }

    public static function createOrUpdate($input, $id = null)
    {
        // $admin = auth()->guard('admin')->user();
        if ($id) {
            $user = User::findOrFail($id);
            $user->update([
                "full_name"       => $input["user"]["full_name"],
                "phone_number"    => $input["user"]["phone_number"],
                "email"           => $input["user"]["email"] ?? null,
                "birthdate"       => Carbon::parse($input["user"]["birth_date"]),
                "gender"          => $input["user"]["gender"],
            ]);

            $properties["user"]["occupation"]   = isset($input["user"]["occupation"]) ? $input["user"]["occupation"] : null;
            $properties["user"]["civil_status"] = isset($input["user"]["civil_status"]) ? $input["user"]["civil_status"] : null;
            $properties["user"]["address"]      = isset($input["user"]["address"]) ? $input["user"]["address"] : null;


            $user->properties = $properties;
            $user->save();
        } else {
           
            $user = User::create([
                "full_name"       => $input["user"]["full_name"],
                "phone_number"    => $input["user"]["phone_number"],
                "email"           => isset($input["user"]["email"]) ? $input["user"]["email"] : null,
                "birthdate"       => Carbon::parse($input["user"]["birth_date"]),
                "gender"          => $input["user"]["gender"],
                "active"          => self::STATUS_ACTIVE,
            ]);
            $properties = [];

            $properties["occupation"] = isset($input["occupation"]) ? $input["occupation"] : null;
            $properties["civil_status"] = isset($input["civil_status"]) ? $input["civil_status"] : null;
            $properties["address"] = isset($input["address"]) ? $input["address"] : null;



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

}

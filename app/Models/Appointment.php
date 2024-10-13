<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
  use HasFactory;

  const TYPE_VISIT = 1;
  const TYPE_SESSION = 2;

  const TYPE_ARRAY = [
    self::TYPE_VISIT => 'Visit',
    self::TYPE_SESSION => 'Session',
  ];


  const STATUS_PENDING   = 1;
  const STATUS_COMPLETED = 2;
  const STATUS_CANCELLED = 3;


  const STATUS_ARRAY = [
    self::STATUS_PENDING   => 'pending',
    self::STATUS_COMPLETED => 'completed',
    self::STATUS_CANCELLED => 'cancelled',
  ];


  const PRICING_BY_DURATION = 1;
  const PRICING_BY_DEVICE = 2;

  const PRICING_ARRAY = [
    self::PRICING_BY_DURATION => 'Duration',
    self::PRICING_BY_DEVICE => 'Device',
  ];

  protected $fillable = [
    'date',
    'time',
    'duration',
    'type',
    'status',
    'notes',
    'pricing',
    'user_id',
    'created_by',
    'updated_by',

    'cost',
    'discount',
    'discount_type',
    'currency',

    'properties',

    'supervisor_id'
  ];


  // protected $casts = [
  //   'properties' => 'array',
  // ];

  protected $appends = [
    'pricing_title',
    'type_title',
    'status_title',
    'user_full_name',
    'date_readable',
    'time_readable',

    'supervisor_name',
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function devices()
  {
    return $this->belongsToMany(Device::class)->withPivot(['discount', 'currency', 'price', 'exchange_rate', 'quantity', 'discount_type']);
  }

  public function order()
  {
    return $this->morphOne(Order::class, 'orderable');
  }

  public function getDateReadableAttribute()
  {
    return Carbon::parse($this->date)->diffForHumans();
  }
  public function getTimeReadableAttribute()
  {
    return Carbon::parse($this->time)->format('h:i A');
  }

  public function getTypeTitleAttribute()
  {
    return self::TYPE_ARRAY[$this->type];
  }

  public function getPricingTitleAttribute()
  {
    return self::PRICING_ARRAY[$this->pricing];
  }

  public function getStatusTitleAttribute()
  {
    return self::STATUS_ARRAY[$this->status];
  }

  public function getUserFullNameAttribute()
  {
    return User::find($this->user_id)->full_name;
  }

  public function getSupervisorNameAttribute()
  {
    $admin = Admin::find($this->supervisor_id);
    return isset($admin) ? $admin->name : null;
  }

  public static function createOrUpdate($input, $id = null)
  {
    $user = auth()->guard('admin')->user();

    // update
    if ($id) {
      $appointment = Appointment::findOrFail($id);
      $appointment->update([
        'date' => $input['date'],
        'time' => Carbon::parse($input['time']),
        'duration' => $input['duration'],
        'type' => $input['type'],
        // 'status' => $input['status'],
        'notes' => isset($input['notes']) ? $input['notes'] : '',
        'pricing' => $input['pricing'],
        'user_id' => $input['user_id'],
        'updated_by' => $user->id,

        'cost'      => isset($input['cost']) ? $input['cost'] : 0,
        'discount'  => $input['discount'],
        'discount_type'  => $input['discount_type'],

        'currency'  => isset($input['currency']) ? $input['currency'] : null,

        'supervisor_id' => isset($input['supervisor_id']) ? $input['supervisor_id'] : null,
      ]);

      $devices = json_decode($input['devices']);

      $devicesToKeep = [];
      foreach ($devices as $device) {
        $data = [
          'id'              => $device->id,
          'discount'        => $device->discount ?? 0,
          'discount_type'   => $device->discount_type,
          'quantity'        => 1,
          'price'           => $device->price,
          'currency'        => $device->currency,
          'exchange_rate'   => 1,
        ];

        if ($appointment->devices->contains($device->id)) {
          // Update existing record
          $appointment->devices()->updateExistingPivot($device->id, $data);
          $devicesToKeep[] = $device->id;
        } else {
          // Create a new record
          $appointment->devices()->attach($device->id, $data);
          $devicesToKeep[] = $device->id;
        }
      }
      $devicesToRemove = $appointment->devices()->whereNotIn('devices.id', $devicesToKeep)->pluck('device_id');
      $appointment->devices()->detach($devicesToRemove);


      $appointment->save();
    }
    // store
    else {
      $appointment = Appointment::create([
        'date' => $input['date'],
        'time' => Carbon::parse($input['time']),
        'duration' => $input['duration'],
        'type' => $input['type'],
        // 'status' => $input['status'],
        'notes' => isset($input['notes']) ? $input['notes'] : '',
        'pricing' => $input['pricing'],
        'user_id' => $input['user_id'],
        'created_by' => $user->id,
        'updated_by' => $user->id,

        'cost'      => isset($input['cost']) ? $input['cost'] : 0,
        'discount'  => $input['discount'],
        'discount_type'  => $input['discount_type'],
        'currency'  => isset($input['currency']) ? $input['currency'] : null,

        'supervisor_id' => isset($input['supervisor_id']) ? $input['supervisor_id'] : null,
      ]);

      $devices = json_decode($input['devices']);

      foreach ($devices as $device) {
        $appointment
          ->devices()
          ->attach($device->id, [
            'discount'        => $device->discount ?? 0,
            'discount_type'   =>  $device->discount_type ?? 1,
            'quantity'        => 1,
            'price'           => $device->price,
            'currency'        => $device->currency,
            'exchange_rate'   => 1,
          ]);
      }
      $appointment->save();
    }

    return $appointment;
  }
}

<?php

namespace App\Models;

use App\Models\Device;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
  use HasFactory;

  const TYPE_APPOINTMENT_VISIT = 1;
  const TYPE_APPOINTMENT_SESSION = 2;

  const TYPES_ARRAY = [
    self::TYPE_APPOINTMENT_VISIT    => 'Visit',
    self::TYPE_APPOINTMENT_SESSION  => 'Session',
  ];

  const STATUS_UNPAID = 1;
  const STATUS_PARTIAL_PAID = 2;
  const STATUS_PAID = 3;
  const STATUS_CANCELED = 4;

  const STATUS_ARRAY = [
    self::STATUS_UNPAID       => 'Unpaid',
    self::STATUS_PARTIAL_PAID => 'Partial paid',
    self::STATUS_PAID         => 'Paid',
    self::STATUS_CANCELED     => 'Canceled',

  ];

  const FEE_TYPE_VISIT = 1;

  const FEE_TYPES_ARRAY = [
    self::FEE_TYPE_VISIT => 'Visit charge',
  ];

  protected $fillable = [
    'orderable_id',
    'orderable_type',
    'type',
    'user_id',
    'order_date',
    'fees',
    'discount',
    'sub_total',
    'grand_total',
    'total_remaining',
    'status',
    'notes',
  ];

  protected $appends = [
    'grand_total_fees',
    'sub_total_fees',
    'order_from',
    'status_name',
    'type_name',
    'user_name',
    'created_by_name',
  ];

  public function getGrandTotalFeesAttribute()
  {
    $fees = json_decode($this->fees);
    $total_fees = 0;

    foreach ($fees as $fee) {
      // dd($fee);
      $total_fees += $fee->price - ($fee->price * $fee->discount / 100);
    }

    return $total_fees;
  }

  public function getSubTotalFeesAttribute()
  {
    $fees = json_decode($this->fees);
    $total_fees = 0;

    foreach ($fees as $fee) {
      // dd($fee);
      $total_fees += $fee->price;
    }

    return $total_fees;
  }

  public function getOrderFromAttribute()
  {
    // TODO: change this to
    return 'Appointment';
  }

  public function getStatusNameAttribute()
  {
    return self::STATUS_ARRAY[$this->status];
  }

  public function getTypeNameAttribute()
  {
    return self::TYPES_ARRAY[$this->type];
  }

  public function getUserNameAttribute()
  {
    // FIXME: later fit to other types
    // $appoinment = $this->orderable;
    $user = User::find($this->user_id);

    return isset($user->full_name) ? $user->full_name : '-';
  }

  public function getCreatedByNameAttribute()
  {
    $admin_id = $this->created_by;
    $admin = Admin::find($admin_id);

    return $admin->name;
  }

  public static function boot()
  {
    parent::boot();
    static::creating(function ($model) {
      $user = Auth::user();
      $model->created_by = $user->id;
      $model->updated_by = $user->id;
    });
    static::updating(function ($model) {
      $user = Auth::user();
      $model->updated_by = $user->id;
      if ($model->status != self::STATUS_CANCELED) {
        if ($model->total_paid == $model->grand_total) {
          $model->status = self::STATUS_PAID;
        } else if ($model->total_remaining != 0) {
          $model->status = self::STATUS_PARTIAL_PAID;
        } else if ($model->total_remaining == 0) {
          $model->status = self::STATUS_UNPAID;
        }
      }
    });
  }

  public function orderable()
  {
    return $this->morphTo();
  }
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function getItems()
  {
    if ($this->orderable_type == Appointment::class) {
      $appoinment = $this->orderable()->first();

      return $appoinment->devices;
    }
  }
}

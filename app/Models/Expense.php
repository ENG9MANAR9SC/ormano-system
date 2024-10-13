<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
  use HasFactory;
  protected $fillable = [
    'description',
    'amount',
    'expense_date',
    'type',
    'expense_by',
    'created_by',
    'updated_by'

  ];

  protected $appends = [
    'type_name',
    'admin_name',
  ];
  public function admin()
    {
      return $this->belongsTo(Admin::class, 'admin_id');
    }
  public function type()
    {
      return $this->hasMany(Type::class, 'type_id');
    }

  public function getTypeNameAttribute()
  {
    $type = Type::find($this->type);
    if ($type) {
      return $type->title;
    }
      return null;
  }
  public function getAdminNameAttribute()
  {
    $expense_by = Admin::find($this->expense_by);

    if ($expense_by) {
      return $expense_by->name;
    }
      return null;
  }

  public static function createOrUpdate($input, $id = null) {
      $user = auth()->guard('admin')->user();
      // update
      if($id) {

        $expense = Expense::findOrFail($id);
        $expense->update([
          'description'   =>  $input['note'] ?? '',
          'amount'        =>  $input['amount'],
          'type'          =>  $input['type_id'],
          'expense_date'  =>  $input['date'],
          'expense_by'    =>  $input['admin_id'],
          // 'updated_by'    =>  $user->id,
        ]);


        $expense->save();
      }
      // store
      else {
        $expense= Expense::create([
          'description'   => $input['note'] ?? '',
          'amount'        => $input['amount'],
          'type'          => $input['type_id'],
          'expense_date'  => $input['date'],
          'expense_by'    => $input['admin_id'],
          'created_by'    => $user->id,
          // 'updated_by'    => $user->id,

        ]);
        $expense->save();
      }

      return $expense;
    }
}

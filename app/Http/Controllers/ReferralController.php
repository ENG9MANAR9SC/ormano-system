<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\AppModel;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
  public function index(Request $request) {
    $per_page = request()->per_page ?? 12;
    $search = $request->search_key;


    $referrals = Referral::query();
    if (isset($search)) {
      $referrals = $referrals->where('title', 'like', '%' . $search . '%');
    }
    $referrals = $referrals->paginate($per_page);

    return response()->json([
      'referrals' => $referrals,
    ]);
  }

  public function allReferrals(Request $request) {
    $search = $request->s;

    $users = User::where('full_name', 'like', "%{$search}%")->get();
    $admins = Admin::where('name', 'like', "%{$search}%")->get();
    $custom = Referral::where('title', 'like', "%{$search}%")->get();

    $searchResults = [
      ...$users->map(function ($user) {
        return [
          'id' => $user->id,
          'text' => $user->full_name,
          'type' => 'Patient',
          'model_id' => AppModel::where('slug', 'user')->first()->id,
        ];
      }),
      ...$admins->map(function ($admin) {
        return [
          'id' => $admin->id,
          'text' => $admin->name,
          'type' => 'Employee',
          'model_id' => AppModel::where('slug', 'admin')->first()->id,
        ];
      }),
      ...$custom->map(function ($c) {
        return [
          'id' => $c->id,
          'text' => $c->title,
          'type' => 'Custom',
          'model_id' => AppModel::where('slug', 'referral')->first()->id,
        ];
      }),
    ];

    $slicedResults = array_slice($searchResults, 0, 10);

    return response()->json([
      'refs' => $searchResults
    ]);

    // $referrals = Referral::whereHas('admin.user', function ($query) use ($search) {
    //   $query->where('role', $search);
    // })->paginate($per_page);
  }

  public function store(Request $request) {
    $input = $request->validate([
      "title" => "required",
      "notes" => "nullable",
      "id"    => "nullable|exists:referrals,id",
    ]);

    if(isset($input['id'])) {
      Referral::find($input['id'])->update([
        'title' => $input['title'],
        'notes' => isset($input['notes']) ? $input['notes'] : null,
      ]);

      return response()->json([
        'status' => true,
        'update' => true,
      ]);
    } else {
      Referral::create([
        'title' => $input['title'],
        'notes' => isset($input['notes']) ? $input['notes'] : null,
      ]);

      return response()->json([
        'status' => true,
        'update' => false,
      ]);
    }
  }

  public function destroy($id) {
    Referral::destroy($id);

    return response()->json([
      'status' => true,
    ]);
  }
}

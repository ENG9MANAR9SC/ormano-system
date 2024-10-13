<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Device;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
  public function getData()
  {
    $appointmentsTodayCount = Appointment::whereDate('date', Carbon::now())->count();
    $patientCount = User::count();
    $devicesCount = Device::count();

    $appointmentsToday = Appointment::whereDate('date', Carbon::now())->orderBy('time')->get();

    $currentDate = Carbon::today();

    $dateAfterWeek = Carbon::today()->addDays(7);
    if ($currentDate->format('m') == 12 && $dateAfterWeek->format('m') == 1) {
      $birthdaysInRange = DB::table('users')
        ->where(function ($query) use ($currentDate) {
          $query->whereRaw('DATE_FORMAT(birthdate, "%m-%d") >= ?', [$currentDate->format('m-d')])
            ->whereRaw('DATE_FORMAT(birthdate, "%m-%d") <= "12-31"');
        })
        ->orWhere(function ($query) use ($dateAfterWeek) {
          $query->whereRaw('DATE_FORMAT(birthdate, "%m-%d") >= "01-01"')
            ->whereRaw('DATE_FORMAT(birthdate, "%m-%d") <= ?', [$dateAfterWeek->format('m-d')]);
        })
        ->orderByRaw('MONTH(birthdate), DAY(birthdate)')
        ->get();
    } else {
      $birthdaysInRange = DB::table('users')
        ->where(function ($query) use ($currentDate, $dateAfterWeek) {
          $query->whereRaw('DATE_FORMAT(birthdate, "%m-%d") >= ? AND DATE_FORMAT(birthdate, "%m-%d") < ?', [$currentDate->format('m-d'), $dateAfterWeek->format('m-d')]);
        })
        ->orderByRaw('MONTH(birthdate), DAY(birthdate)')
        ->get();
    }


    $totalPaymentsForToday = Payment::whereDate('created_at', '=', Carbon::today())->sum('amount');


    return response()->json([
      'appointmentsTodayCount'  => $appointmentsTodayCount,
      'patientCount'            => $patientCount,
      'devicesCount'            => $devicesCount,
      'appointmentsToday'       => $appointmentsToday,
      'incomingBirthdays'       => $birthdaysInRange,
      'totalPaymentsForToday'   => $totalPaymentsForToday,
    ]);
  }
}

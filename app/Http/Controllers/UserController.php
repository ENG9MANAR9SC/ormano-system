<?php

namespace App\Http\Controllers;

use App\Models\AppModel;
use App\Models\Note;
use App\Models\User;
use App\Models\UserSheet;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Validated;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $per_page = request()->per_page ?? 12;
    $search = $request->s; // Get the 'search' value from the request

    if (isset($search)) {
      $users = User::where('full_name', 'like', '%' . $search . '%')
        ->orWhere('phone_number', 'like', '%' . $search . '%')
        ->paginate(request()->perPage);
    } else {
      $users = User::paginate($per_page);
    }
    return response()->json([
      'users' => $users,
    ]);
  }

  public function searchById($id)
  {
    return response()->json([
      'user' => User::find($id),
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    if ($request->id) {
      $validated = $request->validate([
        "full_name" => "required",
        "phone_number" => "required",
        "email" => "nullable",
        "address" => "nullable",
        "birthdate" => "required",
        "gender" => "required",
        "occupation" => "nullable",
        "civil_status" => "nullable",
        "weight" => "nullable",
        "referral_id" => "nullable",
      ]);
    } else {
      $validated = $request->validate([
        "full_name" => "required",
        "phone_number" => "required | unique:users,phone_number",
        "email" => "nullable",
        "address" => "nullable",
        "birthdate" => "required",
        "gender" => "required",
        "occupation" => "nullable",
        "civil_status" => "nullable",
        "weight" => "nullable",
        "referral_id" => "nullable",
      ]);
    }

    User::createOrUpdate($validated, $request->id);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(User $user)
  {
    $userWithSheetsAndMedia = User::with(['sheets.media', 'appointments.devices', 'notes'])->find($user->id);
    return response()->json([
      'item' => $userWithSheetsAndMedia,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($user)
  {
    User::destroy($user);

    return true;
  }

  public function export()
  {
    // TODO:
    // $payments = User::all();

    // $headers = [
    //   'ID',
    //   'Patient name',
    //   'Amount',
    //   'Created by',
    //   'Referral',
    //   'Date',
    // ];

    // $rows = $payments->map(function($payment) {

    //   return [
    //     $payment->id,
    //     $payment->user_name,
    //     $payment->amount,
    //     $payment->created_by_name,
    //     $user_referral,
    //     $payment->created_at->format('Y-m-d H:i:s'),
    //   ];
    // })->toArray();


    // $result = ExcelExporterService::getInstance()
    //   ->setHeaders($headers)
    //   ->setRows($rows)
    //   ->exportExcel();

    // return response()->json([
    //   'filedata' => $result['file'],
    //   'filename' => $result['name'],
    // ]);
  }

  ///////////////////////////////
  // sheets
  ///////////////////////////////

  public function saveSheet(Request $request)
  {
    $validated = $request->validate([
      "user_id" => 'required',
      "date" => 'required',
      "type" => 'required',
      "notes" => 'nullable',
    ]);

    $sheet = User::createOrAttachSheet($validated, request()->id);

    return response()->json([
      'msg' => request()->id ? 'Sheet updated successfully' : 'Sheet created successfully',
      'data' => $sheet,
      'status' => true
    ]);
  }

  public function getUserSheets($id)
  {
    $user = User::with('sheets.media')->find($id);

    // dd($user->sheets);
    return response()->json([
      'items' => $user->sheets,
    ]);
  }

  public function addFileToSheet(Request $request, $id)
  {
    $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

    // check if the upload is success, throw exception or return response you need
    if ($receiver->isUploaded() === false) {
      throw new UploadMissingFileException();
    }

    // receive the file
    $save = $receiver->receive();

    // check if the upload has finished (in chunk mode it will send smaller files)
    if ($save->isFinished()) {
      // save the file and return any response you need, current example uses `move` function. If you are
      // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
      // return $this->saveFile($save->getFile());
      $sheet = UserSheet::with('media')->find($id);

      $sheet->addMedia($save->getFile())
        ->toMediaCollection();
      return response()->json([
        'status'    => true
      ]);
    }

    // we are in chunk mode, lets send the current progress
    /** @var AbstractHandler $handler */
    $handler = $save->handler();

    return response()->json([
      "done" => $handler->getPercentageDone(),
    ]);
  }

  public function destroySheet($id)
  {
    $sheet = UserSheet::findOrFail($id);
    $sheet->clearMediaCollection();
    UserSheet::where('id', $id)->delete();

    return response()->json([
      'status' => true,
    ]);
  }

  ///////////////////////////////
  // notes
  ///////////////////////////////
  public function saveNotes(Request $request)
  {
    // dd($request);
    $request->validate([
      'user_id' => 'required|exists:users,id'
    ]);

    $notes = json_decode($request->notes);
    $user = User::findOrFail($request->user_id);

    $user->notes()->detach();

    foreach ($notes as $note) {
      if (isset($note->id)) {
        $oldNotes = Note::find($note->id);

        $user->notes()->syncWithoutDetaching($oldNotes->id);
      } else {
        $newNote = Note::create([
          'app_model' => 3, // refer to user model
          'text'      => $note->text,
          'type'      => $note->type,
        ]);

        $user->notes()->syncWithoutDetaching($newNote->id);
      }
    }

    return response()->json([
      'notes' => $user->notes()->get()
    ]);
  }

  public function clearReferral($id)
  {
    $user = User::find($id);

    if ($user) {
      $user->update([
        'referral_id' => null,
        'referral_model' => null,
      ]);
      return response()->json([
        'status' => true,
      ]);
    }

    return response()->json([
      'status' => false,
    ]);
  }

  ///////////////////////////////
  // packages
  ///////////////////////////////
  public function getUserPackages()
  {
    $user = User::find(request()->user_id);
    $packages = $user->packages()
      ->where('user_package.appointment_count', '>', 0)
      ->get()
      ->map(function ($e) {
        return [
          'title' => $e->title,
          'appointment_count' => $e->pivot->appointment_count,
          'user_package_id' => $e->pivot->id,
        ];
      });

    return response()->json([
      'packages' => $packages,
    ]);
  }

  ///////////////////////////////
  // others
  ///////////////////////////////
  public function refreshBalance($id)
  {
    $user = User::find($id);
    $user->recalculateBalance();

    return response()->json([
      'status' => true,
      'balance' => $user->balance,
    ]);
  }
}

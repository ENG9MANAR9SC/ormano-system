<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
  public function index(Request $request)
  {
    $request->validate([
      'type'    => 'required',
      's'       => 'nullable',
      'user_id' => 'required',
    ]);

    $notes = Note::where('type', $request->type)
      ->where('text', 'LIKE', '%' . $request->s . '%')
      ->get();

    return response()->json([
      'notes' => $notes,
    ]);
  }
}

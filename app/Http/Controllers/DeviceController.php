<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Treatment;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
    $per_page = request()->per_page ?? 12;
    $devices = Device::paginate($per_page);
    return response()->json([
      'devices' => $devices,

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
    $validated = $request->validate([
      'translations' => 'json',
      'category_id' => 'required',
      'currency' => 'required',
      'price' => 'required',
      'status' => 'nullable',
    ]);


    Device::createOrUpdate($validated, $request->id);

    return response()->json([
      'status' => true,
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Device  $device
   * @return \Illuminate\Http\Response
   */
  public function show(Device $device)
  {
    //
    return response()->json([
      'item' => $device,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Device  $device
   * @return \Illuminate\Http\Response
   */
  public function edit(Device $device)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Device  $device
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Device $device)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Device  $device
   * @return \Illuminate\Http\Response
   */
  public function destroy(Device $device)
  {
    Device::destroy($device->id);

    return true;
  }
}

<?php

namespace App\Http\Controllers\Api;

use App\Setting;
use App\DatabaseManagement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DatabaseManagement\Store;

class DatabaseManagementController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = DatabaseManagement::latest();
        // check if the items has page and limit
        if ($request->has('page')) {
            $limit= (!empty($request['limit'])) ? $request['limit'] : 10;
            $items= $items->paginate($limit);
        } else {
            $items= $items->get();
        }
        // check if the item is not empty
        if (!empty($items)) {
            try {
               return response()->json($items,200); 
           } catch(\Exception $e) {
               return response()->json("Error.",400);
           }
        } else {
            return response()->json("0 items found.",404);
        }
    }

    /**
     * Settings api to set the rfid.
     *
     * @return \Illuminate\Http\Response
     */
    public function setRfid(Request $request)
    {
        $settings = Setting::find(1);
        $settings->rfid = $request->rfid;
        $settings->save();
        return $settings;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        try {
            $item = DatabaseManagement::query()
            ->create([
                'user' => $request->user,
                'path' => $request->path,
                'shift' => $request->shift
            ]);

            return response()->json($item,200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $item = DatabaseManagement::where('id',$id)->get();
            return response()->json($item,200); 
        } catch (\Exception $exception) {
            throw $exception;
        }
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
    public function update(Update $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

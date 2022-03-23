<?php

namespace App\Http\Controllers\Api;

use App\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\Store;
use App\Http\Requests\Vehicle\Update;

class VehicleController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = new Vehicle;
 
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
    public function store(Store $request)
    {
        try {
            $vehicle = Vehicle::query()
            ->create([
                'vehicle_name' => $request->vehicle_name,
                'description' => $request->description
            ]);

            return response()->json($vehicle,200);
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
        //
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
        try {
          $item = Vehicle::findOrFail($id);
          $item->vehicle_name = $request->vehicle_name;
          $item->description = $request->description;
          $item->save();
          return response()->json($item,200); 
          } catch (\Exception $exception) {
          throw $exception;
          }
      }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $item = Vehicle::findOrFail($id);
            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}

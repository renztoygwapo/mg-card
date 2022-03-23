<?php

namespace App\Http\Controllers\Api;

use App\FleetCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FleetCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = FleetCard::latest()->with('customerGroup');
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $item = CustomerGroup::where('id', $id)->with('pointSystem')->get();
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
        try {
            $item = FleetCard::findOrFail($id);
            $item->company_name = $request->company_name;
            $item->company_address = $request->company_address;
            $item->company_number = $request->company_number;
            $item->customer_group_id = $request->customer_group_id;
            $item->tin_no = $request->tin_no;
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
            $item = FleetCard::findOrFail($id);
            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}

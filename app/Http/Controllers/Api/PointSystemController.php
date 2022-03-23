<?php

namespace App\Http\Controllers\Api;

use App\PointSystem;
use App\PointSystemSummary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PointSystem\Store;
use App\Http\Requests\PointSystem\Update;

class PointSystemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = PointSystem::latest()->with(['customerGroup','product']);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        try {
            $point_system_counts = PointSystem::where('product_id', $request->product_id)
                                              ->where('customer_group_id', $request->customer_group_id)
                                              ->get();
                                              
            if (count($point_system_counts) === 0) {
                $point_system = PointSystem::create($request->only(
                    ['customer_group_id', 'product_id', 'equivalent_points']
                ));
    
                $point_system_summary = PointSystemSummary::create([
                    'point_system_id' => $point_system->id,
                    'product_id' => $point_system->product_id,
                    'customer_group_id' => $point_system->customer_group_id,
                    'shift' => $request->shift,
                    'encoded_by' => $request->username,
                    'equivalent_points' => $point_system->equivalent_points
                ]);
    
                $point_system->customerGroup;
                $point_system->product;
                return $point_system;
            } else {
                return response()->json('Point System Already Exist!', 500);
            }
            
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
            $item = PointSystem::where('id', $id)->with(['customerGroup','product'])->get();
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

            $item = PointSystem::findOrFail($id);
            $item->customer_group_id = $request->customer_group_id;
            $item->product_id = $request->product_id;
            $item->equivalent_points = $request->equivalent_points;
            $item->save();

            $point_system_summary = PointSystemSummary::create([
                'point_system_id' => $item->id,
                'product_id' => $item->product_id,
                'customer_group_id' => $item->customer_group_id,
                'shift' => $request->shift,
                'encoded_by' => $request->username,
                'equivalent_points' => $item->equivalent_points
            ]);

            $item->customerGroup;
            $item->product;
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
            $item = PointSystem::findOrFail($id);
            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}

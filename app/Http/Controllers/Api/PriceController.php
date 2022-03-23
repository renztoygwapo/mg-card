<?php

namespace App\Http\Controllers\API;

use App\Price;
use App\PriceSummary;
use Illuminate\Http\Request;
use App\Http\Requests\Price\Store;
use App\Http\Requests\Price\Update;
use App\Http\Controllers\Controller;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Price::latest()->with('product');

        // check if the items has page and limit
        if ($request->has('page')) {
            $limit= (!empty($request['limit'])) ? $request['limit'] : 15;
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
            $has_product_id = Price::where('product_id', $request->product_id)->get();
            
            if (count($has_product_id) <= 0) {
                $price = Price::create($request->only(
                    ['product_date', 'product_id', 'product_price']
                ));
                
                $price_summary = PriceSummary::create([
                    'price_id' => $price->id,
                    'product_date' => $request->product_date,
                    'shift' => $request->shift,
                    'encoded_by' => $request->username,
                    'product_id' => $request->product_id,
                    'updated_product_price' => $request->product_price
                ]);
                
                $price->product;
                return $price;
            } else {
                
                $price = Price::findOrFail($has_product_id[0]->id);
                $price->product_price = $request->product_price;
                $price->save();
                $price->product;

                $price_summary = PriceSummary::create([
                    'price_id' => $price->id,
                    'product_date' => $request->product_date,
                    'shift' => $request->shift,
                    'encoded_by' => $request->username,
                    'product_id' => $request->product_id,
                    'updated_product_price' => $request->product_price
                ]);

                return response()->json(['message' => 'Product already exist. Price automatically updated.'], 200);
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
            $item = Price::with('product')->where('id', $id)->get();
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

            $item = Price::findOrFail($id);
            $item->product_date = $request->product_date;
            $item->product_id = $request->product_id;
            $item->product_price = $request->product_price;
            $item->save();

            $price_summary = PriceSummary::create([
                'price_id' => $item->id,
                'product_date' => $request->product_date,
                'shift' => $request->shift,
                'encoded_by' => $request->username,
                'product_id' => $request->product_id,
                'updated_product_price' => $request->product_price
            ]);

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
            $item = Price::findOrFail($id);
            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}

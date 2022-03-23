<?php

namespace App\Http\Controllers\API;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Store;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Product\Update;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Product::latest()->with(['price','pointSystem']);

        if ($request->export) {
            $items = $items->get();
            
            $items = $items->map(function ($val) {
                return [
                    'PRODUCT NAME' => $val->product_name,
                    'PRODUCT TYPE' => $val->product_type
                ];
            });
            
            $this->convertToExcel($items);
            
        } else {
             // check if the items has page and limit
             if ($request->has('page')) {
                 $limit= (!empty($request['limit'])) ? $request['limit'] : 15;
                 $items= $items->paginate($limit);
             } else {
                 $items= $items->get();
             }
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
            $product = Product::create($request->only(
                ['product_name', 'product_type']
            ));
            return $product;
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
            $item = Product::with('price')->where('id',$id)->get();
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
            $item = Product::findOrFail($id);
            $item->product_name = $request->product_name;
            $item->product_type = $request->product_type;
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
            $item = Product::findOrFail($id);
            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function convertToExcel($sheetData)
    {
        $sheetData = collect($sheetData);
        
        Excel::create('REPORT PRODUCT SUMMARY', function($excel) use ($sheetData) {
    
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('REPORT PRODUCT SUMMARY');
            $excel->setDescription('REPORT PRODUCT SUMMARY');
    
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($sheetData) {
                $sheet->fromArray($sheetData);
            });
    
        })->download('xls');
    }
}

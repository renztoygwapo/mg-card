<?php

namespace App\Http\Controllers\Api;

use App\PriceSummary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class PriceSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = PriceSummary::latest();

       if ($request->filter_date) {
           $items = $items->where('product_date', $request->filter_date)->get();

           $items = $items->map(function ($val) {
               return [
                   'UPDATE DATE' => $val->product_date,
                   'SHIFT' => (string) $val->shift,
                   'ENCODED BY' => $val->encoded_by,
                   'PRODUCT' => $val->product->product_name,
                   'PRICE' => $val->updated_product_price,
               ];
           });
       } else {
           $items = $items->get();
           $items = $items->map(function ($val) {
               return [
                   'UPDATE DATE' => $val->product_date,
                   'SHIFT' => (string) $val->shift,
                   'ENCODED BY' => $val->encoded_by,
                   'PRODUCT' => $val->product->product_name,
                   'PRICE' => $val->updated_product_price,
               ];
           });
       }

       $this->convertToExcel($items);
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
    public function destroy($id)
    {
        //
    }

    public function convertToExcel($sheetData)
    {
        $sheetData = collect($sheetData);
        
        Excel::create('REPORT PRICE UPDATE SUMMARY', function($excel) use ($sheetData) {
    
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('REPORT PRICE UPDATE SUMMARY');
            $excel->setDescription('REPORT PRICE UPDATE SUMMARY');
    
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($sheetData) {
                $sheet->fromArray($sheetData);
            });
    
        })->download('xls');
    }
}

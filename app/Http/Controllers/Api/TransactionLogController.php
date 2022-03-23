<?php

namespace App\Http\Controllers\Api;

use App\TransactionLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class TransactionLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $items = TransactionLog::latest();
        
        if ($request->month != 'null') {
            $items = $items
                    ->whereMonth('created_at', $request->month)
                    ->whereYear('created_at', $request->year)  
                    ->get();
      
            $items = $items->map(function ($val) {
                return [
                    'CUSTOMER' => $val->customer->first_name.' '.$val->customer->middle_initial.' '.$val->customer->last_name,
                    'CUSTOMER GROUP' => $val->customerGroup->name,
                    'PRODUCT' => $val->product->product_name,
                    'LITER' => $val->liters,
                    'PRICE' => $val->price,
                    'AMOUNT' => $val->amount,
                    'POINTS' => $val->points,
                    'SHIFT' => (string) $val->shift,
                    'REMARKS' => $val->remarks,
                    'ENCODED BY' => $val->encoded_by,
                    'ENCODED DATE' => $val->created_at->toDateString(),
                    'ENCODED TIME' => date('h:i:s', strtotime($val->created_at->toTimeString())),
                ];
            });
        } else {
            $month = date('m');
            $month = date('Y');
            $items = $items
                    ->whereMonth('created_at', $request->month)
                    ->whereYear('created_at', $request->year)  
                    ->get();
            $items = $items->map(function ($val) {
                return [
                    'CUSTOMER' => $val->customer->first_name.' '.$val->customer->middle_initial.' '.$val->customer->last_name,
                    'CUSTOMER GROUP' => $val->customerGroup->name,
                    'PRODUCT' => $val->product->product_name,
                    'LITER' => $val->liters,
                    'PRICE' => $val->price,
                    'AMOUNT' => $val->amount,
                    'POINTS' => $val->points,
                    'SHIFT' => (string) $val->shift,
                    'REMARKS' => $val->remarks,
                    'ENCODED BY' => $val->encoded_by,
                    'ENCODED DATE' => $val->created_at->toDateString(),
                    'ENCODED TIME' => date('h:i:s', strtotime($val->created_at->toTimeString())),
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

        Excel::create('TRANSACTION LOGS', function($excel) use ($sheetData) {
    
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('TRANSACTION LOGS');
            $excel->setDescription('TRANSACTION LOGS');
    
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($sheetData) {
                $sheet->fromArray($sheetData);
            });
    
        })->download('xls');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\PointSystemSummary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class PointSystemSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = PointSystemSummary::latest();

       if ($request->filter_date) {
           $items = $items->whereDate('created_at', $request->filter_date)->get();

            $items = $items->map(function ($val) {
                return [
                    'DATE UPDATED' => $val->created_at->toDateString(),
                    'TIME UPDATED' => $val->created_at->toTimeString(),
                    'SHIFT' => (string) $val->shift,
                    'CUSTOMER GROUP' => $val->customerGroup->name,
                    'ENCODED BY' => $val->encoded_by,
                    'PRODUCT NAME' => $val->product->product_name,
                    'POINTS PER LITER' => $val->equivalent_points,
                ];
            });
        } else {
            $items = $items->get();
            $items = $items->map(function ($val) {
                return [
                    'DATE UPDATED' => $val->created_at->toDateString(),
                    'TIME UPDATED' => $val->created_at->toTimeString(),
                    'SHIFT' => (string) $val->shift,
                    'CUSTOMER GROUP' => $val->customerGroup->name,
                    'ENCODED BY' => $val->encoded_by,
                    'PRODUCT NAME' => $val->product->product_name,
                    'POINTS PER LITER' => $val->equivalent_points,
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
        
        Excel::create('REPORT POINT SYSTEM SUMMARY', function($excel) use ($sheetData) {
    
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('REPORT POINT SYSTEM SUMMARY');
            $excel->setDescription('REPORT POINT SYSTEM SUMMARY');
    
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($sheetData) {
                $sheet->fromArray($sheetData);
            });
    
        })->download('xls');
    }
}

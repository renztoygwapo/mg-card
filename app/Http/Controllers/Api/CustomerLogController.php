<?php

namespace App\Http\Controllers\API;

use App\CustomerLog;
use App\Customer;
use App\Transaction;
use App\Redeem;
use App\CustomerGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class CustomerLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = CustomerLog::latest();

       if ($request->filter_date) {
           $items = $items->where('created_at', $request->filter_date)->get();

           $items = $items->map(function ($val) {
               return [
                    'DATE' => $val->created_at->toDateString(),
                    'BARCODE' => $val->customer->barcode,
                    'REFERENCE NO.' => $val->customer->reference_no,
                    'CUSTOMER NAME' => $val->customer->first_name.' '.$val->customer->middle_initial.' '.$val->customer->last_name,
                    'CUSTOMER GROUP' => CustomerGroup::where('id', $val->customer->customer_group_id)->value('name'),
                    'ADDRESS' => $val->customer->address,
                    'BIRTHDATE' => $val->customer->birthdate,
                    'MOBILE NO' => $val->customer->mobile_no,
                    'POINTS' => $val->customer->points,
                    'LITERS' => $val->customer->liters,
                    'VEHICLES' => $val->customer->vehicles,
                    'EXPIRE AT' => $val->customer->expire_at,
                    'STATUS' => $val->customer->status? 'ACTIVE':'INACTIVE',
                    'SHIFT' => (string) $val->shift,
                    'ENCODED BY' => $val->encoded_by
               ];
           });
           $fileName = 'CUSTOMER LOGS';
       } else {
           $items = $items->get();
           $items = $items->map(function ($val) {
               return [
                    'REFERENCE NO.' => $val->reference_no,
                    'CUSTOMER' => $val->first_name.' '.$val->middle_initial.' '.$val->last_name,
                    'ADDRESS' => $val->address,
                    'CONTACT NO' => $val->mobile_no,
                    'BIRTHDATE' => $val->birthdate,
                    'BARCODE' => $val->barcode,
                    'POINTS' => $val->points,
                    'DATE REGISTERED' => $val->created_at->toDateString(),
                    'DATE EXPIRY' => $val->expire_at,
                    'STATUS' => $val->status ? 'ACTIVE' : 'INACTIVE',
                    'GROUP' => $val->customer_group->name,
                    'VEHICLE' => $val->vehicles,
                ];
           });
           $fileName = 'CUSTOMER LOGS';
       }

       $this->convertToExcel($items, $fileName);
    }

    public function cardVolume(Request $request) {
        $items = Customer::whereMonth('created_at', $request->month)->whereYear('created_at', $request->year)->get();
        $items = $items->map(function ($val) {
            return [
                'DATE REGISTERED' => $val->created_at->toDateString(),
                'BARCODE' => $val->barcode,
                'CUSTOMER' => $val->first_name.' '.$val->middle_initial.' '.$val->last_name,
                'ADDRESS' => $val->address,
                'CONTACT NO' => $val->mobile_no,
                'BIRTHDATE' => $val->birthdate,
                'POINTS' => $val->points,
                'DATE EXPIRY' => $val->expire_at,
                'STATUS' => $val->status ? 'ACTIVE' : 'INACTIVE',
                'GROUP' => $val->customer_group->name,
                'VEHICLE' => $val->vehicles,
            ];
        });
        $fileName = 'CARD VOLUME';
        $this->convertToExcel($items, $fileName);   
    }

    public function earnedPoints (Request $request) {
        $items = Transaction::whereMonth('created_at', $request->month)->whereYear('created_at', $request->year)->get();
        $month = date('F', mktime(0, 0, 0, $request->month, 10));
        $items = $items->map(function ($val) {
            return [
                'DATE' => $val->created_at->toDateString(),
                'ENCODED TIME' => date('h:i:s', strtotime($val->created_at->toTimeString())),
                'CUSTOMER' => $val->customer->first_name.' '.$val->customer->last_name,
                'PRODUCT' => $val->product->product_name,
                'UNIT PRICE' => $val->price,
                'LITERS/QTY' => $val->liters,
                'DEBIT' => $val->liters,
                'TOTAL AMOUNT' => $val->amount,
                'SHIFT' => (string) $val->shift,
                'CUSTOMER GROUP' => $val->customer_group->name,
                'VEHICLE' => $val->customer->vehicles,
                'BARCODE' => $val->customer->barcode,
                'EARNED POINTS' => $val->points,
            ];
        });
        $fileName = 'TOTAL EARNED POINTS AS OF' . ' ' . $month;
        $this->convertToExcel($items, $fileName);
    }

    public function unredeemedPoints (Request $request) {
        $items = Customer::where('points', '>', 0)->get();
        $items = $items->map(function ($val) {
            return [
                'DATE' => $val->created_at->toDateString(),
                'BARCODE' => $val->barcode,
                'REFERENCE NO.' => $val->reference_no,
                'CUSTOMER NAME' => $val->first_name.' '.$val->middle_initial.' '.$val->last_name,
                'CUSTOMER GROUP' => CustomerGroup::where('id', $val->customer_group_id)->value('name'),
                'ADDRESS' => $val->address,
                'BIRTHDATE' => $val->birthdate,
                'MOBILE NO' => $val->mobile_no,
                'POINTS' => $val->points,
                'LITERS' => $val->liters,
                'VEHICLES' => $val->vehicles,
                'EXPIRE AT' => $val->expire_at,
                'STATUS' => $val->status? 'ACTIVE':'INACTIVE',
                'SHIFT' => (string) $val->shift,
                'ENCODED BY' => $val->encoded_by
           ];
        });
        $fileName = 'TOTAL UNREDEEMED POINTS';
        $this->convertToExcel($items, $fileName);
    }

    public function totalRedeem (Request $request) {
        $items = Redeem::whereMonth('created_at', $request->month)->whereYear('created_at', $request->year)->get();
        $month = date('F', mktime(0, 0, 0, $request->month, 10));
        $items = $items->map(function ($val) {
            return [
                'SERIAL NUMBER' => $val->serial_no,
                'CUSTOMER NAME' => $val->customer->first_name.' '.$val->customer->middle_initial.' '.$val->customer->last_name,
                'AMOUNT REDEEM' => $val->amount,
                'CONVERTION DATE' => $val->created_at->toDateString(),
                'POINTS CONVERTION' => $val->amount,
                'SHIFT' => $val->shift
            ];
        });
        $fileName = 'TOTAL REDEEM POINTS AS OF' . ' ' . $month;
        $this->convertToExcel($items, $fileName);
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

    public function convertToExcel($sheetData, $fileName)
    {
        $sheetData = collect($sheetData);
        $fileName= $fileName;
        
        Excel::create($fileName, function($excel) use ($sheetData, $fileName) {
    
            // Set the spreadsheet title, creator, and description
            $excel->setTitle($fileName);
            $excel->setDescription($fileName);
    
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($sheetData) {
                $sheet->fromArray($sheetData);
            });
    
        })->download('xls');
    }
}

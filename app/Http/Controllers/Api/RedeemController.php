<?php

namespace App\Http\Controllers\Api;

use App\Redeem;
use App\Customer;
use App\RedeemLog;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\Redeem\Store;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Helpers\ItemWrapper;
use Carbon\Carbon;

class RedeemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Redeem::with(['customer'])->latest();
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        //filter by customer
        if ($request->filter_by_customer) {
            $items = $items->where('customer_id', $request->filter_by_customer);
        }

        // filter by date only
        if ($request->filter_customer_date) {
            $items = $items->whereDate('created_at', $request->filter_customer_date);
        }
        
        // filter transactions by date
        if ($request->has('from_date')) {
            $items = $items->whereDate('created_at', '>=', $fromDate);
        }
        
        if ($request->has('to_date')) {
           $items = $items->whereDate('created_at', '<=', $toDate);
        }

        // filter by shift
        if ($request->filter_by_shift) {
            $items = $items->where('shift', $request->filter_by_shift);
        }

        if ($request->export) {
            $items = $items->get();
            $fileName = '';

            $items = $items->map(function ($val) {
                return [
                    'CONVERTION DATE' => $val->created_at->toDateString(),
                    'SERIAL NUMBER' => $val->serial_no,
                    'CUSTOMER NAME' => $val->customer->first_name.' '.$val->customer->middle_initial.' '.$val->customer->last_name,
                    'POINTS CONVERTION' => (string)($val->amount + $val->tax),
                    'TAX' => $val->tax,
                    'AMOUNT REDEEM' => $val->amount,
                    'SHIFT' => $val->shift
                ];
            });
            
            $fileName = 'POINTS REDEEM';
            $this->convertToExcel($items, $fileName);

        } else if ($request->month && $request->year) {
            $fileName = '';
            
            $items = Redeem::whereMonth('created_at', $request->month)->whereYear('created_at', $request->year)->get();
            
            $items = $items->map(function ($val) {
                return [
                    'CONVERTION DATE' => $val->created_at->toDateString(),
                    'SERIAL NUMBER' => $val->serial_no,
                    'CUSTOMER NAME' => $val->customer->first_name.' '.$val->customer->middle_initial.' '.$val->customer->last_name,
                    'POINTS CONVERTION' => $val->amount + $val->tax,
                    'TAX' => $val->tax,
                    'AMOUNT REDEEM' => $val->amount,
                    'SHIFT' => $val->shift
                ];
            });
            
            $fileName = 'POINTS REDEEM';
            $this->convertToExcel($items, $fileName);

        } else if ($request->export_by_customer) {
            
            $items = $items->where('customer_id', $request->export_by_customer)->get();
            $customer = Customer::where('id', $request->export_by_customer)->get();
            $fileName = '';

            $items = $items->map(function ($val) {
                return [
                    'CONVERTION DATE' => $val->created_at->toDateString(),
                    'SERIAL NUMBER' => $val->serial_no,
                    'CUSTOMER NAME' => $val->customer->first_name.' '.$val->customer->middle_initial.' '.$val->customer->last_name,
                    'POINTS CONVERTION' => $val->amount + $val->tax,
                    'TAX' => $val->tax,
                    'AMOUNT REDEEM' => $val->amount,
                    'SHIFT' => $val->shift
                ];
            });
            
            $fileName = 'Points Redeem of ' .$customer[0]['first_name']. ' ' .$customer[0]['last_name'];
            $this->convertToExcel($items, $fileName);
            
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

    public function getDays($number_of_days)
    {
        $array = collect();
        for($i = 1; $i <= $number_of_days; $i++) {
            $array->push($i);
        }
        return $array;
        // if ($request->day) {
            
        // }
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
            $redeem = Redeem::where('customer_id', $request->customer_id)
                                      ->whereDate('created_at', '=', now()->toDateString())
                                      ->get();
            if (count($redeem) < 1) {

            $points = $request->points;
            $amount = $request->amount;

            $item = new Redeem();
            $item->customer_id = $request->customer_id;
            $item->amount = $request->amount;
            $item->tax = $request->tax;
            $item->serial_no = $request->serial_no;
            $item->shift = $request->shift;
            $item->redeem_type = $request->redeem_type;
            $item->save();
            $item->customer;

            $redeem_log = RedeemLog::create([
                'redeem_id' => $item->id,
                'customer_id' => $request->customer_id,
                'amount' => $request->amount,
                'tax' => $request->tax,
                'serial_no' => $request->serial_no,
                'shift' => $request->shift,
                'encoded_by' => $request->username,
                'redeem_type' => $request->redeem_type
            ]);

            if ($request->customer_redeem) {
                foreach ($request->customer_redeem as $cus_redeem) {
                    //update customer points (points - amount) 
                    $customer = Customer::findOrFail($cus_redeem);
                    $customer->points = 0;
                    $customer->save();
                }
            } else {
                //update customer points (points - amount)
                $total_amount = $request->amount + $request->tax;
                $customer = Customer::findOrFail($request->customer_id);
                $customer->points -= $total_amount;
                $customer->save();
            }
            

            // return response()->json($item,200); 
            } else {
                return response()->json([
                    'message' =>'Single Redeem Only per Day!'
                ], 500);
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
            $item = Redeem::where('id', $id)->with(['customer'])->get();
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
        try {
            $item = Redeem::findOrFail($id);
            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
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

    public function testPrint(Request $request)
    {   
        $date = Carbon::now()->format("Y-m-d");

        try {
            $connector = null;
            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);

            /* Header */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("MYGAS PETROLEUM CORPORATION\n");
            $printer->text($request->station . "\n");
            $printer->text("Motorista Loyalty Card\n");
            $printer -> selectPrintMode();
            $printer -> feed();

            /* Title of receipt */
            $printer -> setEmphasis(true);
            $printer->text("REDEEMPTION\n");
            $printer -> setEmphasis(false);
            $printer->text("DATE: ". $date . "\n");
            $printer -> feed();

            /* body */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Customer: " . $request->name . "\n");
            $printer->text("Barcode: " . $request->barcode . "\n");
            if($request->tin) {
                $printer->text("Tin: " . $request->tin . "\n");
            }
            $printer -> feed();

            /* value */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $amount_redeem = $request->redeem - $request->tax;
            $items = array(
                new ItemWrapper("Total Points: ", $request->total_points),
                new ItemWrapper("Points Redeem: ", round($request->redeem, 2)),
                new ItemWrapper("Tax: ", round( $request->tax, 2)),
                new ItemWrapper("Amount Redeem: ", round( $amount_redeem, 2)),
                new ItemWrapper("Points Remaining: ", round( $request->remaining_points, 2)),
                new ItemWrapper("Promo Avail: ", $request->promo),
            );

            $printer -> setEmphasis(true);
            foreach ($items as $item) {
                $printer -> text($item);
            }
            $printer -> setEmphasis(false);
            $printer -> feed();

            $printer->text("Transact By: " . $request->transact_by . "\n");
            $printer -> feed();

            /* footer */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setEmphasis(true);
            $printer->text("** THANK YOU! **\n");
            $printer -> setEmphasis(false);
            $printer -> feed(2);

            $printer->cut();
            
            /* Close printer */
            $printer -> close();

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

}

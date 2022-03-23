<?php

namespace App\Http\Controllers\Api;

use App\Product;
use App\Customer;
use App\PointSystem;
use App\Transaction;
use App\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\Transaction\Store;
use App\Http\Requests\Transaction\Update;
use App\Http\Requests\Transaction\CustomerId;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Helpers\ItemWrapper;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Transaction::with(['customer','product','customer_group'])->latest();
        $temp = 0;
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // filter transaction by customer
        if ($request->filter_by_customer) {
            $items = $items->where('customer_id', $request->filter_by_customer);
        }

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

            //total running points
            // foreach ($items as $item) {
            //     $temp += $item->points;
            //     $tem = $temp -= $item->redeem->amount;
            //     $item['running_points'] = $tem;
            // }
            
            $items = $items->map(function ($val) {
                return [
                    'DATE' => $val->created_at->toDateString(),
                    'ENCODED TIME' => date('h:i:s', strtotime($val->created_at->toTimeString())),
                    'PRODUCT' => $val->product->product_name,
                    'UNIT PRICE' => $val->price,
                    'LITERS/QTY' => $val->liters,
                    'DEBIT' => $val->liters,
                    'TOTAL AMOUNT' => $val->amount,
                    'RUNNING POINTS' => $val->running_points,
                    'SHIFT' => (string) $val->shift,
                    'CUSTOMER' => $val->customer->first_name.' '.$val->customer->last_name,
                    'CUSTOMER GROUP' => $val->customer_group->name,
                    'VEHICLE' => $val->customer->vehicles,
                    'BARCODE' => $val->customer->barcode,
                    'EARNED POINTS' => $val->points,
                ];
            });
            $fileName = 'CUSTOMER TRANSACTION';
            $this->convertToExcel($items, $fileName);

        } else if ($request->export_by_customer) {

            $items = $items->where('customer_id', $request->export_by_customer)->get();
            $customer = Customer::where('id', $request->export_by_customer)->get();
            $fileName = '';

            //total running points
            // foreach ($items as $item) {
            //     $temp += $item->points;
            //     $tem = $temp -= $item->redeem->amount;
            //     $item['running_points'] = $tem;
            // }
            
            $items = $items->map(function ($val) {
                return [
                    'DATE' => $val->created_at->toDateString(),
                    'ENCODED TIME' => date('h:i:s', strtotime($val->created_at->toTimeString())),
                    'PRODUCT' => $val->product->product_name,
                    'UNIT PRICE' => $val->price,
                    'LITERS/QTY' => $val->liters,
                    'DEBIT' => $val->liters,
                    'TOTAL AMOUNT' => $val->amount,
                    'RUNNING POINTS' => $val->running_points,
                    'SHIFT' => (string) $val->shift,
                    'CUSTOMER' => $val->customer->first_name.' '.$val->customer->last_name,
                    'CUSTOMER GROUP' => $val->customer_group->name,
                    'VEHICLE' => $val->customer->vehicles,
                    'BARCODE' => $val->customer->barcode,
                    'EARNED POINTS' => $val->points,
                ];
            });
            $fileName = 'Transactions of ' .$customer[0]['first_name']. ' ' .$customer[0]['last_name'];
            $this->convertToExcel($items, $fileName);
        return $items;

        } else {
             // check if the items has page and limit
             if ($request->has('page')) {
                $limit= (!empty($request['limit'])) ? $request['limit'] : 50;
                $items= $items->paginate($limit);
            } else {
                $items= $items->get();
            }
        }

        //total running points
        foreach ($items as $item) {
            $temp += $item->points; 
            $item['running_points'] = $temp;
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
            $transaction = Transaction::where('customer_id', $request->customer_id)
                                      ->where('shift', $request->shift)
                                      ->whereDate('created_at', now()->toDateString())
                                      ->get();
            // if (count($transaction) < 3) {
                // get the value of customer_group_id of customer
                $customer_group_id = Customer::where('id', $request->customer_id)
                            ->value('customer_group_id');

                // get the value of equivalent points of specific product and group in the point system 
                $equivalent_points = PointSystem::where('customer_group_id', $customer_group_id)
                            ->where('product_id', $request->product_id)
                            ->value('equivalent_points');

                //compute points in every transaction
                $points = $request->liters * $equivalent_points;
                $liters = $request->liters;

                $item = new Transaction();
                $item->customer_id = $request->customer_id;
                $item->product_id = $request->product_id;
                $item->customer_group_id = $request->customer_group_id;
                $item->liters = $request->liters;
                $item->price = $request->price;
                $item->amount = $request->amount;
                $item->points = $request->points;
                $item->shift = $request->shift;
                $item->remarks = $request->remarks;
                $item->save();
                $item->customer;
                $item->product;
                $item->customer_group;

                $transaction_log = new TransactionLog();
                $transaction_log->transaction_id = $item->id;
                $transaction_log->customer_id = $request->customer_id;
                $transaction_log->product_id = $request->product_id;
                $transaction_log->customer_group_id = $request->customer_group_id;
                $transaction_log->liters = $request->liters;
                $transaction_log->price = $request->price;
                $transaction_log->amount = $request->amount;
                $transaction_log->points = $request->points;
                $transaction_log->shift = $request->shift;
                $transaction_log->remarks = $request->remarks;
                $transaction_log->encoded_by = $request->username;
                $transaction_log->save();

                //update customer points (current points + the points of each transaction) 
                $customer = Customer::findOrFail($request->customer_id);
                $customer->points += $points;
                $customer->liters += $liters;
                $customer->save();

                return response()->json($item,200); 
            // } else {
            //     return response()->json([
            //         'message' =>'Transaction limit has exceeded for this shift.'
            //     ], 500);
            // }
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
            $item = Transaction::with(['customer','product','customer_group'])->where('id', $id)->get();
            return response()->json($item,200); 
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function showByCustomerId(CustomerId $request)
    {
        try {
            $item = Transaction::with(['customer','product'])->where('customer_id', $request->customer_id)->latest()->get();
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
    // public function showTransactionProductPoints() {
    //     $items = Transaction::where('product_id', '=', $this->product_id);
    //             ->sum('points');
    //             return response()->json($items,200);
    // }
    
    public function showTransactionbyCustomer(Request $request, Transaction $transaction) {
        $items = Transaction::with(['customer','product','customer_group']);
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        //filter date only no range
        if ($request->filter_customer_date) {
            $items = $items->whereDate('created_at', $request->filter_customer_date);
        }

        // filter by date range
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

        // filter by customer group
        if ($request->filter_by_customer_group) {
            $items = $items->where('customer_group_id', $request->filter_by_customer_group);
        }

        // filter by customer group
        if ($request->filter_by_product) {
            $items = $items->where('product_id', $request->filter_by_product);
        }

        if ($request->export) {
            $items = $items->get();

            $items = $items->map(function ($val) {
                return [
                    'DATE' => $val->created_at->toDateString(),
                    'ENCODED TIME' => date('h:i:s', strtotime($val->created_at->toTimeString())),
                    'SHIFT' => (string) $val->shift,
                    'CUSTOMER' => $val->customer->first_name.' '.$val->customer->last_name,
                    'CUSTOMER GROUP' => $val->customer_group->name,
                    'VEHICLE' => $val->customer->vehicles,
                    'BARCODE' => $val->customer->barcode,
                    'PRODUCT' => $val->product->product_name,
                    'UNIT PRICE' => $val->price,
                    'LITER/QTY' => $val->liters,
                    'EARNED POINTS' => $val->points,
                    'TOTAL AMOUNT' => $val->amount
                ];
            });
            $fileName = 'ACCUMULATED POINTS SUMMARY';
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
            // get the value of customer_group_id of customer
            $customer_group_id = Customer::where('id', $request->customer_id)
                                         ->value('customer_group_id');

            // get the value of equivalent points of specific product and group in the point system 
            $equivalent_points = PointSystem::where('customer_group_id', $customer_group_id)
                                            ->where('product_id', $request->product_id)
                                            ->value('equivalent_points');
            
            //compute points in every transaction
            $points = $request->liters * $equivalent_points;

            $item = Transaction::findOrFail($id);
            $item->customer_id = $request->customer_id;
            $item->product_id = $request->product_id;
            $item->customer_group_id = $request->customer_group_id;
            $item->liters = $request->liters;
            $item->price = $request->price;
            $item->amount = $request->amount;
            $item->points = $request->points;
            $item->shift = $request->shift;
            $item->remarks = $request->remarks;
            $item->save();
            $item->customer;
            $item->product;
            $item->customer_group;

            // return $item->id;
            
            $transaction_log = new TransactionLog();
            $transaction_log->transaction_id = $item->id;
            $transaction_log->customer_id = $request->customer_id;
            $transaction_log->product_id = $request->product_id;
            $transaction_log->customer_group_id = $request->customer_group_id;
            $transaction_log->liters = $request->liters;
            $transaction_log->price = $request->price;
            $transaction_log->amount = $request->amount;
            $transaction_log->points = $request->points;
            $transaction_log->shift = $request->shift;
            $transaction_log->encoded_by = $request->username;
            $transaction_log->save();

            //update customer points (current points + the points of each transaction) 
            $customer = Customer::findOrFail($request->customer_id);
            $customer->points += $points;
            // $customer->liters += $liters;
            $customer->save();

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
            $item = Transaction::findOrFail($id);

            $customer = Customer::findOrFail($item->customer_id);
            $customer->points -= $item->points;
            $customer->save();

            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function dailyProductPoints(Request $request)
    {
        $products = Product::get();
        $date = $request->date;
        $month = $request->month;
        $year = $request->year;
        foreach ($products as $product) {
            if ($request->month != 'null') {
                $product['daily_points'] = Transaction::whereMonth('created_at','=', $month)
                                                    ->whereYear('created_at','=', $year)
                                                    ->where('product_id', $product->id)
                                                    ->sum('points');
            
                $product['daily_liters'] = Transaction::whereMonth('created_at','=', $month)
                                                    ->whereYear('created_at','=', $year)
                                                    ->where('product_id', $product->id)
                                                    ->sum('liters');
            } else {
                $product['daily_points'] = Transaction::whereDate('created_at','=', $date)
                                                    ->where('product_id', $product->id)
                                                    ->sum('points');
            
                $product['daily_liters'] = Transaction::whereDate('created_at','=', $date)
                                                    ->where('product_id', $product->id)
                                                    ->sum('liters');
            }
        }

        return $products;
    }
    
    public function convertToExcel($sheetData, $fileName)
    {
        $sheetData = collect($sheetData);
        $fileName = $fileName;
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

    public function subtractPoints(Request $request) 
    {
        $points = $request->points;

        $customer = Customer::findOrFail($request->customer_id);
        $customer->points -= $points;
        $customer->save();
    }

    // test print
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
            $printer->text("TRANSACTION\n");
            $printer -> setEmphasis(false);
            $printer->text("DATE: ". $date . "\n");
            $printer -> feed();

            /* body */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Customer: " . $request->name . "\n");
            $printer->text("Barcode: " . $request->barcode . "\n");
            $printer -> feed();

            /* value */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $items = array(
                new ItemWrapper("Product: ", $request->product),
                new ItemWrapper("Amount: ", $request->amount),
                new ItemWrapper("Total Liters: ", $request->total_liters),
                new ItemWrapper("Total Points Avail: ", round($request->total_points, 2)),
                new ItemWrapper("Current Points", round($request->points, 2)),
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

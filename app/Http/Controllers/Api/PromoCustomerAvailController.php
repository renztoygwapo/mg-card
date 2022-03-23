<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\PromoCustomerAvail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PromoCustomerAvail\Store;
use App\Http\Requests\PromoCustomerAvail\Update;

class PromoCustomerAvailController extends Controller
{
    public function index (Request $request) {

        $items = PromoCustomerAvail::with(['customer','promo_item'])->whereDate('date_expired', '>=',Carbon::today()->toDateString())->where('isAvail', '=', 'false');

        if ($request->customer_id) {
            $items = $items->where('customer_id', '=', $request->customer_id);
        }

        if ($request->export) {
            $item = PromoCustomerAvail::with(['customer','promo_item']);
            $items = $item->get();

            $items = $items->map(function ($val) {
                return [
                    'BARCODE' => $val->customer->barcode,
                    'CUSTOMER NAME' => $val->customer->first_name.' '.$val->customer->middle_initial.' '.$val->customer->last_name,
                    'POINTS' => $val->customer->points,
                    'MOBILE NUMBER' => $val->customer->mobile_no
                ];
            });
 
            $this->convertToExcel($items);

        } else if ($request->export_availed) {
            $item = PromoCustomerAvail::with(['customer','promo_item'])->where('isAvail', '=', 'true');
            $items = $item->get();

            $items = $items->map(function ($val) {
                return [
                    'SERIAL NO' => $val->serial_no,
                    'BARCODE' => $val->customer->barcode,
                    'CUSTOMER NAME' => $val->customer->first_name.' '.$val->customer->middle_initial.' '.$val->customer->last_name,
                    'POINTS' => $val->customer->points,
                    'MOBILE NUMBER' => $val->customer->mobile_no
                ];
            });
 
            $this->convertToExcelAvailed($items);
        } else {
            // check if the items has page and limit
            if ($request->has('page')) {
                $limit= (!empty($request['limit'])) ? $request['limit'] : 10;
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

    public function store(Store $request)
    {
        try {
            $promocustomeravail = PromoCustomerAvail::create($request->only(
                ['customer_id', 'promo_id','date_expired','isAvail']
            ));
            return $promocustomeravail;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function update(Update $request, $id)
    {
        try {
            $item = PromoCustomerAvail::findOrFail($id);
            $item->date_expired = $request->date_expired;
            $item->promo_id = $request->promo_id;
            $item->serial_no = $request->serial_no;
            $item->customer_id = $request->customer_id;
            $item->isAvail = $request->isAvail;
            $item->tag = $request->tag;
            $item->save();
            return response()->json($item,200); 
        } catch (\Exception $exception) {
            throw $exception;
        }
    }


    public function getTagCustomer (Request $request) {
        $items = PromoCustomerAvail::with(['customer','promo_item'])->where('tag', 0)->whereDate('date_expired','>=',Carbon::today()->toDateString());
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
    public function convertToExcel($sheetData)
    {
        $sheetData = collect($sheetData);
        
        Excel::create('TAGGED CUSTOMER', function($excel) use ($sheetData) {
    
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('TAGGED CUSTOMER');
            $excel->setDescription('TAGGED CUSTOMER');
    
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($sheetData) {
                $sheet->fromArray($sheetData);
            });
    
        })->download('xls');
    }

    public function convertToExcelAvailed($sheetData)
    {
        $sheetData = collect($sheetData);
        
        Excel::create('PROMO AVAILED BY CUSTOMER', function($excel) use ($sheetData) {
    
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('PROMO AVAILED BY CUSTOMER');
            $excel->setDescription('PROMO AVAILED BY CUSTOMER');
    
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($sheetData) {
                $sheet->fromArray($sheetData);
            });
    
        })->download('xls');
    }
}

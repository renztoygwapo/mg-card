<?php

namespace App\Http\Controllers\Api;

use App\PromoItem;
use Carbon\Carbon;
use App\PromoCustomerAvail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PromoItem\Store;
use App\Http\Requests\PromoItem\Update;

class PromoItemController extends Controller
{
    public function index(Request $request)
    {
        $items = PromoItem::whereDate('date_end', '>=',Carbon::today()->toDateString())->latest();

        if ($request->points) {
            $items = $items->where('eq_points', '<=', $request->points)->whereDate('date_end', '>=',Carbon::today()->toDateString());
        }
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

    public function store(Store $request)
    {
        try {
            $promoitem = PromoItem::create($request->only(
                ['name', 'eq_points','point_types','date_start','date_end']
            ));
            return $promoitem;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $item = PromoItem::with('promoitem')->where('id',$id)->get();
            return response()->json($item,200); 
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function update(Update $request, $id)
    {
        try {
            $item = PromoItem::findOrFail($id);
            $item->name = $request->name;
            $item->eq_points = $request->eq_points;
            $item->point_types = $request->point_types;
            $item->date_start = $request->date_start;
            $item->date_end = $request->date_end;
            $item->save();
            return response()->json($item,200); 
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function destroy($id)
    {
        try {
            $item = PromoItem::findOrFail($id);
            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function exportPromoItems ()
    {
        $items = PromoCustomerAvail::with('customer')->latest()->get();
        $items = $items->map(function ($val) {
            // dd($val->customer->customer_group);
            return [
                'NAME' => $val->customer->first_name. ' ' . $val->customer->last_name,
                'BARCODE' => $val->customer->barcode,
                'POINTS' => $val->customer->points,
                'CONTACT NUMBER' => $val->customer->mobile_no,
                'CUSTOMER GROUP' => $val->customer->customer_group->name
            ];
        });

       $this->convertToExcel($items);
    }

    public function convertToExcel($sheetData)
    {
        $sheetData = collect($sheetData);
        
        Excel::create('REPORT PROMO ITEMS SUMMARY', function($excel) use ($sheetData) {
    
            // Set the spreadsheet title, creator, and description
            $excel->setTitle('REPORT PROMO ITEMS SUMMARY');
            $excel->setDescription('REPORT PROMO ITEMS SUMMARY');
    
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($sheetData) {
                $sheet->fromArray($sheetData);
            });
    
        })->download('xls');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Transaction;
use App\Customer;
use Carbon\Carbon;
use App\CustomerLog;
use App\CustomerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Customer\Store;
use App\Http\Requests\Customer\Update;
use App\Http\Requests\Customer\Barcode;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Customer::latest()->with(['customer_group', 'customer_transactions', 'redeem']);
        $fromDate = $request->get('from_date');
        
        //search function 
        if (($request->has('s')) && ($request->has('s_field'))) {
            $fields= explode(',',$request['s_field']);
            foreach ($fields as $key => $field) {
                if ($key==0) {
                    $items= $items->where($field,'LIKE','%'.$request['s'].'%');
                } else {
                    $items= $items->orwhere($field,'LIKE','%'.$request['s'].'%');
                }
            }
        }
        
        if ($request->customer_group) {
            $items = $items->get();

            $items = Customer::where('customer_group_id',$request->customer_group)->with('customer_group');
        }

        if ($request->has('from_date')) {
            $items = Customer::with(['customer_transactions','customer_group'])->whereHas('customer_transactions', function($q) use ($fromDate){
                
                $q->where('created_at', '<=', $fromDate);
                
            });
        }
        if ($request->card_volume) {
            $request->month = 03;
            $items = $items->whereMonth('created_at', $request->month)->get();
            $fileName = '';
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
            $fileName = 'Customer List - '.$request->station;
            
            $this->convertToExcel($items, $fileName);
            
        }

        if ($request->export) {
            $items = $items->get();
            $fileName = '';
            if ($request->type === 'list') {
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
                $fileName = 'Customer List - '.$request->station;
            }

            if ($request->type === 'points') {
                $items = $items->map(function ($val) {
                    return [
                        'REFERENCE NO.' => $val->reference_no,
                        'CUSTOMER' => $val->first_name.' '.$val->middle_initial.' '.$val->last_name,
                        'BARCODE' => $val->barcode,
                        'ADDRESS' => $val->address,
                        'GROUP' => $val->customer_group->name,
                        'TOTAL POINTS' => $val->points,
                        'TOTAL LITERS' => $val->liters
                    ];
                });
                $fileName = 'Customer Points';
            }
            
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

    public function customerHighPoints (Request $request, Transaction $transaction) {

        if ($request->type === 'running') {

            $items = DB::table('customers')
                ->select(
                    'id','first_name','last_name','mobile_no','customer_group_id',
                    DB::raw('(Select sum(points) from transactions where customer_id = customers.id) as points')
                )
                ->latest('points')
                ->where('points', '>=', $request->points)
                ->get();

        } else if ($request->type === 'current') {

            $items = Customer::latest('points')->with(['customer_group'])->where('points', '>=', $request->points)->get();

        } else {
            $items = [];
        }
        
        
        $limit = $request->limit;
        $take = 100;

            if ($request->filter_customer_group) {
                $items = $items->where('customer_group_id', $request->filter_customer_group);
            }

            if ($limit === '0') {
                $items = $items->take($take);
            } else {
                $items = $items->take($limit);
            }

            if ($request->export) {
                $group = $request->filter_customer_group_id;
                $item_limit = $items->take($take);
                $items = $item_limit->where('customer_group_id', $group);
                $customer_group = CustomerGroup::where('id', $group)->get();
                $fileName = '';
    
                $items = $items->map(function ($val) {
                    return [
                        'BARCODE' => $val->barcode,
                        'CUSTOMER NAME' => $val->first_name.' '.$val->middle_initial.' '.$val->last_name,
                        'POINTS' => $val->points,
                        'MOBILE NUMBER' => $val->mobile_no
                    ];
                });
                $fileName = 'TOP CUSTOMERS OF '.$customer_group[0]['name'];
                
                $this->convertToExcel($items, $fileName);
            }

        if (!empty($items)) {
            try {
               return response()->json(array_values($items->toArray()),200); 
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
            // $expire_date = date('Y-m-d', strtotime('+1 year'));
            $customer = Customer::create([
                'first_name' => request('first_name'),
                'middle_initial' => request('middle_initial'),
                'last_name' => request('last_name'),
                'barcode' => request('barcode'),
                'reference_no' => request('reference_no'),
                'address' => request('address'),
                'mobile_no' => request('mobile_no'),
                'birthdate' => request('birthdate'),
                'expire_at' => request('expire_at'),
                'customer_group_id' => request('customer_group_id'),
                'vehicles' => request('vehicles'),
                'is_admin' => request('is_admin'),
                'plate_no' => request('plate_no'),
                'is_admin' => request('is_admin')
            ]);

            $customer_log = CustomerLog::create([
                'customer_id' => $customer->id,
                'shift' => $request->shift,
                'encoded_by' => $request->username
            ]);
            
            return $customer;
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
            $item = Customer::where('id', $id)->with('customer_group')->get();
            return response()->json($item,200); 
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function showByBarcode(Barcode $request)
    {
        try {
            $item = Customer::where('barcode', $request->barcode)->with(['customer_group','customer_transactions']);
            $item = $item->latest()->get();
            return response()->json($item,200); 
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function showCustomerCurrentPoints(Request $request, Customer $customer)
    {
        $customer = $customer->newQuery();

        if ($request->has('customer_group_id')) {
            $customer->where('customer_group_id', $request->customer_group_id);
        }

        if ($request->has('search_keyword')) {
            $customer->where(function ($query) use ($request){
                $query->where('address', 'LIKE', '%'.$request->search_keyword.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$request->search_keyword.'%')
                    ->orWhere('barcode', 'LIKE', '%'.$request->search_keyword.'%')
                    ->orWhere('points', 'LIKE', '%'.$request->search_keyword.'%')   
                    ->orWhere('first_name', 'LIKE', '%'.$request->search_keyword.'%');
            });
        }

        if (!empty($customer)) {
            try {
                $limit= (!empty($request['limit'])) ? $request['limit'] : 10;
                $customers = $customer->with('customer_group')->paginate($limit);
                return response()->json($customers,200); 
            } catch(\Exception $e) {
                return response()->json("Error.",400);
            }
        } else {
            return response()->json("0 items found.",404);
        }
    }

    public function export()
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
            $item = Customer::findOrFail($id);
            $item->barcode = $request->barcode;
            $item->reference_no = $request->reference_no;
            $item->first_name = $request->first_name;
            $item->middle_initial = $request->middle_initial;
            $item->last_name = $request->last_name;
            $item->address = $request->address;
            $item->last_name = $request->last_name;
            $item->mobile_no = $request->mobile_no;
            $item->birthdate = $request->birthdate;
            $item->customer_group_id = $request->customer_group_id;
            $item->vehicles = $request->vehicles;
            $item->expire_at = $request->expire_at;
            $item->save();

            $customer_log = CustomerLog::create([
                'customer_id' => $item->id,
                'shift' => $request->shift,
                'encoded_by' => $request->username
            ]);
            
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
            $item = Customer::findOrFail($id);
            $item->delete();
            return response()->json('Success',200);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function importCustomerData(Request $request)
    {
        if($request->hasFile('import_file')){
			$path = $request->file('import_file')->getRealPath();
            $data = Excel::load($path)->get();
			if($data->count()){
				foreach ($data as $key => $value) {
                        $customer_group_id = CustomerGroup::where('name', $value->customer_group)->value('id');
                        $datum[] = [
                            'barcode' => 'mg-' . $value->barcode_plate_no, 
                            'points' => 0,
                            'first_name' => $value->first_name,
                            'middle_initial' => $value->mi,
                            'last_name' => $value->last_name,
                            'address' => $value->address,
                            'birthdate' => $value->birthday,
                            'mobile_no' => $value->contact_no,
                            'customer_group_id' => $customer_group_id,
                            'expire_at' => date('Y-m-d', strtotime('+1 years')),
                            'status' => 1,
                            'vehicles' => $value->vehicle,
                            'created_at' => now()->toDateTimeString(),
                            'updated_at' => now()->toDateTimeString(),
                        ];
                }
                
				if(!empty($datum)){
                    $result = Customer::insert($datum);
                    if ($result) {
                        return 'Records Inserted Successfully!';
                    }
				}
			}
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

}

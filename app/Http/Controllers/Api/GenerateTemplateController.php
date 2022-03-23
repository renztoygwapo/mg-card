<?php

namespace App\Http\Controllers\Api;

use App\Product;
use App\Transaction;
use App\GenerateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\GeneralTemplate\Store;
use App\Http\Requests\GeneralTemplate\Update;

class GenerateTemplateController extends Controller
{
    public function index(Request $request)
    {
        $items = GenerateTemplate::latest();

        $station = $request->station;

        if ($request->export) {
            $year = date('Y');
            $number = (int)cal_days_in_month(CAL_GREGORIAN, $request->month, $year);
            $month = $request->month;

            Excel::create('GENERAL TEMPLATE', function($excel) use($number, $year, $station, $items, $month) {

                $excel->sheet('FINAL', function($sheet) use($number, $station, $year, $items, $month) {
                    $sheet->setWidth(array(
                        'B'     =>  13,
                        'C'     =>  13,
                        'D'     =>  13,
                        'E'     =>  13,
                        'F'     =>  13,
                        'G'     =>  13,
                        'H'     =>  13,
                        'I'     =>  13,
                        'J'     =>  13,
                        'K'     =>  13,
                        'L'     =>  13,
                        'M'     =>  13,
                        'N'     =>  13,
                    ));

                    $sheet->mergeCells('A1:N1');
                    $sheet->mergeCells('A2:N2');
                    $sheet->setMergeColumn(array(
                        'columns' => array('A'),
                        'rows' => array(
                            array(1,2),
                        )
                    ));

                    $sheet->mergeCells('B3:F3');
                    $sheet->mergeCells('C4:D4');

                    $sheet->cell('A1:N1', function($cell) {
                        $cell->setBackground('ffff00');
                    });

                    $sheet->cell('A1', function($cell) use($station) {
                        $cell->setvalue(strtoupper($station));
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(20);
                        $cell->setAlignment('center');
                    });

                    $monthName = date('F', mktime(0, 0, 0, $month, 10));;
                    $sheet->cell('A2', function($cell) use($year,$monthName) {
                        $cell->setvalue(strtoupper($monthName).' '.$year);
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(20);
                        $cell->setAlignment('center');
                    });
                    
                    $sheet->cell('B3', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setBorder('none','thin','none','none');
                        $cell->setFontSize(13);
                        $cell->setValue('LITERS IN CARD');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('A5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('DATE');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('B5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('PREMIUM');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('C5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('UNLEADED');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('D5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('DIESEL');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('E5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('LUBES');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('F5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                    
                    });
                    $days_array = collect($this->getDays($number));
                    $rows = 6;
                    for ($i=1; $i <= $number; $i++) {
                        $cell_num_a = 'A'.''. $rows;
                        $sheet->cell($cell_num_a, function($cell) use($number, $cell_num_a, $i, $days_array, $month) {

                            // manipulate the cell
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(10);
                            $cell->setValue($i);
                            $cell->setAlignment('center');
                        
                        });
                        $items = Transaction::with(['product'])
                                ->select(DB::raw('DATE(created_at) as date'), DB::raw('product_id as product_id'), DB::raw('SUM(liters) as liters'))
                                ->groupBy('date','product_id')
                                ->whereDay('created_at', $i)
                                ->whereMonth('created_at', $month)
                                ->get();

                        $items = collect($items);
                        $items->values();
                        $premiums = $items->where('product.product_name', 'PREMIUM');
                        $item_premiums = collect($premiums);
                        $item_premium = $item_premiums->values();

                        $premium = $item_premium->map(function ($val) {
                            return $val->liters;
                        });

                        $unleadeds = $items->where('product.product_name', 'UNLEADED');
                        $item_unleadeds = collect($unleadeds);
                        $item_unleaded = $item_unleadeds->values();
                        $unleaded = $item_unleaded->map(function ($val) {
                            return $val->liters;
                        });

                        $desiels = $items->where('product.product_name', 'DIESEL');
                        $item_desiels = collect($desiels);
                        $item_desiel = $item_desiels->values();
                        $desiel = $item_desiel->map(function ($val) {
                            return $val->liters;
                        });
            

                        $lubes = $items->where('product.product_name', 'LUBES');
                        $item_lubes = collect($lubes);
                        $item_lube = $item_lubes->values();
                        $lube = $item_lube->map(function ($val) {
                            return $val->liters;
                        });

                        $liters = $items->map(function ($val) {
                            return $val->liters;
                        });

                        if (count($premium)) {
                            $cell_num_b = 'B'.''. $rows;
                            $sheet->cell($cell_num_b, function($cell) use($number, $premium, $liters, $cell_num_b, $i, $days_array) {
                            
                                // manipulate the cell
                                $cell->setFontWeight('bold');
                                $cell->setFontSize(10);
                                $cell->setFontColor('FB6444');
                                $cell->setValue($premium[0]);
                                $cell->setAlignment('center');
                            
                            });
                        }

                        if (count($unleaded)) {
                           
                            $cell_num_c = 'C'.''. $rows;
                            $sheet->cell($cell_num_c, function($cell) use($number, $unleaded, $liters, $cell_num_c, $i, $days_array) {
                        
                            // manipulate the cell
                                $cell->setFontWeight('bold');
                                $cell->setFontSize(10);
                                $cell->setFontColor('FB6444');
                                $cell->setValue($unleaded[0]);
                                $cell->setAlignment('center');
                        
                            });
                        }

                        if (count($desiel)) {
                            $cell_num_d = 'D'.''. $rows;
                            $sheet->cell($cell_num_d, function($cell) use($number, $desiel, $desiels, $liters, $cell_num_d, $i, $days_array) {
                            
                                // manipulate the cell
                                $cell->setFontWeight('bold');
                                $cell->setFontSize(10);
                                $cell->setFontColor('FB6444');
                                $cell->setValue($desiel[0]);
                                $cell->setAlignment('center');
                        
                            });
                        }

                        if (count($lube)) {

                            $cell_num_e = 'E'.''. $rows;
                            $sheet->cell($cell_num_e, function($cell) use($number, $lube, $liters, $cell_num_e, $i, $days_array) {
                            
                                // manipulate the cell
                                $cell->setFontWeight('bold');
                                $cell->setFontSize(10);
                                $cell->setFontColor('FB6444');
                                $cell->setValue($lube[0]);
                                $cell->setAlignment('center');
                            
                            });
                        }   

                            $cell_num_f = 'F'.''. $rows;
                            $sum = '=B'.''.$rows.''.'+C'.''.$rows.''.'+D'.''.$rows.''.'+E'.''.$rows;
                            $sheet->cell($cell_num_f, function($cell) use($number, $sum, $rows, $cell_num_f, $i, $days_array) {
                            
                                // manipulate the cell
                                $cell->setFontWeight('bold');
                                $cell->setFontSize(10);
                                $cell->setValue($sum);
                                $cell->setAlignment('center');
                            
                            });
                        $days = $number+5;
                        $day = $number+6;
                        $sumB = '=SUM(B6:'.''.'B'.''.$days.''.')';
                        $sumC = '=SUM(C6:'.''.'C'.''.$days.''.')';
                        $sumD = '=SUM(D6:'.''.'D'.''.$days.''.')';
                        $sumE = '=SUM(E6:'.''.'E'.''.$days.''.')';
                        $sumF = '=SUM(F6:'.''.'F'.''.$days.''.')';
                        $sumG = '=SUM(G6:'.''.'G'.''.$days.''.')';
                        $sumH = '=SUM(H6:'.''.'H'.''.$days.''.')';
                        $sumI = '=SUM(I6:'.''.'I'.''.$days.''.')';
                        $sumJ = '=SUM(J6:'.''.'J'.''.$days.''.')';
                        $sumK = '=SUM(K6:'.''.'K'.''.$days.''.')';
                        $sumL = '=SUM(L6:'.''.'L'.''.$days.''.')';
                        $sumM = '=SUM(M6:'.''.'M'.''.$days.''.')';
                        $sumN = '=SUM(N6:'.''.'N'.''.$days.''.')';
                        
                        $sheet->cell('F4:'.''.'F'.''.$day, function($cell) use($days) {
                    
                            // manipulate the cell
                            $cell->setBorder('none','thin','none','none');
                        
                        });
                    
                    $item_generate = GenerateTemplate::whereDay('date', $days_array[$i-1])
                        ->whereMonth('date', $month)
                        ->get();
                    
                    $premium_generate = $item_generate->map(function ($val) {
                        return $val->premium;
                    });

                    $unleaded_generate = $item_generate->map(function ($val) {
                        return $val->unleaded;
                    });

                    $desiel_generate = $item_generate->map(function ($val) {
                        return $val->desiel;
                    });

                    $total_generate = $item_generate->map(function ($val) {
                        return $val->total;
                    });

                    if (count($premium_generate)) {
                    
                    $cell_num_b = 'G'.''. $rows;
                    $sheet->cell($cell_num_b, function($cell) use($number, $premium_generate, $liters, $cell_num_b, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setFontColor('FB6444');
                        $cell->setValue($premium_generate[0]);
                        $cell->setAlignment('center');
                    
                        });
                    }

                    if (count($unleaded_generate)) {
                    
                        $cell_num_b = 'H'.''. $rows;
                        $sheet->cell($cell_num_b, function($cell) use($number, $unleaded_generate, $liters, $cell_num_b, $i, $days_array) {
                        
                            // manipulate the cell
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(10);
                            $cell->setFontColor('FB6444');
                            $cell->setValue($unleaded_generate[0]);
                            $cell->setAlignment('center');
                        
                        });
                    }

                    if (count($desiel_generate)) {
                    
                        $cell_num_b = 'I'.''. $rows;
                        $sheet->cell($cell_num_b, function($cell) use($number, $desiel_generate, $liters, $cell_num_b, $i, $days_array) {
                        
                            // manipulate the cell
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(10);
                            $cell->setFontColor('FB6444');
                            $cell->setValue($desiel_generate[0]);
                            $cell->setAlignment('center');
                        
                        });
                    }

                    if (count($total_generate)) {
                    
                        $cell_num_b = 'J'.''. $rows;
                        $sum = '=G'.''.$rows.''.'+H'.''.$rows.''.'+I'.''.$rows;
                        $sheet->cell($cell_num_b, function($cell) use($number, $sum, $liters, $cell_num_b, $i, $days_array) {
                        
                            // manipulate the cell
                            $cell->setFontWeight('bold');
                            $cell->setFontSize(10);
                            $cell->setValue($sum);
                            $cell->setAlignment('center');
                        
                        });
                    }

                    $cell_num_b = 'K'.''. $rows;
                    $sum = '=G'.''.$rows.''.'-B'.''.$rows;
                    $sheet->cell($cell_num_b, function($cell) use($number, $sum, $liters, $cell_num_b, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sum);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_b = 'L'.''. $rows;
                    $sum = '=H'.''.$rows.''.'-C'.''.$rows;
                    $sheet->cell($cell_num_b, function($cell) use($number, $sum, $liters, $cell_num_b, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sum);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_b = 'M'.''. $rows;
                    $sum = '=I'.''.$rows.''.'-D'.''.$rows;
                    $sheet->cell($cell_num_b, function($cell) use($number, $sum, $liters, $cell_num_b, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sum);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_b = 'N'.''. $rows;
                    $sum = '=J'.''.$rows.''.'-F'.''.$rows;
                    $sheet->cell($cell_num_b, function($cell) use($number, $sum, $liters, $cell_num_b, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sum);
                        $cell->setAlignment('center');
                    
                    });

                        ++$rows;

                    }

                    $total_days = $number+6;
                    $cell_num_g = 'B'.''. $total_days;
                    $sheet->cell($cell_num_g, function($cell) use($number, $sumB, $cell_num_g, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumB);
                        $cell->setAlignment('center');
                    
                    });

                    $sheet->cell('B'.''.$total_days.''.':N'.''.$total_days, function($cell) {
                        $cell->setBackground('F7A391');
                    });

                    $cell_num_h = 'C'.''. $total_days;
                    $sheet->cell($cell_num_h, function($cell) use($number, $sumC, $cell_num_h, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumC);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_i = 'D'.''. $total_days;
                    $sheet->cell($cell_num_i, function($cell) use($number, $sumD, $cell_num_i, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumD);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_j = 'E'.''. $total_days;
                    $sheet->cell($cell_num_j, function($cell) use($number, $sumE, $cell_num_j, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumE);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_k = 'F'.''. $total_days;
                    $sheet->cell($cell_num_k, function($cell) use($number, $sumF, $cell_num_k, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumF);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_k = 'G'.''. $total_days;
                    $sheet->cell($cell_num_k, function($cell) use($number, $sumG, $cell_num_k, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumG);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_k = 'H'.''. $total_days;
                    $sheet->cell($cell_num_k, function($cell) use($number, $sumH, $cell_num_k, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumH);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_k = 'I'.''. $total_days;
                    $sheet->cell($cell_num_k, function($cell) use($number, $sumI, $cell_num_k, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumI);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_k = 'J'.''. $total_days;
                    $sheet->cell($cell_num_k, function($cell) use($number, $sumJ, $cell_num_k, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumJ);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_k = 'K'.''. $total_days;
                    $sheet->cell($cell_num_k, function($cell) use($number, $sumK, $cell_num_k, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumK);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_k = 'L'.''. $total_days;
                    $sheet->cell($cell_num_k, function($cell) use($number, $sumL, $cell_num_k, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumL);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_k = 'M'.''. $total_days;
                    $sheet->cell($cell_num_k, function($cell) use($number, $sumM, $cell_num_k, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumM);
                        $cell->setAlignment('center');
                    
                    });

                    $cell_num_k = 'N'.''. $total_days;
                    $sheet->cell($cell_num_k, function($cell) use($number, $sumN, $cell_num_k, $i, $days_array) {
                    
                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(10);
                        $cell->setValue($sumN);
                        $cell->setAlignment('center');
                    
                    });

                    $sheet->mergeCells('G3:J3');
                    $sheet->mergeCells('H4:I4');

                    $sheet->mergeCells('K3:N3');
                    $sheet->mergeCells('L4:M4');

                    $sheet->cell('J4:'.''.'J'.''.$day, function($cell) use($days) {
                    
                        // manipulate the cell
                        $cell->setBorder('none','thin','none','none');
                    
                    });

                    $sheet->cell('G3', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(13);
                        $cell->setBorder('none','thin','none','none');
                        $cell->setValue('TOTAL STATION VOLUME IN LITERS');
                        $cell->setAlignment('center');
                    
                    });

                    $sheet->cell('K3', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(14);
                        $cell->setValue('WALK-IN');
                        $cell->setAlignment('center');
                    
                    });

                    $sheet->cell('G5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('PREMIUM');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('H5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('UNLEADED');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('I5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('DESIEL');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('J5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                    
                    });

                    $sheet->mergeCells('K3:N3');
                    $sheet->mergeCells('L4:M4');

                    $sheet->cell('K3', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(13);
                        $cell->setValue('WALK-IN');
                        $cell->setAlignment('center');
                    
                    });

                    $sheet->cell('K5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('PREMIUM');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('L5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('UNLEADED');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('M5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('DESIEL');
                        $cell->setAlignment('center');
                    
                    });
                    $sheet->cell('N5', function($cell) {

                        // manipulate the cell
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(12);
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                    
                    });
                });
            
            })->export('xls');
            
        } else {
            // check if the items has page and limit
            if ($request->has('page')) {
                $limit= (!empty($request['limit'])) ? $request['limit'] : 15;
                $items= $items->paginate($limit);
            } else {
                $items= $items->get();
            }
        }

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

    public function store(Store $request)
    {
        try {
            $general_template = GenerateTemplate::create($request->only(
                ['date', 'premium', 'unleaded', 'desiel', 'total']
            ));
            return $general_template;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $item = GenerateTemplate::where('id', $id)->get();
            return response()->json($item,200); 
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function update(Update $request, $id)
    {
        try {

            $item = GenerateTemplate::findOrFail($id);
            $item->date = $request->date;
            $item->premium = $request->premium;
            $item->unleaded = $request->unleaded;
            $item->desiel = $request->desiel;
            $item->save();
            return response()->json($item,200); 
            
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function generateConvertoExcel($sheetData, $fileName)
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
                // $sheet->setCellValue('D2','=SUM(B2:C2)');
            });
    
        })->download('xls');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use MarkWalet\DotenvManager\DotenvManager;

class DatabaseController extends Controller
{

    public function setConnection(Request $request)
    {
        $request->validate([
            'db_database' => 'required'
        ]);

        $environment = app(DotenvManager::class);
        $environment->update("DB_DATABASE", $request->db_database);

        Artisan::call('config:cache');

        return response()->json([
            'message' => env('DB_DATABASE') . ' is set as default database',
            'db_database' => env('DB_DATABASE')
        ]);
    }

    public function updateOrCreateConnection(Request $request)
    {
        $request->validate([
            'action' => 'required|in:create,update,test',
            'db_name' => 'required',
            'file' => 'file|required_if:action,update,create'
        ]);

        try {
            $db_name = $request->db_name;
            $host = config('database.connections.mysql.host');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $connection = $request->hasFile('file') ? new \mysqli($host, $username, $password) : mysqli_connect($host, $username, $password, $db_name);
            if ($request->hasFile('file')) {
                $sqlFileQuery = file_get_contents($request->file('file'));
                // dd($sqlFileQuery);
                $appConnection = DB::connection();
                $connection->query("DROP DATABASE IF EXISTS $db_name");
                $connection->query("CREATE DATABASE $db_name");
                $connection->close();
                DB::unprepared("use $db_name; $sqlFileQuery");
            }
        } catch (\Exception $e) {
            return response ()->json($e->getMessage(), 500);
        }
        switch ($request->action) {
            case 'create';
                $message = 'connection created.';
            break;
            case 'update';
                $message = 'connection updated.';
            break;
            case 'test';
                $message = 'connection success.';
            break;
        }
        return response ()->json($message);
    }

    public function dumpDatabase()
    {
        $databaseName = DB::connection()->getDatabaseName();
        $fileName = $databaseName;
        $database = Artisan::call("backup:mysql-dump", ['filename' => $fileName]);
        return $database;
    }

    public function reportTemplate(Request $request)
    {
        // $test2 = md5('codic_testdevtest123');
        // $test1 = md5('DEVC000000000004');
        // // $code = md5(md5('DEVC000000000001') + md5('codic_testdevtest123'));
        // return md5($test1 . $test2);

        $shift = $request->shift;
        $cashier = $request->cashier;

        $date = Carbon::now()->format("Y-m-d");

        $transaction = Transaction::with(['product'])
                                ->select(DB::raw('DATE(created_at) as date'), DB::raw('product_id as product_id'), DB::raw('SUM(liters) as liters'), DB::raw('SUM(points) as points'))
                                ->groupBy('date','product_id')
                                ->whereDate('created_at', $date)
                                ->where('shift', $shift)
                                ->get();

        // return $transaction;
        $total_liters = [];
        $total_points = [];
        foreach($transaction as $data) {
            array_push($total_liters, (Double) $data->liters);
            array_push($total_points, (Double) $data->points);
        }

        try {
            $connector = null;
            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);
            /* Header */
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("MYGAS PETROLEUM CORPORATION\n");
            $printer->text("Maa, Brgy. Magtuod, Davao City, 8000\n");
            $printer->text("Cashier" .$cashier."\n");
            $printer->text("Shift" .$shift. "\n");
            $printer -> selectPrintMode();
            $printer -> feed();

            /* Title of receipt */
            $printer -> setEmphasis(true);
            $printer->text("REPORT SUMMARY\n");
            $printer -> setEmphasis(false);
            $printer->text("DATE: ". $date . "\n");
            $printer -> feed();

            /* body */
            $printer -> setEmphasis(true);
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Total Liters: " . array_sum($total_liters) . "\n");
            $printer->text("Total Points: " . array_sum($total_points) . "\n");
            $printer -> feed();

            /* value */
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            foreach($transaction as $data) {
                $items = array(
                    new ItemWrapper("Product: ", $data->product->product_name),
                    new ItemWrapper("No. of Liters: ", $data->liters),
                );
            }
            $printer -> setEmphasis(true);
            foreach ($items as $item) {
                $printer -> text($item);
            }
            $printer -> setEmphasis(false);
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

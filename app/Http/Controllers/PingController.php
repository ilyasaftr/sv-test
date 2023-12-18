<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Process\Pipe;
use Illuminate\Support\Facades\Process;
use Throwable;

class PingController extends Controller
{
    public function ping()
    {
        // get query ip address
        $ip = request()->query('ip');
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return response()->json([
                'errors' => [
                    'ip' => [
                        'The ip field must be a valid IPv4 address.'
                    ]
                ]
            ], 400);
        }

        try {
            $result = Process::run("/sbin/ping -c 4 $ip");
            $output = $result->output();
            $output = explode("/", $output);
            $output = explode(" ", $output[3]);
            $averageMs = $output[2];

            return response()->json([
                'data' => [
                    'ip' => $ip,
                    'average_ms' => $averageMs
                ],
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], 400);
        }
    }
}

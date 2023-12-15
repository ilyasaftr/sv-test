<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Process\Pipe;
use Illuminate\Support\Facades\Process;

class PingController extends Controller
{
    public function ping()
    {
        // get query ip address
        $ip = request()->query('ip');
        $result = Process::run("/sbin/ping -c 4 $ip");
        $output = $result->output();
        $output = explode("/", $output);
        $output = explode(" ", $output[3]);
        $averageMs = $output[2];
        return response()->json([
            'ip' => $ip,
            'averageMs' => round($averageMs, 2)
        ]);
    }
}

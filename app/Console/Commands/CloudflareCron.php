<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudflare:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cloudflare IP Dynamic Cron';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cloudflare IP Dynamic Cron | '. date('Y-m-d H:i:s'));
        Log::info('Cloudflare IP Dynamic Cron | '. date('Y-m-d H:i:s'));

        $response = Http::get('https://api.ipify.org/?format=json');
        $publicIp = $response['ip'];
        $this->info('IP Public: ' . $publicIp);

        // update cloudflare dns record
        $cloudflareZoneId = Env('CLOUDFLARE_ZONE_ID');
        $cloudflareIdentifier = Env('CLOUDFLARE_IDENTIFIER');
        $cloudflareEmail = Env('CLOUDFLARE_EMAIL');
        $cloudflareKey = Env('CLOUDFLARE_KEY');
        $fullUrll = "https://api.cloudflare.com/client/v4/zones/$cloudflareZoneId/dns_records/$cloudflareIdentifier";

        $body = [
            'type' => 'A',
            'name' => 'api',
            'content' => $publicIp,
            'ttl' => 1,
            'proxied' => true
        ];

        $response = Http::withHeaders([
            'X-Auth-Email' => $cloudflareEmail,
            'X-Auth-Key' => $cloudflareKey,
            'Content-Type' => 'application/json'
        ])->put($fullUrll, $body);

        if ($response->successful()) {
            $this->info('DNS Updated');
        } else {
            $this->error('DNS Update Failed');
            Log::error($response->body());
        }
    }
}

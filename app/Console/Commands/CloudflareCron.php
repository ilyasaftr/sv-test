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
        $cloudflareDomain = Env('CLOUDFLARE_DOMAIN');
        $this->runningCheck($cloudflareDomain);
    }

    private function runningCheck($name) {
        $cloudflareZoneId = Env('CLOUDFLARE_ZONE_ID');
        $cloudflareEmail = Env('CLOUDFLARE_EMAIL');
        $cloudflareKey = Env('CLOUDFLARE_KEY');
        $fullUrl = "https://api.cloudflare.com/client/v4/zones/$cloudflareZoneId/dns_records";

        $response = Http::withHeaders([
            'X-Auth-Email' => $cloudflareEmail,
            'X-Auth-Key' => $cloudflareKey,
            'Content-Type' => 'application/json'
        ])->get($fullUrl);

        if ($response->successful()) {
            $data = $response->json();
            $result = collect($data['result'])->where('name', $name)->first();

            // if not exist create new
            if (!$result) {
                $result = $this->createDNSRecord($name, $this->getPublicIP());
            } else {
                $this->updateDNSRecord($result['id'], $name, $this->getPublicIP());
            }

            return $result['id'];
        } else {
            $this->error('DNS Update Failed');
            Log::error($response->body());
        }
    }

    private function createDNSRecord($name, $content)
    {
        $cloudflareZoneId = Env('CLOUDFLARE_ZONE_ID');
        $cloudflareEmail = Env('CLOUDFLARE_EMAIL');
        $cloudflareKey = Env('CLOUDFLARE_KEY');
        $fullUrl = "https://api.cloudflare.com/client/v4/zones/$cloudflareZoneId/dns_records";

        $body = [
            'type' => 'A',
            'name' => $name,
            'content' => $content,
            'ttl' => 1,
            'proxied' => true
        ];

        $response = Http::withHeaders([
            'X-Auth-Email' => $cloudflareEmail,
            'X-Auth-Key' => $cloudflareKey,
            'Content-Type' => 'application/json'
        ])->post($fullUrl, $body);

        if ($response->successful()) {
            $this->info('DNS Created');
            return $response->json()['result'];
        } else {
            $this->error('DNS Create Failed');
            Log::error($response->body());
        }
    }

    private function updateDNSRecord($id, $name, $content)
    {
        $cloudflareZoneId = Env('CLOUDFLARE_ZONE_ID');
        $cloudflareEmail = Env('CLOUDFLARE_EMAIL');
        $cloudflareKey = Env('CLOUDFLARE_KEY');
        $fullUrl = "https://api.cloudflare.com/client/v4/zones/$cloudflareZoneId/dns_records/$id";

        $body = [
            'type' => 'A',
            'name' => $name,
            'content' => $content,
            'ttl' => 1,
            'proxied' => true
        ];

        $response = Http::withHeaders([
            'X-Auth-Email' => $cloudflareEmail,
            'X-Auth-Key' => $cloudflareKey,
            'Content-Type' => 'application/json'
        ])->put($fullUrl, $body);

        if ($response->successful()) {
            $this->info('DNS Updated');
        } else {
            $this->error('DNS Update Failed');
            Log::error($response->body());
        }
    }

    private function getPublicIP() {
        $response = Http::get('https://api.ipify.org/?format=json');
        return $response['ip'];
    }
}

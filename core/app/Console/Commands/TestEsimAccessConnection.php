<?php

namespace App\Console\Commands;

use App\Services\EsimAccessService;
use Illuminate\Console\Command;

class TestEsimAccessConnection extends Command
{
    protected $signature = 'esimaccess:test';
    protected $description = 'Test eSIM Access API connection (balance query)';

    public function handle()
    {
        $this->info('Testing eSIM Access connection...');

        $service = app(EsimAccessService::class);
        $result = $service->getBalance();

        if (isset($result['error'])) {
            $this->error('Connection failed: ' . $result['error']);
            return 1;
        }

        if (!empty($result['success']) && isset($result['obj'])) {
            $obj = $result['obj'];
            $this->info('Connection OK.');
            if (isset($obj['balance'])) {
                $this->line('Balance: ' . ($obj['balance'] ?? 'N/A'));
            }
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return 0;
        }

        $this->warn('Unexpected response:');
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return 0;
    }
}

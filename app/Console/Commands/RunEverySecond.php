<?php

namespace App\Console\Commands;

use Cache;
use Illuminate\Console\Command;

class RunEverySecond extends Command
{
    protected $signature = 'run:every-second';
    protected $description = 'Runs the command every second with a loop';

    public function handle()
    {
        // Set a unique process ID in the cache
        $processId = uniqid();
        $cacheKey = 'command:run-every-second';
        $expirationSeconds = 100;

        Cache::put($cacheKey, $processId, $expirationSeconds); // Store ID for 10 seconds (auto cleanup)
        sleep(3); // wait for last loop to complete

        $this->info('Command started with ID: ' . $processId);

        while (true) {
            // Check if a new instance has started by comparing process ID
            if (Cache::get($cacheKey) !== $processId) {
                $this->info('New instance detected : '.Cache::get($cacheKey).'. Stopping old process...');
                break;
            }


            \Log::info('Command executed at: ' . now());

            sleep(1);
        }

        $this->info('Command stopped.');
    }
}


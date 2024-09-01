<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchRandomUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::acceptJson()->get('https://randomuser.me/api/');

        if ($response->successful()) {
            $results = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Successfully fetched random user data : ' . json_encode($results['results'], JSON_PRETTY_PRINT));
            // Save user data to the database (Undefiend in Task)
        } else {
            Log::error('Failed to fetch random user data.');
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\AvailableJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateJobHiringStatus extends Command
{
    protected $signature = 'jobs:update-hiring-status';
    protected $description = 'Update hiring status based on posted_at and expires_at dates';

    public function handle()
    {
        $now = now()->format('Y-m-d H:i:s');

        $this->info("Current time: {$now}");

        // Activate jobs that should be active
        $activated = AvailableJob::where('hiring_status', '!=', 'active')
            ->where('posted_at', '<=', $now)
            ->where('expires_at', '>', $now)
            ->update(['hiring_status' => 'active']);

        $this->info("Activated: {$activated} jobs");

        // Close expired jobs
        $closed = AvailableJob::where('hiring_status', '!=', 'closed')
            ->where('expires_at', '<=', $now)
            ->update(['hiring_status' => 'closed']);

        $this->info("Closed: {$closed} jobs");
        $this->info("Total updated: " . ($activated + $closed));

        return Command::SUCCESS;
    }
}

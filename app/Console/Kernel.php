<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\UpdateYoutubeViews;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{



        /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Other commands...
        UpdateYoutubeViews::class, // Add this line
    ];



    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('youtube:views:update')->everyMinute();
        $schedule->command('youtube:views:update')->everyFiveMinutes();
        // $schedule->command('youtube:views:update')->everyThirtyMinutes();
        // $schedule->command('youtube:views:update')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

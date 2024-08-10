<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            // Get sessions starting in the next 5 minutes
            $sessions = \App\Models\Session::where('time', '=', now()->addMinutes(5))->get();

            foreach ($sessions as $session) {
                // Notify the user and the student
                $session->user->notify(new \App\Notifications\SessionReminder($session));
                $session->student->notify(new \App\Notifications\SessionReminder($session));
            }
        })->everyMinute();    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

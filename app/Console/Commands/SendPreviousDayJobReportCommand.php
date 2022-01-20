<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PreviousDayJobReportNotification;
use Illuminate\Console\Command;

class SendPreviousDayJobReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:jobs-previous-day:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email previous day job reports';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emails = preg_split('/\r\n|[\r\n]/', nova_get_setting('job_report_emails'));

        User::whereIn('email', $emails)->get()->each(function($user){
            $user->notify(new PreviousDayJobReportNotification());
        });
    }
}

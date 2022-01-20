<?php

namespace App\Notifications;

use App\Exports\JobStageExport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class PreviousDayJobReportNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $previous_day = Carbon::now()->subDay()->format('Y-m-d');
        $report_path = "exports/job_exports_{$previous_day}.xlsx";

        Excel::store(new JobStageExport(), $report_path);

        return (new MailMessage)
            ->subject('Dagobah job report for ' . $previous_day)
            ->greeting('Hello ' . $notifiable->given_name)
            ->line('Please find attached the Dagobah job report for ' . $previous_day . '.')
            ->attach(storage_path('app/public/'.$report_path), [
                'as' => "job_exports_{$previous_day}.xlsx",
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

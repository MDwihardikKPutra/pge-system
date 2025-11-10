<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkPlanReminderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Rencana Kerja Belum Diisi',
            'message' => 'Anda belum mengisi rencana kerja hari ini. Silakan isi sebelum jam 10:00 pagi.',
            'icon' => '📋',
            'type' => 'work-plan-reminder',
            'url' => route('user.work-plans.index'),
            'priority' => 'high',
        ];
    }
}




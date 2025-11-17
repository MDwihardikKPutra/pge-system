<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkRealizationReminderNotification extends Notification
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
            'title' => 'Realisasi Kerja Belum Diisi',
            'message' => 'Anda belum mengisi realisasi kerja hari ini. Silakan isi sebelum jam 17:00 (5 sore).',
            'icon' => '✅',
            'type' => 'work-realization-reminder',
            'url' => route('user.work-realizations.index'),
            'priority' => 'high',
        ];
    }
}








<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSubmissionNotification extends Notification
{
    use Queueable;

    protected $submission;
    protected $type;
    protected $submitter;

    /**
     * Create a new notification instance.
     */
    public function __construct($submission, $type, $submitter)
    {
        $this->submission = $submission;
        $this->type = $type;
        $this->submitter = $submitter;
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
        $typeLabels = [
            'spd' => 'SPD',
            'purchase' => 'Pembelian',
            'vendor-payment' => 'Pembayaran Vendor',
            'leave' => 'Cuti/Izin',
        ];

        $typeLabel = $typeLabels[$this->type] ?? 'Pengajuan';
        
        // Get route based on type (admin approval routes)
        $routes = [
            'spd' => 'admin.approvals.spd.index',
            'purchase' => 'admin.approvals.purchases.index',
            'vendor-payment' => 'admin.approvals.vendor-payments.index',
            'leave' => 'admin.approvals.leaves',
        ];
        
        $route = $routes[$this->type] ?? 'admin.dashboard';
        
        // Get identifier
        $identifier = match($this->type) {
            'spd' => $this->submission->spd_number ?? 'SPD-' . $this->submission->id,
            'purchase' => $this->submission->purchase_number ?? 'PUR-' . $this->submission->id,
            'vendor-payment' => $this->submission->payment_number ?? 'VP-' . $this->submission->id,
            'leave' => $this->submission->leave_number ?? 'LEAVE-' . $this->submission->id,
            default => '#' . $this->submission->id,
        };
        
        return [
            'title' => "Pengajuan {$typeLabel} Baru",
            'message' => "Pengajuan {$typeLabel} {$identifier} dari {$this->submitter->name} memerlukan persetujuan",
            'icon' => '📄',
            'type' => $this->type,
            'submission_id' => $this->submission->id,
            'identifier' => $identifier,
            'submitter_name' => $this->submitter->name,
            'url' => route($route),
            'priority' => 'normal',
        ];
    }
}




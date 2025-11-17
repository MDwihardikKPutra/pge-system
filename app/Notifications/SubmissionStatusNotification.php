<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubmissionStatusNotification extends Notification
{
    use Queueable;

    protected $submission;
    protected $type;
    protected $status;
    protected $approver;

    /**
     * Create a new notification instance.
     */
    public function __construct($submission, $type, $status, $approver)
    {
        $this->submission = $submission;
        $this->type = $type;
        $this->status = $status;
        $this->approver = $approver;
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

        $statusText = $this->status === 'approved' ? 'disetujui' : 'ditolak';
        $icon = $this->status === 'approved' ? '✓' : '✗';
        $color = $this->status === 'approved' ? 'green' : 'red';
        
        $typeLabel = $typeLabels[$this->type] ?? 'Pengajuan';
        
        // Get route based on type
        $routes = [
            'spd' => 'user.spd.index',
            'purchase' => 'user.purchases.index',
            'vendor-payment' => 'user.vendor-payments.index',
            'leave' => 'user.leaves.index',
        ];
        
        $route = $routes[$this->type] ?? 'user.dashboard';
        
        // Get identifier
        $identifier = match($this->type) {
            'spd' => $this->submission->spd_number ?? 'SPD-' . $this->submission->id,
            'purchase' => $this->submission->purchase_number ?? 'PUR-' . $this->submission->id,
            'vendor-payment' => $this->submission->payment_number ?? 'VP-' . $this->submission->id,
            'leave' => $this->submission->leave_number ?? 'LEAVE-' . $this->submission->id,
            default => '#' . $this->submission->id,
        };
        
        return [
            'title' => "{$typeLabel} {$statusText}",
            'message' => "{$typeLabel} {$identifier} telah {$statusText} oleh {$this->approver->name}",
            'icon' => $icon,
            'color' => $color,
            'type' => $this->type,
            'status' => $this->status,
            'submission_id' => $this->submission->id,
            'identifier' => $identifier,
            'approver_name' => $this->approver->name,
            'url' => route($route),
            'priority' => 'normal',
        ];
    }
}








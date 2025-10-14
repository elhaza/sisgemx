<?php

namespace App\Notifications;

use App\Models\PaymentReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceiptSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public PaymentReceipt $paymentReceipt) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo Comprobante de Pago Recibido')
            ->greeting('Hola '.$notifiable->name)
            ->line('Se ha recibido un nuevo comprobante de pago.')
            ->line('Estudiante: '.$this->paymentReceipt->payment->student->user->name)
            ->line('Monto: $'.number_format($this->paymentReceipt->amount_paid, 2))
            ->line('Fecha de pago: '.$this->paymentReceipt->payment_date->format('d/m/Y'))
            ->action('Ver Comprobante', route('finance.payment-receipts.show', $this->paymentReceipt))
            ->line('Por favor, revisa y valida el comprobante.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_receipt_id' => $this->paymentReceipt->id,
            'student_name' => $this->paymentReceipt->payment->student->user->name,
            'amount' => $this->paymentReceipt->amount_paid,
            'payment_date' => $this->paymentReceipt->payment_date,
        ];
    }
}

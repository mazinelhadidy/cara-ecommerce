<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order #{$this->order->id} status updated")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your order is now {$this->order->status}.")
            ->line("Order total: {$this->order->total}")
            ->action('View order', url(route('orders.show', $this->order)))
            ->line('Thank you for shopping with us!');
    }
}

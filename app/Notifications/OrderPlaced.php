<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
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
            ->subject("Your order #{$this->order->id} has been placed")
            ->greeting("Hello {$notifiable->name},")
            ->line('Thank you for placing your order with us.')
            ->line("Order status: {$this->order->status}")
            ->line("Order total: {$this->order->total}")
            ->action('View order', url(route('orders.show', $this->order)))
            ->line('We will notify you when your order status changes.');
    }
}

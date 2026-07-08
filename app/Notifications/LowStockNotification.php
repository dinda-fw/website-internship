<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public Product $product)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Peringatan Stok Menipis - '.$this->product->name)
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Stok barang berikut sudah menipis dan perlu diperhatikan:')
            ->line('Nama Barang: '.$this->product->name)
            ->line('Kode Barang: '.$this->product->code)
            ->line('Sisa Stok: '.$this->product->stock)
            ->action('Lihat Detail Barang', route('products.show', $this->product))
            ->line('Segera lakukan pengadaan ulang jika diperlukan.');
    }
}

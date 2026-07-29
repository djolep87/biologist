<?php

namespace App\Notifications;

use App\Models\GradjevinskiDkoZahtev;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradjevinskiDkoZahtevOdbijen extends Notification
{
    public function __construct(public GradjevinskiDkoZahtev $zahtev) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("❌ DKO zahtev {$this->zahtev->broj_zahteva} je odbijen")
            ->greeting('Zahtev je odbijen')
            ->line("Vaš DKO zahtev **{$this->zahtev->broj_zahteva}** je odbijen.")
            ->line('Razlog: '.$this->zahtev->razlog_odbijanja)
            ->action('Pogledaj detalje', url('/dko-zahtevi/'.$this->zahtev->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'gradjevinski_dko_zahtev_odbijen',
            'zahtev_id' => $this->zahtev->id,
            'broj_zahteva' => $this->zahtev->broj_zahteva,
            'poruka' => "❌ Vaš DKO zahtev {$this->zahtev->broj_zahteva} je odbijen – {$this->zahtev->razlog_odbijanja}",
            'url' => '/dko-zahtevi/'.$this->zahtev->id,
        ];
    }
}

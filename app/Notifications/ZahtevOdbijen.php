<?php

namespace App\Notifications;

use App\Models\ZahtevPredaje;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ZahtevOdbijen extends Notification
{
    public function __construct(public ZahtevPredaje $zahtev) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Zahtev za predaju otpada je odbijen')
            ->greeting('Obaveštenje o vašem zahtevu')
            ->line("Zahtev za predaju otpada ({$this->zahtev->naziv_otpada}) je odbijen.")
            ->line("Razlog: {$this->zahtev->napomena_admina}")
            ->action('Pogledajte detalje', url('/dashboard?tab=zahtevi'))
            ->line('Kontaktirajte administratora za više informacija.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'zahtev_odbijen',
            'zahtev_id' => $this->zahtev->id,
            'poruka' => "Zahtev odbijen: {$this->zahtev->napomena_admina}",
            'url' => '/dashboard?tab=zahtevi',
        ];
    }
}

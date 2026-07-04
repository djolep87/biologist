<?php

namespace App\Notifications;

use App\Models\ZahtevPredaje;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoviZahtevPredaje extends Notification
{
    public function __construct(public ZahtevPredaje $zahtev) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->zahtev->loadMissing(['team', 'evidencije']);

        return (new MailMessage)
            ->subject("Novi zahtev za predaju otpada — {$this->zahtev->team->name}")
            ->greeting('Novi zahtev!')
            ->line("Firma: **{$this->zahtev->team->name}**")
            ->line("Otpad: {$this->zahtev->naziv_otpada} ({$this->zahtev->indeksni_broj})")
            ->line("Ukupna masa: {$this->zahtev->masa_ukupno} t")
            ->line("Broj izveštaja: {$this->zahtev->evidencije->count()}")
            ->when($this->zahtev->napomena_klijenta, fn (MailMessage $m) => $m->line("Napomena: {$this->zahtev->napomena_klijenta}"))
            ->action('Otvori zahtev', url('/admin/zahtevi/' . $this->zahtev->id))
            ->line('Prijavite se u admin panel da obradite zahtev.');
    }

    public function toArray(object $notifiable): array
    {
        $this->zahtev->loadMissing('team');

        return [
            'type' => 'novi_zahtev',
            'zahtev_id' => $this->zahtev->id,
            'firma' => $this->zahtev->team->name,
            'naziv_otpada' => $this->zahtev->naziv_otpada,
            'indeksni_broj' => $this->zahtev->indeksni_broj,
            'masa_ukupno' => $this->zahtev->masa_ukupno,
            'url' => '/admin/zahtevi/' . $this->zahtev->id,
        ];
    }
}

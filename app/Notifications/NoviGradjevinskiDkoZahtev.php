<?php

namespace App\Notifications;

use App\Models\GradjevinskiDkoZahtev;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoviGradjevinskiDkoZahtev extends Notification
{
    public function __construct(public GradjevinskiDkoZahtev $zahtev) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->zahtev->loadMissing(['team', 'constructionSite']);

        return (new MailMessage)
            ->subject("Novi DKO zahtev (građevinski) — {$this->zahtev->team->name}")
            ->greeting('Novi građevinski DKO zahtev!')
            ->line("Firma: **{$this->zahtev->team->name}**")
            ->line("Gradilište: {$this->zahtev->constructionSite?->naziv_gradilista}")
            ->line("Zahtev: {$this->zahtev->broj_zahteva}")
            ->line('Ukupna masa: '.number_format((float) $this->zahtev->masa_ukupno, 3, ',', '.').' t')
            ->when($this->zahtev->napomena_klijenta, fn (MailMessage $m) => $m->line("Napomena: {$this->zahtev->napomena_klijenta}"))
            ->action('Otvori zahtev', url('/admin/dko-zahtevi/'.$this->zahtev->id));
    }

    public function toArray(object $notifiable): array
    {
        $this->zahtev->loadMissing(['team', 'constructionSite']);

        return [
            'type' => 'novi_gradjevinski_dko_zahtev',
            'zahtev_id' => $this->zahtev->id,
            'broj_zahteva' => $this->zahtev->broj_zahteva,
            'firma' => $this->zahtev->team->name,
            'gradiliste' => $this->zahtev->constructionSite?->naziv_gradilista,
            'masa_ukupno' => $this->zahtev->masa_ukupno,
            'poruka' => "📋 Novi DKO zahtev – {$this->zahtev->team->name} – ".number_format((float) $this->zahtev->masa_ukupno, 3, ',', '.')." t – {$this->zahtev->constructionSite?->naziv_gradilista}",
            'url' => '/admin/dko-zahtevi/'.$this->zahtev->id,
        ];
    }
}

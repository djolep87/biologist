<?php

namespace App\Notifications;

use App\Models\DokumentKretanja;
use App\Models\GradjevinskiDkoZahtev;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradjevinskiDkoZahtevObradjen extends Notification
{
    public function __construct(
        public GradjevinskiDkoZahtev $zahtev,
        public DokumentKretanja $dokument
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("✅ DKO dokument je spreman — {$this->zahtev->broj_zahteva}")
            ->greeting('DKO dokument je spreman!')
            ->line("Zahtev **{$this->zahtev->broj_zahteva}** je obrađen.")
            ->line("Broj izveštaja: {$this->dokument->broj_izvestaja}")
            ->when($this->zahtev->napomena_admina, fn (MailMessage $m) => $m->line("Napomena: {$this->zahtev->napomena_admina}"))
            ->action('Preuzmi DKO', url('/dko-zahtevi/'.$this->zahtev->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'gradjevinski_dko_zahtev_obradjen',
            'zahtev_id' => $this->zahtev->id,
            'broj_zahteva' => $this->zahtev->broj_zahteva,
            'dokument_id' => $this->dokument->id,
            'broj_dokumenta' => $this->dokument->broj_izvestaja ?: $this->dokument->broj_dokumenta,
            'poruka' => "✅ DKO dokument je spreman za preuzimanje – {$this->zahtev->broj_zahteva}",
            'url' => '/dko-zahtevi/'.$this->zahtev->id,
        ];
    }
}

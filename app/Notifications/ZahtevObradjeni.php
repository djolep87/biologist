<?php

namespace App\Notifications;

use App\Models\DokumentKretanja;
use App\Models\ZahtevPredaje;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ZahtevObradjeni extends Notification
{
    public function __construct(
        public ZahtevPredaje $zahtev,
        public DokumentKretanja $dokument
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Vaš zahtev je obrađen — {$this->dokument->broj_dokumenta}")
            ->greeting('Vaš zahtev je obrađen!')
            ->line("DOKO dokument **{$this->dokument->broj_dokumenta}** je kreiran.")
            ->line("Otpad: {$this->zahtev->naziv_otpada} — {$this->zahtev->masa_ukupno} t")
            ->when($this->zahtev->napomena_admina, fn (MailMessage $m) => $m->line("Napomena administratora: {$this->zahtev->napomena_admina}"))
            ->action('Preuzmi dokument', url('/dashboard?tab=zahtevi'))
            ->line('Prijavite se u aplikaciju da preuzmete DOKO obrazac.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'zahtev_obradjen',
            'zahtev_id' => $this->zahtev->id,
            'dokument_id' => $this->dokument->id,
            'broj_dokumenta' => $this->dokument->broj_dokumenta,
            'poruka' => 'Vaš zahtev je obrađen. DOKO dokument je spreman.',
            'url' => '/dashboard?tab=zahtevi',
        ];
    }
}

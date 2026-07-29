<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class NotificationBell extends Component
{
    public string $variant = 'client';

    public function markRead(string $id): void
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function getUnreadCountProperty(): int
    {
        return auth()->user()->unreadNotifications->count();
    }

    public function getNotificationsProperty(): Collection
    {
        return auth()->user()->unreadNotifications->take(10);
    }

    public function notificationTitle(array $data): string
    {
        return match ($data['type'] ?? '') {
            'novi_zahtev' => '📨 Novi zahtev — ' . ($data['firma'] ?? ''),
            'zahtev_obradjen' => '✅ Zahtev obrađen — ' . ($data['broj_dokumenta'] ?? ''),
            'zahtev_odbijen' => '❌ Zahtev odbijen',
            'novi_gradjevinski_dko_zahtev' => '📋 Novi DKO zahtev — ' . ($data['firma'] ?? ''),
            'gradjevinski_dko_zahtev_obradjen' => '✅ DKO spreman — ' . ($data['broj_zahteva'] ?? ''),
            'gradjevinski_dko_zahtev_odbijen' => '❌ DKO zahtev odbijen — ' . ($data['broj_zahteva'] ?? ''),
            default => $data['poruka'] ?? 'Obaveštenje',
        };
    }

    public function notificationSubtitle(array $data): string
    {
        return match ($data['type'] ?? '') {
            'novi_zahtev' => ($data['naziv_otpada'] ?? '') . ' | ' . ($data['masa_ukupno'] ?? '') . ' t',
            'zahtev_obradjen' => $data['poruka'] ?? '',
            'zahtev_odbijen' => $data['poruka'] ?? '',
            'novi_gradjevinski_dko_zahtev' => ($data['gradiliste'] ?? '') . ' | ' . ($data['masa_ukupno'] ?? '') . ' t',
            'gradjevinski_dko_zahtev_obradjen' => $data['poruka'] ?? '',
            'gradjevinski_dko_zahtev_odbijen' => $data['poruka'] ?? '',
            default => '',
        };
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'unreadCount' => $this->unreadCount,
            'notifications' => $this->notifications,
        ]);
    }
}

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
            default => $data['poruka'] ?? 'Obaveštenje',
        };
    }

    public function notificationSubtitle(array $data): string
    {
        return match ($data['type'] ?? '') {
            'novi_zahtev' => ($data['naziv_otpada'] ?? '') . ' | ' . ($data['masa_ukupno'] ?? '') . ' t',
            'zahtev_obradjen' => $data['poruka'] ?? '',
            'zahtev_odbijen' => $data['poruka'] ?? '',
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

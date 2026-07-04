<div x-data="{ open: false }" class="relative">
    <button type="button" @click="open = !open"
        class="relative p-2 {{ $variant === 'admin' ? 'text-gray-400 hover:text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[1.25rem] h-5 px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.outside="open = false" x-cloak
        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50 max-h-96 overflow-y-auto">
        @forelse ($notifications as $notif)
            @php $data = $notif->data; @endphp
            <a href="{{ url($data['url'] ?? '#') }}"
                wire:click="markRead('{{ $notif->id }}')"
                @click="open = false"
                class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0">
                <div class="text-sm font-medium text-gray-900">
                    {{ $this->notificationTitle($data) }}
                </div>
                @if ($this->notificationSubtitle($data))
                    <div class="text-xs text-gray-500 mt-1">{{ $this->notificationSubtitle($data) }}</div>
                @endif
                <div class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
            </a>
        @empty
            <div class="px-4 py-6 text-center text-sm text-gray-400">
                Nema novih notifikacija
            </div>
        @endforelse

        @if ($unreadCount > 0)
            <div class="px-4 py-2 border-t border-gray-100">
                <button type="button" wire:click="markAllRead"
                    class="text-xs {{ $variant === 'admin' ? 'text-indigo-600' : 'text-green-600' }} hover:underline">
                    Označi sve kao pročitano
                </button>
            </div>
        @endif
    </div>
</div>

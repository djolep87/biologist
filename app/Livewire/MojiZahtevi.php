<?php

namespace App\Livewire;

use App\Models\ZahtevPredaje;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MojiZahtevi extends Component
{
    use WithPagination;

    public ?int $confirmingDeleteId = null;

    #[On('zahtevPoslat')]
    public function refreshList(): void
    {
        $this->resetPage();
        $this->confirmingDeleteId = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function obrisiOdbijenZahtev(int $id): void
    {
        $zahtev = ZahtevPredaje::forTeam()
            ->where('status', 'odbijeno')
            ->findOrFail($id);

        $zahtev->obrisiOdbijen();

        $this->confirmingDeleteId = null;
        $this->dispatch('evidencijaUpdated');
        $this->dispatch('notify', message: 'Odbijeni zahtev je uklonjen. Otpad je ponovo dostupan za novi zahtev.', type: 'success');
    }

    public function getZahteviProperty(): LengthAwarePaginator
    {
        return ZahtevPredaje::forTeam()
            ->with(['evidencije', 'dokumentKretanja'])
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.moji-zahtevi', [
            'zahtevi' => $this->zahtevi,
        ]);
    }
}

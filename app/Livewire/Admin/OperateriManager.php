<?php

namespace App\Livewire\Admin;

use App\Models\Operater;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class OperateriManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterTip = '';

    public string $filterStatus = '';

    public bool $showModal = false;

    public ?int $operaterId = null;

    public string $naziv = '';

    public string $kratki_naziv = '';

    public string $pib = '';

    public string $maticni_broj = '';

    /** @var array<int, string> */
    public array $tip = ['sakupljac'];

    public string $opstina = '';

    public string $mesto = '';

    public string $postanski_broj = '';

    public string $ulica = '';

    public string $telefon = '';

    public string $faks = '';

    public string $email = '';

    public string $kontakt_osoba = '';

    public string $dozvola_broj = '';

    public string $dozvola_datum_izdavanja = '';

    public string $dozvola_vazi_do = '';

    /** @var array<int, string> */
    public array $prihvata_indeksne_brojeve = [];

    public string $indeksInput = '';

    public string $r_oznaka = '';

    public string $d_oznaka = '';

    public bool $aktivan = true;

    public string $napomena = '';

    public ?int $confirmingDeleteId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $operater = Operater::findOrFail($id);
        $this->operaterId = $operater->id;
        $this->naziv = $operater->naziv;
        $this->kratki_naziv = $operater->kratki_naziv ?? '';
        $this->pib = $operater->pib;
        $this->maticni_broj = $operater->maticni_broj;
        $this->tip = $operater->tip ?? ['sakupljac'];
        $this->opstina = $operater->opstina ?? '';
        $this->mesto = $operater->mesto ?? '';
        $this->postanski_broj = $operater->postanski_broj ?? '';
        $this->ulica = $operater->ulica ?? '';
        $this->telefon = $operater->telefon ?? '';
        $this->faks = $operater->faks ?? '';
        $this->email = $operater->email ?? '';
        $this->kontakt_osoba = $operater->kontakt_osoba ?? '';
        $this->dozvola_broj = $operater->dozvola_broj ?? '';
        $this->dozvola_datum_izdavanja = $operater->dozvola_datum_izdavanja?->format('Y-m-d') ?? '';
        $this->dozvola_vazi_do = $operater->dozvola_vazi_do?->format('Y-m-d') ?? '';
        $this->prihvata_indeksne_brojeve = $operater->prihvata_indeksne_brojeve ?? [];
        $this->r_oznaka = $operater->r_oznaka ?? '';
        $this->d_oznaka = $operater->d_oznaka ?? '';
        $this->aktivan = $operater->aktivan;
        $this->napomena = $operater->napomena ?? '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function addIndeksTag(): void
    {
        $value = trim($this->indeksInput);
        if ($value === '') {
            return;
        }
        if (! in_array($value, $this->prihvata_indeksne_brojeve, true)) {
            $this->prihvata_indeksne_brojeve[] = $value;
        }
        $this->indeksInput = '';
    }

    public function removeIndeksTag(int $index): void
    {
        unset($this->prihvata_indeksne_brojeve[$index]);
        $this->prihvata_indeksne_brojeve = array_values($this->prihvata_indeksne_brojeve);
    }

    public function save(): void
    {
        $this->validate($this->rules(), $this->messages());

        $data = [
            'naziv' => $this->naziv,
            'kratki_naziv' => $this->kratki_naziv,
            'pib' => $this->pib,
            'maticni_broj' => $this->maticni_broj,
            'tip' => $this->tip,
            'opstina' => $this->opstina ?: null,
            'mesto' => $this->mesto ?: null,
            'postanski_broj' => $this->postanski_broj ?: null,
            'ulica' => $this->ulica ?: null,
            'telefon' => $this->telefon ?: null,
            'faks' => $this->faks ?: null,
            'email' => $this->email ?: null,
            'kontakt_osoba' => $this->kontakt_osoba ?: null,
            'dozvola_broj' => $this->dozvola_broj ?: null,
            'dozvola_datum_izdavanja' => $this->dozvola_datum_izdavanja ?: null,
            'dozvola_vazi_do' => $this->dozvola_vazi_do ?: null,
            'prihvata_indeksne_brojeve' => $this->prihvata_indeksne_brojeve ?: null,
            'r_oznaka' => $this->r_oznaka ?: null,
            'd_oznaka' => $this->d_oznaka ?: null,
            'aktivan' => $this->aktivan,
            'napomena' => $this->napomena ?: null,
        ];

        if ($this->operaterId) {
            Operater::findOrFail($this->operaterId)->update($data);
            session()->flash('success', 'Operater je ažuriran.');
        } else {
            Operater::create(array_merge($data, ['created_by' => auth()->id()]));
            session()->flash('success', 'Operater je kreiran.');
        }

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id): void
    {
        Operater::findOrFail($id)->delete();
        $this->confirmingDeleteId = null;
        session()->flash('success', 'Operater je obrisan.');
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterTip = '';
        $this->filterStatus = '';
        $this->resetPage();
    }

    public function render()
    {
        $operateri = Operater::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('naziv', 'like', '%'.$this->search.'%')
                    ->orWhere('kratki_naziv', 'like', '%'.$this->search.'%')
                    ->orWhere('pib', 'like', '%'.$this->search.'%');
            }))
            ->when($this->filterTip !== '', fn ($q) => $q->whereJsonContains('tip', $this->filterTip))
            ->when($this->filterStatus === 'aktivan', fn ($q) => $q->where('aktivan', true))
            ->when($this->filterStatus === 'neaktivan', fn ($q) => $q->where('aktivan', false))
            ->orderBy('naziv')
            ->paginate(15);

        return view('livewire.admin.operateri-manager', [
            'operateri' => $operateri,
            'tipOptions' => Operater::tipLabels(),
            'rOznake' => $this->oznake('R', 13),
            'dOznake' => $this->oznake('D', 15),
        ]);
    }

    /** @return array<string, mixed> */
    private function rules(): array
    {
        return [
            'naziv' => ['required', 'string', 'max:255'],
            'kratki_naziv' => ['required', 'string', 'max:30'],
            'pib' => ['required', 'digits:9', Rule::unique('operateri', 'pib')->ignore($this->operaterId)],
            'maticni_broj' => ['required', 'digits:8', Rule::unique('operateri', 'maticni_broj')->ignore($this->operaterId)],
            'tip' => ['required', 'array', 'min:1'],
            'email' => ['nullable', 'email'],
            'dozvola_vazi_do' => ['nullable', 'date'],
        ];
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        return [
            'naziv.required' => 'Naziv firme je obavezan.',
            'kratki_naziv.required' => 'Kratki naziv je obavezan.',
            'pib.required' => 'PIB je obavezan.',
            'pib.digits' => 'PIB mora imati tačno 9 cifara.',
            'pib.unique' => 'Operater sa ovim PIB-om već postoji.',
            'maticni_broj.required' => 'Matični broj je obavezan.',
            'maticni_broj.digits' => 'Matični broj mora imati tačno 8 cifara.',
            'maticni_broj.unique' => 'Operater sa ovim matičnim brojem već postoji.',
            'tip.required' => 'Izaberite bar jedan tip operatera.',
        ];
    }

    private function resetForm(): void
    {
        $this->reset([
            'operaterId', 'naziv', 'kratki_naziv', 'pib', 'maticni_broj', 'tip',
            'opstina', 'mesto', 'postanski_broj', 'ulica', 'telefon', 'faks', 'email',
            'kontakt_osoba', 'dozvola_broj', 'dozvola_datum_izdavanja', 'dozvola_vazi_do',
            'prihvata_indeksne_brojeve', 'indeksInput', 'r_oznaka', 'd_oznaka', 'napomena',
        ]);
        $this->tip = ['sakupljac'];
        $this->aktivan = true;
    }

    /** @return array<int, string> */
    private function oznake(string $prefix, int $count): array
    {
        return collect(range(1, $count))->map(fn (int $n) => $prefix.$n)->all();
    }
}

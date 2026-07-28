<?php

namespace App\Livewire\Admin;

use App\Models\Team;
use Illuminate\Validation\Rule;
use Livewire\Component;

class DkoNumeracijaSettings extends Component
{
    public Team $team;

    public string $format = 'osnovni';

    public int $brojCifara = 3;

    public string $prefix = '';

    /** @var array<int, array{oznaka: string, naziv: string}> */
    public array $lokacije = [];

    public function mount(Team $team): void
    {
        $this->team = $team;

        $settings = $team->dkoNumeracija();
        $this->format = $settings['format'];
        $this->brojCifara = in_array($settings['broj_cifara'], [3, 4], true) ? $settings['broj_cifara'] : 3;
        $this->prefix = $settings['prefix'];
        $this->lokacije = $settings['lokacije'] ?: [];
    }

    public function addLokacija(): void
    {
        $this->lokacije[] = ['oznaka' => '', 'naziv' => ''];
    }

    public function removeLokacija(int $index): void
    {
        unset($this->lokacije[$index]);
        $this->lokacije = array_values($this->lokacije);
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->is_super_admin, 403);

        $validated = $this->validate([
            'format' => ['required', Rule::in(['osnovni', 'lokacija', 'vremenski'])],
            'brojCifara' => ['required', 'integer', Rule::in([3, 4])],
            'prefix' => ['nullable', 'string', 'max:20'],
            'lokacije' => ['array'],
            'lokacije.*.oznaka' => ['nullable', 'string', 'max:10'],
            'lokacije.*.naziv' => ['nullable', 'string', 'max:100'],
        ], [], [
            'brojCifara' => 'broj cifara',
            'lokacije.*.oznaka' => 'oznaka lokacije',
        ]);

        $lokacije = collect($validated['lokacije'] ?? [])
            ->map(fn ($l) => [
                'oznaka' => trim((string) ($l['oznaka'] ?? '')),
                'naziv' => trim((string) ($l['naziv'] ?? '')),
            ])
            ->filter(fn ($l) => $l['oznaka'] !== '')
            ->values()
            ->all();

        if ($validated['format'] === 'lokacija' && $lokacije === []) {
            $this->addError('lokacije', 'Za format sa oznakom lokacije morate definisati bar jednu lokaciju.');

            return;
        }

        $this->team->forceFill([
            'dko_format_broja' => $validated['format'],
            'dko_broj_cifara' => $validated['brojCifara'],
            'dko_prefix' => trim((string) ($validated['prefix'] ?? '')) ?: null,
            'dko_lokacije' => $lokacije ?: null,
        ])->save();

        $this->lokacije = $lokacije;

        $this->dispatch('notify', message: 'Podešavanja numeracije DKO su sačuvana.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.dko-numeracija-settings');
    }
}

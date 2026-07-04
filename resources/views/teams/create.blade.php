<x-layouts.dashboard title="Dodaj firmu">
  @if (auth()->user()->allTeams()->isEmpty())
    <div class="mb-6 bg-green-50 border border-green-200 rounded-2xl px-6 py-4 flex items-start gap-3 max-w-4xl">
      <span class="text-2xl">🎉</span>
      <div>
        <p class="font-semibold text-green-800">Nalog je kreiran!</p>
        <p class="text-green-700 text-sm mt-1">Sada dodajte vašu firmu da biste počeli.</p>
      </div>
    </div>
  @endif

  <div class="max-w-4xl">
    @livewire('teams.create-team-form')
  </div>
</x-layouts.dashboard>

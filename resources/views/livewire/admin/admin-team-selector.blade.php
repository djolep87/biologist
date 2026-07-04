<div class="rounded-lg bg-indigo-500/15 border border-indigo-400/20 px-3 py-2.5">
    <p class="text-xs text-indigo-300/80 uppercase tracking-wide font-medium mb-1.5">Aktivna firma</p>
    <select wire:model.live="selectedTeamId"
        class="w-full rounded-lg border-indigo-400/30 bg-[#2a3140] text-sm text-white focus:border-indigo-400 focus:ring-indigo-400">
        <option value="">— Izaberite firmu —</option>
        @foreach ($teams as $team)
            <option value="{{ $team->id }}">{{ $team->name }}@if($team->pib) ({{ $team->pib }})@endif</option>
        @endforeach
    </select>
    @if ($selectedTeam)
        <p class="mt-2 text-xs text-indigo-300/70">{{ $selectedTeam->tip_subjekta ?? 'DEO1' }}</p>
    @endif
</div>

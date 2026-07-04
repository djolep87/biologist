<?php

namespace App\Services;

use App\Models\DnevnaEvidencija;
use App\Models\DokumentKretanja;
use App\Models\Team;
use App\Models\User;
use App\Models\WastePlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminDashboardStatsService
{
    /**
     * Kompletan skup metrika za dashboard u jednom pozivu.
     *
     * @param  array{key: string, start: Carbon, end: Carbon, year: int, label: string}  $period
     * @return array<string, mixed>
     */
    public function build(array $period): array
    {
        $alerts = $this->alerts();
        $systemStats = $this->systemStats($alerts);

        return [
            'period' => [
                'key' => $period['key'],
                'label' => $period['label'],
                'start' => $period['start']->toDateString(),
                'end' => $period['end']->toDateString(),
                'year' => $period['year'],
            ],
            'systemStats' => $systemStats,
            'wasteStats' => $this->wasteStats($period, $systemStats['activeClients']),
            'monthlyChart' => $this->monthlyChart($period['year']),
            'topClients' => $this->topClients($period),
            'recentActivity' => $this->recentActivity(),
            'alerts' => $alerts,
            'recentDeliveries' => $this->recentDeliveries(),
            'footer' => $this->footerStats(),
            'generatedAt' => now()->toIso8601String(),
        ];
    }

    /**
     * Razrešava period iz ulaznih parametara (this_year / last_year / this_month / custom).
     *
     * @param  array<string, mixed>  $input
     * @return array{key: string, start: Carbon, end: Carbon, year: int, label: string}
     */
    public function resolvePeriod(array $input): array
    {
        $key = $input['period'] ?? 'this_year';
        $now = Carbon::now();

        return match ($key) {
            'last_year' => [
                'key' => 'last_year',
                'start' => $now->copy()->subYear()->startOfYear(),
                'end' => $now->copy()->subYear()->endOfYear(),
                'year' => $now->year - 1,
                'label' => 'Prošla godina ('.($now->year - 1).')',
            ],
            'this_month' => [
                'key' => 'this_month',
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'year' => $now->year,
                'label' => 'Ovaj mesec',
            ],
            'custom' => $this->resolveCustom($input, $now),
            default => [
                'key' => 'this_year',
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
                'year' => $now->year,
                'label' => 'Ova godina ('.$now->year.')',
            ],
        };
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{key: string, start: Carbon, end: Carbon, year: int, label: string}
     */
    private function resolveCustom(array $input, Carbon $now): array
    {
        try {
            $start = ! empty($input['start']) ? Carbon::parse($input['start'])->startOfDay() : $now->copy()->startOfYear();
            $end = ! empty($input['end']) ? Carbon::parse($input['end'])->endOfDay() : $now->copy()->endOfDay();
        } catch (\Throwable) {
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfDay();
        }

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [
            'key' => 'custom',
            'start' => $start,
            'end' => $end,
            'year' => $start->year,
            'label' => $start->format('d.m.Y.').' – '.$end->format('d.m.Y.'),
        ];
    }

    // === SEKCIJA 1: sistemske metrike ===

    /**
     * @param  array<int, array<string, mixed>>  $alerts
     * @return array<string, mixed>
     */
    private function systemStats(array $alerts): array
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();

        $totalClients = Team::count();
        $newClientsThisMonth = Team::where('created_at', '>=', $monthStart)->count();

        $lastActivity = $this->lastActivityByTeam();
        $activeClients = 0;
        $inactiveClients = 0;
        $inactiveDays = [];

        foreach (Team::pluck('id') as $teamId) {
            $last = $lastActivity->get($teamId);

            if ($last && Carbon::parse($last)->gte($now->copy()->subDays(30))) {
                $activeClients++;
            }

            if (! $last || Carbon::parse($last)->lt($now->copy()->subDays(60))) {
                $inactiveClients++;
                if ($last) {
                    $inactiveDays[] = Carbon::parse($last)->diffInDays($now);
                }
            }
        }

        $totalUsers = User::count();
        $newUsersThisMonth = User::where('created_at', '>=', $monthStart)->count();

        $totalPlans = WastePlan::count();
        $newPlansThisMonth = WastePlan::where('generated_at', '>=', $monthStart)->count();

        $actionableAlerts = collect($alerts)->whereIn('severity', ['critical', 'warning'])->count();

        return [
            'totalClients' => $totalClients,
            'newClientsThisMonth' => $newClientsThisMonth,
            'activeClients' => $activeClients,
            'activePercentage' => $totalClients > 0 ? round($activeClients / $totalClients * 100) : 0,
            'inactiveClients' => $inactiveClients,
            'avgInactiveDays' => $inactiveDays !== [] ? (int) round(array_sum($inactiveDays) / count($inactiveDays)) : 0,
            'totalUsers' => $totalUsers,
            'newUsersThisMonth' => $newUsersThisMonth,
            'totalPlans' => $totalPlans,
            'newPlansThisMonth' => $newPlansThisMonth,
            'alertsCount' => $actionableAlerts,
        ];
    }

    // === SEKCIJA 2: otpad metrike ===

    /**
     * @param  array{start: Carbon, end: Carbon}  $period
     * @return array<string, mixed>
     */
    private function wasteStats(array $period, int $activeClients): array
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $prevMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $prevMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $totalProduced = $this->sumProduced($period['start'], $period['end']);
        $prevYearProduced = $this->sumProduced(
            $period['start']->copy()->subYear(),
            $period['end']->copy()->subYear()
        );

        $totalDelivered = $this->sumDelivered($period['start'], $period['end']);
        $deliveredThisMonth = $this->sumDelivered($monthStart, $monthEnd);
        $deliveredPrevMonth = $this->sumDelivered($prevMonthStart, $prevMonthEnd);

        $totalEntries = $this->countEntries($period['start'], $period['end']);
        $entriesThisMonth = $this->countEntries($monthStart, $monthEnd);
        $entriesPrevMonth = $this->countEntries($prevMonthStart, $prevMonthEnd);

        $totalInStorage = array_sum($this->storageByTeam($period['end']));

        $averagePerClient = $activeClients > 0 ? round($totalProduced / $activeClients, 1) : 0.0;

        return [
            'totalProduced' => round($totalProduced, 2),
            'producedTrend' => $this->pct($totalProduced, $prevYearProduced),
            'totalDelivered' => round($totalDelivered, 2),
            'deliveredTrend' => $this->pct($deliveredThisMonth, $deliveredPrevMonth),
            'totalInStorage' => round($totalInStorage, 2),
            'totalEntries' => $totalEntries,
            'entriesThisMonth' => $entriesThisMonth,
            'entriesTrend' => $this->pct($entriesThisMonth, $entriesPrevMonth),
            'averagePerClient' => $averagePerClient,
        ];
    }

    // === SEKCIJA 4: grafikon po mesecima ===

    /**
     * @return array<int, array{month: string, monthNum: int, produced: float, delivered: float, inStorage: float}>
     */
    private function monthlyChart(int $year): array
    {
        $yearEnd = Carbon::create($year, 12, 31)->endOfDay();

        $sums = DnevnaEvidencija::query()
            ->whereYear('datum', $year)
            ->selectRaw('MONTH(datum) as m')
            ->selectRaw('SUM(proizvedena_kolicina) as produced')
            ->selectRaw('SUM(CASE WHEN predat_operateru = 1 THEN predata_kolicina ELSE 0 END) as delivered')
            ->groupBy('m')
            ->get()
            ->keyBy('m');

        $monthlyStorage = $this->monthlyStorageSnapshots($year, $yearEnd);

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Avg', 'Sep', 'Okt', 'Nov', 'Dec'];
        $result = [];

        for ($m = 1; $m <= 12; $m++) {
            $row = $sums->get($m);
            $result[] = [
                'month' => $labels[$m - 1],
                'monthNum' => $m,
                'produced' => round((float) ($row->produced ?? 0), 2),
                'delivered' => round((float) ($row->delivered ?? 0), 2),
                'inStorage' => round($monthlyStorage[$m] ?? 0.0, 2),
            ];
        }

        return $result;
    }

    /**
     * Snapshot stanja skladišta (svih firmi) na kraju svakog meseca u godini.
     *
     * @return array<int, float>
     */
    private function monthlyStorageSnapshots(int $year, Carbon $yearEnd): array
    {
        $records = DnevnaEvidencija::query()
            ->whereNotNull('datum')
            ->where('datum', '<=', $yearEnd->toDateString())
            ->orderBy('datum')
            ->orderBy('id')
            ->get(['team_id', 'indeksni_broj', 'datum', 'stanje_na_skladistu']);

        $monthEnds = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthEnds[$m] = Carbon::create($year, $m, 1)->endOfMonth();
        }

        $map = [];
        $snapshots = array_fill(1, 12, 0.0);
        $mIdx = 1;

        foreach ($records as $r) {
            $d = Carbon::parse($r->datum);

            while ($mIdx <= 12 && $d->gt($monthEnds[$mIdx])) {
                $snapshots[$mIdx] = array_sum($map);
                $mIdx++;
            }

            $map[$r->team_id.'|'.$r->indeksni_broj] = (float) $r->stanje_na_skladistu;
        }

        while ($mIdx <= 12) {
            $snapshots[$mIdx] = array_sum($map);
            $mIdx++;
        }

        return $snapshots;
    }

    // === SEKCIJA 4b: top klijenti ===

    /**
     * @param  array{start: Carbon, end: Carbon}  $period
     * @return array<int, array{name: string, amount: float, percentage: float}>
     */
    private function topClients(array $period): array
    {
        $rows = DnevnaEvidencija::query()
            ->whereBetween('datum', [$period['start']->toDateString(), $period['end']->toDateString()])
            ->selectRaw('team_id, SUM(proizvedena_kolicina) as produced')
            ->groupBy('team_id')
            ->orderByDesc('produced')
            ->get();

        $total = (float) $rows->sum('produced');
        $teams = Team::whereIn('id', $rows->pluck('team_id'))->pluck('name', 'id');

        return $rows->take(5)->map(fn ($row) => [
            'name' => $teams->get($row->team_id, 'Firma #'.$row->team_id),
            'amount' => round((float) $row->produced, 2),
            'percentage' => $total > 0 ? round((float) $row->produced / $total * 100, 1) : 0.0,
        ])->values()->all();
    }

    // === SEKCIJA 5: poslednja aktivnost ===

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recentActivity(): array
    {
        return DnevnaEvidencija::query()
            ->with(['team:id,name', 'user:id,name'])
            ->orderByDesc('created_at')
            ->take(15)
            ->get()
            ->map(function (DnevnaEvidencija $e) {
                $predato = $e->predat_operateru && $e->dokument_kretanja_id;

                return [
                    'id' => $e->id,
                    'time' => $e->created_at?->toIso8601String(),
                    'client' => $e->team?->name ?? '—',
                    'user' => $e->user?->name ?? '—',
                    'action' => $e->predat_operateru ? 'Predaja otpada' : 'Unos otpada',
                    'wasteType' => trim($e->indeksni_broj.' '.($e->naziv_otpada ?? '')),
                    'amount' => DnevnaEvidencija::formatKolicina(
                        $e->predat_operateru ? $e->predata_kolicina : $e->proizvedena_kolicina
                    ),
                    'status' => $predato ? 'success' : ($e->predat_operateru ? 'pending' : 'success'),
                    'url' => route('admin.klijenti.show', $e->team_id),
                ];
            })
            ->all();
    }

    // === SEKCIJA 6a: upozorenja ===

    /**
     * @return array<int, array<string, mixed>>
     */
    public function alerts(): array
    {
        $now = Carbon::now();
        $teams = Team::all(['id', 'name', 'created_at', 'pib', 'delatnost']);
        $lastActivity = $this->lastActivityByTeam();
        $lastDelivery = $this->lastDeliveryByTeam();
        $storage = $this->storageByTeam($now);
        $entryCounts = $this->entryCountByTeam();
        $dokoCounts = DokumentKretanja::selectRaw('team_id, COUNT(*) as c')->groupBy('team_id')->pluck('c', 'team_id');

        $alerts = [];

        foreach ($teams as $team) {
            $last = $lastActivity->get($team->id);
            $lastDate = $last ? Carbon::parse($last) : null;
            $storageStanje = $storage[$team->id] ?? 0.0;
            $entries = (int) ($entryCounts[$team->id] ?? 0);
            $url = route('admin.klijenti.show', $team->id);

            // 🔴 Negativno stanje skladišta (Predato > Proizvedeno)
            if ($storageStanje < -0.001) {
                $alerts[] = $this->alert('critical', $team->name,
                    'Negativno stanje skladišta ('.DnevnaEvidencija::formatKolicina($storageStanje).') — predato više nego proizvedeno.',
                    $now, $url);
            }

            // 🔴 Nema unos > 90 dana (firma starija od 90 dana)
            if ($team->created_at->lt($now->copy()->subDays(90))
                && (! $lastDate || $lastDate->lt($now->copy()->subDays(90)))) {
                $desc = $lastDate
                    ? 'Nema nijedan unos '.$lastDate->diffInDays($now).' dana.'
                    : 'Firma nema nijedan unos otpada.';
                $alerts[] = $this->alert('critical', $team->name, $desc, $lastDate ?? $team->created_at, $url);
            }

            // 🟡 Nije predao otpad > 60 dana, a ima otpada na skladištu
            $lastDel = $lastDelivery->get($team->id);
            $lastDelDate = $lastDel ? Carbon::parse($lastDel) : null;
            if ($storageStanje > 0.001
                && (! $lastDelDate || $lastDelDate->lt($now->copy()->subDays(60)))
                && $entries > 0) {
                $desc = $lastDelDate
                    ? 'Nije predao otpad '.$lastDelDate->diffInDays($now).' dana (na skladištu '.DnevnaEvidencija::formatKolicina($storageStanje).').'
                    : 'Nikada nije predao otpad (na skladištu '.DnevnaEvidencija::formatKolicina($storageStanje).').';
                $alerts[] = $this->alert('warning', $team->name, $desc, $lastDelDate ?? $now, $url);
            }

            // 🟡 Više od 10t na skladištu bez predaje
            if ($storageStanje > 10) {
                $alerts[] = $this->alert('warning', $team->name,
                    'Veliko stanje na skladištu: '.DnevnaEvidencija::formatKolicina($storageStanje).'.',
                    $now, $url);
            }

            // 🔵 Nov klijent, čeka konfiguraciju
            if ($team->created_at->gte($now->copy()->subDays(14)) && $entries === 0) {
                $alerts[] = $this->alert('info', $team->name,
                    'Nov klijent registrovan, čeka konfiguraciju i prve unose.',
                    $team->created_at, $url);
            }

            // 🔵 Nema dokumentovanu predaju (bez ijednog DOKO), a ima unose
            if ($entries > 0 && (int) ($dokoCounts[$team->id] ?? 0) === 0) {
                $alerts[] = $this->alert('info', $team->name,
                    'Nema evidentiran ugovor/predaju sa operaterom (nijedan DOKO dokument).',
                    $now, $url);
            }
        }

        // 🔵 Plan upravljanja otpadom ističe za < 30 dana
        foreach (WastePlan::with('createdBy')->get() as $plan) {
            $expiry = $this->planExpiry($plan);
            if ($expiry && $expiry->isFuture() && $expiry->lte($now->copy()->addDays(30))) {
                $alerts[] = $this->alert('info', $plan->company_name,
                    'Plan upravljanja otpadom ističe '.$expiry->format('d.m.Y.').' ('.$now->diffInDays($expiry).' dana).',
                    $now, route('admin.waste-plans.index'));
            }
        }

        $order = ['critical' => 0, 'warning' => 1, 'info' => 2];

        usort($alerts, fn ($a, $b) => $order[$a['severity']] <=> $order[$b['severity']]);

        return $alerts;
    }

    // === SEKCIJA 6b: poslednje predaje ===

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recentDeliveries(): array
    {
        return DnevnaEvidencija::query()
            ->where('predat_operateru', true)
            ->with('team:id,name')
            ->orderByDesc('datum_predaje_operateru')
            ->orderByDesc('id')
            ->take(10)
            ->get()
            ->map(fn (DnevnaEvidencija $e) => [
                'client' => $e->team?->name ?? '—',
                'date' => optional($e->datum_predaje_operateru)->format('d.m.Y.') ?? '—',
                'wasteType' => trim($e->indeksni_broj.' '.($e->naziv_otpada ?? '')),
                'amount' => DnevnaEvidencija::formatKolicina($e->predata_kolicina),
                'operator' => $e->operater_naziv ?: ($e->naziv_primaoca ?: '—'),
                'hasDocument' => (bool) $e->dokument_kretanja_id,
            ])
            ->all();
    }

    // === SEKCIJA 7: footer ===

    /**
     * @return array<string, mixed>
     */
    private function footerStats(): array
    {
        $firstTeam = Team::orderBy('created_at')->first();
        $activeDays = $firstTeam ? (int) $firstTeam->created_at->diffInDays(now()) : 0;

        return [
            'activeDays' => $activeDays,
            'totalEntriesAllTime' => DnevnaEvidencija::count(),
            'lastSync' => now()->format('H:i'),
            'version' => config('app.version', '1.0'),
        ];
    }

    // === POMOĆNE METODE ===

    private function sumProduced(Carbon $start, Carbon $end): float
    {
        return (float) DnevnaEvidencija::whereBetween('datum', [$start->toDateString(), $end->toDateString()])
            ->sum('proizvedena_kolicina');
    }

    private function sumDelivered(Carbon $start, Carbon $end): float
    {
        return (float) DnevnaEvidencija::whereBetween('datum', [$start->toDateString(), $end->toDateString()])
            ->where('predat_operateru', true)
            ->sum('predata_kolicina');
    }

    private function countEntries(Carbon $start, Carbon $end): int
    {
        return (int) DnevnaEvidencija::whereBetween('datum', [$start->toDateString(), $end->toDateString()])->count();
    }

    /**
     * Poslednji datum aktivnosti (max datum) po firmi.
     *
     * @return Collection<int, string>
     */
    private function lastActivityByTeam(): Collection
    {
        return DnevnaEvidencija::selectRaw('team_id, MAX(datum) as last_datum')
            ->groupBy('team_id')
            ->pluck('last_datum', 'team_id');
    }

    /**
     * @return Collection<int, string>
     */
    private function lastDeliveryByTeam(): Collection
    {
        return DnevnaEvidencija::where('predat_operateru', true)
            ->selectRaw('team_id, MAX(datum_predaje_operateru) as last_pred')
            ->groupBy('team_id')
            ->pluck('last_pred', 'team_id');
    }

    /**
     * @return Collection<int, int>
     */
    private function entryCountByTeam(): Collection
    {
        return DnevnaEvidencija::selectRaw('team_id, COUNT(*) as c')
            ->groupBy('team_id')
            ->pluck('c', 'team_id');
    }

    /**
     * Stanje skladišta po firmi = Σ po (firma, indeksni broj) poslednjeg kumulativnog stanja.
     *
     * @return array<int, float>
     */
    private function storageByTeam(?Carbon $asOf = null): array
    {
        $query = DnevnaEvidencija::query()->whereNotNull('datum');

        if ($asOf) {
            $query->where('datum', '<=', $asOf->toDateString());
        }

        $records = $query
            ->orderBy('team_id')
            ->orderBy('indeksni_broj')
            ->orderBy('datum')
            ->orderBy('id')
            ->get(['team_id', 'indeksni_broj', 'stanje_na_skladistu']);

        $result = [];

        foreach ($records->groupBy('team_id') as $teamId => $teamRecords) {
            $sum = 0.0;
            foreach ($teamRecords->groupBy('indeksni_broj') as $group) {
                $sum += (float) $group->last()->stanje_na_skladistu;
            }
            $result[(int) $teamId] = round($sum, 2);
        }

        return $result;
    }

    private function planExpiry(WastePlan $plan): ?Carbon
    {
        $rok = $plan->form_data['rok_vazenja'] ?? null;

        if (! $rok || ! preg_match('/(\d+)/', (string) $rok, $m)) {
            return null;
        }

        return $plan->generated_at->copy()->addYears((int) $m[1]);
    }

    /**
     * @return array<string, mixed>
     */
    private function alert(string $severity, string $client, string $message, Carbon $createdAt, string $actionUrl): array
    {
        return [
            'severity' => $severity,
            'client' => $client,
            'message' => $message,
            'createdAt' => $createdAt->toIso8601String(),
            'createdAtHuman' => $createdAt->diffForHumans(),
            'actionUrl' => $actionUrl,
        ];
    }

    private function pct(float $current, float $previous): float
    {
        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round(($current - $previous) / abs($previous) * 100, 1);
    }
}

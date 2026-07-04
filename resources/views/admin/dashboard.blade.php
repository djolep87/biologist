<x-layouts.admin title="Dashboard" header="Command Center">
    <div
        x-data="adminDashboard({
            statsUrl: @js(route('admin.dashboard-stats')),
            klijentiUrl: @js(route('admin.klijenti.index')),
            teamsCreateUrl: @js(route('teams.create')),
            wastePlansUrl: @js(route('admin.waste-plans.index')),
            userName: @js(Auth::user()->name),
        })"
        x-init="init()"
        class="space-y-6"
    >
        {{-- ============ 1. WELCOME BAR ============ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 flex flex-col lg:flex-row lg:items-center gap-4 lg:gap-6">
            <div class="min-w-0 flex-1">
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900 truncate">
                    <span x-text="greeting"></span>, {{ Auth::user()->name }} 👋
                </h2>
                <p class="text-sm text-gray-500 mt-0.5" x-text="periodLabel"></p>
            </div>

            <div class="flex items-center justify-center gap-2 text-center">
                <div class="rounded-xl bg-[#f8f9f4] border border-gray-100 px-4 py-2">
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold" x-text="clockDate"></p>
                    <p class="font-['Plus_Jakarta_Sans',sans-serif] text-xl font-bold text-gray-900 tabular-nums" x-text="clockTime"></p>
                </div>
            </div>

            <div class="flex items-center gap-2 lg:justify-end flex-wrap">
                <a :href="cfg.teamsCreateUrl"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors">
                    + Nova firma
                </a>
                <button type="button" @click="exportReport()"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                    ⬇ Eksportuj izveštaj
                </button>
            </div>
        </div>

        {{-- ============ PERIOD FILTER ============ --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 mr-1">Period:</span>
            <template x-for="opt in periodOptions" :key="opt.value">
                <button type="button" @click="applyPeriod(opt.value)"
                    :class="period === opt.value ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
                    class="px-3.5 py-1.5 rounded-lg border text-sm font-medium transition-colors"
                    x-text="opt.label"></button>
            </template>

            <div class="flex items-center gap-2" :class="period === 'custom' ? 'opacity-100' : 'opacity-60'">
                <input type="date" x-model="customStart"
                    class="rounded-lg border-gray-200 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                <span class="text-gray-400 text-sm">–</span>
                <input type="date" x-model="customEnd"
                    class="rounded-lg border-gray-200 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                <button type="button" @click="applyCustom()"
                    class="px-3 py-1.5 rounded-lg bg-gray-800 text-white text-sm font-medium hover:bg-gray-900">
                    Primeni
                </button>
            </div>
        </div>

        {{-- ============ 2. KPI RED 1 — sistemske metrike ============ --}}
        <div>
            {{-- skeleton --}}
            <template x-if="!data">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
                    <template x-for="i in 6" :key="i">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-pulse">
                            <div class="h-8 w-8 rounded-lg bg-gray-100 mb-3"></div>
                            <div class="h-6 w-16 bg-gray-100 rounded mb-2"></div>
                            <div class="h-3 w-24 bg-gray-100 rounded"></div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="data">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
                    {{-- Ukupno klijenata --}}
                    <div @click="window.location.href=cfg.klijentiUrl"
                        class="group cursor-pointer bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-lg">🏢</span>
                            <span class="text-xs font-semibold text-blue-600" x-show="data.systemStats.newClientsThisMonth > 0"
                                x-text="'↑ ' + data.systemStats.newClientsThisMonth + ' novih'"></span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] tabular-nums" x-text="fmt(disp.totalClients, 0)"></p>
                        <p class="text-xs text-gray-500 mt-1">Ukupno klijenata</p>
                    </div>

                    {{-- Aktivni klijenti --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center text-lg">✅</span>
                            <span class="text-xs font-semibold text-green-600" x-text="data.systemStats.activePercentage + '% aktivnih'"></span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] tabular-nums" x-text="fmt(disp.activeClients, 0)"></p>
                        <p class="text-xs text-gray-500 mt-1">Aktivni klijenti (30 dana)</p>
                    </div>

                    {{-- Neaktivni klijenti --}}
                    <div @click="window.location.href=cfg.klijentiUrl"
                        class="group cursor-pointer bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-orange-50 flex items-center justify-center text-lg">⚠️</span>
                            <span class="text-xs font-semibold text-orange-600" x-show="data.systemStats.avgInactiveDays > 0"
                                x-text="'~' + data.systemStats.avgInactiveDays + ' dana'"></span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] tabular-nums" x-text="fmt(disp.inactiveClients, 0)"></p>
                        <p class="text-xs text-gray-500 mt-1">Neaktivni (&gt;60 dana)</p>
                    </div>

                    {{-- Ukupno korisnika --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center text-lg">👥</span>
                            <span class="text-xs font-semibold text-purple-600" x-show="data.systemStats.newUsersThisMonth > 0"
                                x-text="'↑ ' + data.systemStats.newUsersThisMonth"></span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] tabular-nums" x-text="fmt(disp.totalUsers, 0)"></p>
                        <p class="text-xs text-gray-500 mt-1">Ukupno korisnika</p>
                    </div>

                    {{-- Generisani planovi --}}
                    <div @click="window.location.href=cfg.wastePlansUrl"
                        class="group cursor-pointer bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-teal-50 flex items-center justify-center text-lg">📄</span>
                            <span class="text-xs font-semibold text-teal-600" x-show="data.systemStats.newPlansThisMonth > 0"
                                x-text="'↑ ' + data.systemStats.newPlansThisMonth"></span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] tabular-nums" x-text="fmt(disp.totalPlans, 0)"></p>
                        <p class="text-xs text-gray-500 mt-1">Generisani planovi</p>
                    </div>

                    {{-- Sistemska upozorenja --}}
                    <div @click="scrollToAlerts()"
                        :class="data.systemStats.alertsCount > 0 ? 'border-red-200 bg-red-50/40' : 'border-gray-100'"
                        class="group cursor-pointer bg-white rounded-2xl shadow-sm border p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span :class="data.systemStats.alertsCount > 0 ? 'bg-red-100 animate-pulse' : 'bg-green-50'"
                                class="w-9 h-9 rounded-lg flex items-center justify-center text-lg">🔔</span>
                        </div>
                        <p class="text-2xl font-bold font-['Plus_Jakarta_Sans',sans-serif] tabular-nums"
                            :class="data.systemStats.alertsCount > 0 ? 'text-red-600' : 'text-green-600'"
                            x-text="fmt(disp.alertsCount, 0)"></p>
                        <p class="text-xs mt-1" :class="data.systemStats.alertsCount > 0 ? 'text-red-500' : 'text-green-600'"
                            x-text="data.systemStats.alertsCount > 0 ? 'Zahtevaju akciju' : 'Sve u redu'"></p>
                    </div>
                </div>
            </template>
        </div>

        {{-- ============ 3. KPI RED 2 — otpad metrike ============ --}}
        <div>
            <template x-if="!data">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
                    <template x-for="i in 5" :key="i">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 animate-pulse">
                            <div class="h-8 w-8 rounded-lg bg-gray-100 mb-3"></div>
                            <div class="h-6 w-20 bg-gray-100 rounded mb-2"></div>
                            <div class="h-3 w-24 bg-gray-100 rounded"></div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="data">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
                    {{-- Ukupno otpada --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center text-lg">♻️</span>
                            <span class="text-xs font-semibold" :class="trendClass(data.wasteStats.producedTrend)"
                                x-text="trendText(data.wasteStats.producedTrend)"></span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] tabular-nums">
                            <span x-text="fmt(disp.totalProduced, 1)"></span> <span class="text-base font-normal text-gray-400">t</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Ukupno otpada (sve firme)</p>
                    </div>

                    {{-- Ukupno predato --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-lg">🚛</span>
                            <span class="text-xs font-semibold" :class="trendClass(data.wasteStats.deliveredTrend)"
                                x-text="trendText(data.wasteStats.deliveredTrend)"></span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] tabular-nums">
                            <span x-text="fmt(disp.totalDelivered, 1)"></span> <span class="text-base font-normal text-gray-400">t</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Ukupno predato</p>
                    </div>

                    {{-- Na skladištu --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-lg">🏭</span>
                            <span class="text-xs font-semibold" :class="data.wasteStats.totalInStorage < 0 ? 'text-red-600' : 'text-gray-400'"
                                x-show="data.wasteStats.totalInStorage < 0">negativno!</span>
                        </div>
                        <p class="text-2xl font-bold font-['Plus_Jakarta_Sans',sans-serif] tabular-nums"
                            :class="data.wasteStats.totalInStorage < 0 ? 'text-red-600' : 'text-gray-900'">
                            <span x-text="fmt(disp.totalInStorage, 1)"></span> <span class="text-base font-normal text-gray-400">t</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Ukupno na skladištu</p>
                    </div>

                    {{-- Broj unosa ovog meseca --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center text-lg">📝</span>
                            <span class="text-xs font-semibold" :class="trendClass(data.wasteStats.entriesTrend)"
                                x-text="trendText(data.wasteStats.entriesTrend)"></span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] tabular-nums" x-text="fmt(disp.totalEntries, 0)"></p>
                        <p class="text-xs text-gray-500 mt-1">Broj unosa (period)</p>
                    </div>

                    {{-- Prosečno po klijentu --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-9 h-9 rounded-lg bg-cyan-50 flex items-center justify-center text-lg">📊</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] tabular-nums">
                            <span x-text="fmt(disp.averagePerClient, 1)"></span> <span class="text-base font-normal text-gray-400">t</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Prosečno po klijentu</p>
                    </div>
                </div>
            </template>
        </div>

        {{-- ============ 4. GRAFIKONI ============ --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- Levo 60% --}}
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900">Otpad po mesecima — sve firme</h3>
                        <p class="text-sm text-gray-500" x-text="'Godina: ' + (data ? data.period.year : '')"></p>
                    </div>
                    <div class="inline-flex rounded-lg border border-gray-200 p-0.5 bg-gray-50">
                        <template x-for="t in ['bar','line','area']" :key="t">
                            <button type="button" @click="setChartType(t)"
                                :class="chartType === t ? 'bg-white shadow text-indigo-600' : 'text-gray-500'"
                                class="px-3 py-1 rounded-md text-xs font-semibold capitalize transition-colors" x-text="t"></button>
                        </template>
                    </div>
                </div>
                <div class="relative" style="height: 320px;">
                    <div x-show="!data" class="absolute inset-0 animate-pulse bg-gray-50 rounded-xl"></div>
                    <canvas x-ref="monthlyCanvas"></canvas>
                </div>
            </div>

            {{-- Desno 40% --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900 mb-1">Top 5 klijenata</h3>
                <p class="text-sm text-gray-500 mb-4">Po ukupnoj količini otpada</p>
                <div class="relative flex-1" style="min-height: 260px;">
                    <div x-show="!data" class="absolute inset-0 animate-pulse bg-gray-50 rounded-xl"></div>
                    <canvas x-ref="topCanvas"></canvas>
                </div>
                <template x-if="data && data.topClients.length === 0">
                    <p class="text-sm text-gray-400 text-center py-8">Nema podataka za izabrani period.</p>
                </template>
                <a :href="cfg.klijentiUrl" class="mt-4 text-sm text-indigo-600 hover:text-indigo-800 font-medium self-start">Vidi sve klijente →</a>
            </div>
        </div>

        {{-- ============ 5. TABELA — poslednja aktivnost ============ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900">Poslednje aktivnosti — svi klijenti</h3>
                    <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Uživo · osvežava se svakih 30s
                    </p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#f8f9f4]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vreme</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Klijent</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Korisnik</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akcija</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vrsta otpada</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Količina</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-if="!data">
                            <template x-for="i in 6" :key="i">
                                <tr class="animate-pulse">
                                    <td class="px-4 py-3" colspan="7"><div class="h-4 bg-gray-100 rounded w-full"></div></td>
                                </tr>
                            </template>
                        </template>
                        <template x-if="data">
                            <template x-for="row in data.recentActivity" :key="row.id">
                                <tr class="hover:bg-gray-50/60 cursor-pointer" @click="window.location.href=row.url">
                                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap" x-text="timeAgo(row.time)"></td>
                                    <td class="px-4 py-3 font-medium text-gray-900" x-text="row.client"></td>
                                    <td class="px-4 py-3 text-gray-600" x-text="row.user"></td>
                                    <td class="px-4 py-3 text-gray-600" x-text="row.action"></td>
                                    <td class="px-4 py-3 text-gray-600" x-text="row.wasteType"></td>
                                    <td class="px-4 py-3 text-gray-900 font-medium whitespace-nowrap" x-text="row.amount"></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-green-100 text-green-800': row.status === 'success',
                                                'bg-amber-100 text-amber-800': row.status === 'pending',
                                                'bg-red-100 text-red-800': row.status === 'error'
                                            }"
                                            x-text="statusLabel(row.status)"></span>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        <template x-if="data && data.recentActivity.length === 0">
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Nema aktivnosti.</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-100">
                <a href="{{ route('admin.evidencije.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Vidi sve aktivnosti →</a>
            </div>
        </div>

        {{-- ============ 6. DVA BLOKA ============ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="alerts-section">
            {{-- Levo: upozorenja --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900">⚠️ Upozorenja i akcije koje čekaju</h3>
                </div>
                <div class="p-6 space-y-3">
                    <template x-if="!data">
                        <div class="space-y-3">
                            <template x-for="i in 3" :key="i">
                                <div class="h-16 bg-gray-50 rounded-xl animate-pulse"></div>
                            </template>
                        </div>
                    </template>

                    <template x-if="data && data.alerts.length === 0">
                        <div class="rounded-xl bg-green-50 border border-green-100 px-4 py-6 text-center">
                            <p class="text-2xl mb-1">✅</p>
                            <p class="text-sm font-medium text-green-800">Sve firme uredno posluju</p>
                        </div>
                    </template>

                    <template x-if="data">
                        <div class="space-y-3">
                            <template x-for="(a, idx) in visibleAlerts" :key="idx">
                                <div class="flex items-start gap-3 rounded-xl border p-3.5"
                                    :class="{
                                        'bg-red-50 border-red-100': a.severity === 'critical',
                                        'bg-amber-50 border-amber-100': a.severity === 'warning',
                                        'bg-blue-50 border-blue-100': a.severity === 'info'
                                    }">
                                    <span class="text-lg shrink-0" x-text="severityIcon(a.severity)"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-gray-900 truncate" x-text="a.client"></p>
                                        <p class="text-sm text-gray-600" x-text="a.message"></p>
                                        <p class="text-xs text-gray-400 mt-0.5" x-text="a.createdAtHuman"></p>
                                    </div>
                                    <a :href="a.actionUrl" class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800 whitespace-nowrap mt-0.5">Reši →</a>
                                </div>
                            </template>
                            <button type="button" x-show="data.alerts.length > 8 && !showAllAlerts" @click="showAllAlerts = true"
                                class="w-full text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium py-2"
                                x-text="'Vidi još (' + (data.alerts.length - 8) + ')'"></button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Desno: poslednje predaje --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900">🚛 Poslednje predaje otpada</h3>
                </div>
                <div class="p-6 flex-1">
                    <template x-if="!data">
                        <div class="space-y-3">
                            <template x-for="i in 4" :key="i">
                                <div class="h-14 bg-gray-50 rounded-xl animate-pulse"></div>
                            </template>
                        </div>
                    </template>

                    <template x-if="data && data.recentDeliveries.length === 0">
                        <p class="text-sm text-gray-400 text-center py-8">Nema evidentiranih predaja.</p>
                    </template>

                    <template x-if="data">
                        <ul class="divide-y divide-gray-50">
                            <template x-for="(d, idx) in data.recentDeliveries" :key="idx">
                                <li class="py-3 first:pt-0 flex items-start gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900 truncate" x-text="d.client"></p>
                                            <span class="text-xs px-1.5 py-0.5 rounded-full shrink-0"
                                                :class="d.hasDocument ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'"
                                                x-text="d.hasDocument ? '✅ Dokumentovano' : '⚠️ Bez dokumenta'"></span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5" x-text="d.wasteType"></p>
                                        <p class="text-xs text-gray-400" x-text="'Operater: ' + d.operator"></p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-bold text-gray-900" x-text="d.amount"></p>
                                        <p class="text-xs text-gray-400" x-text="d.date"></p>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </template>
                </div>
                <div class="px-6 py-3 border-t border-gray-100 bg-[#f8f9f4]">
                    <p class="text-xs text-gray-600" x-show="data" x-text="deliveriesSummary"></p>
                </div>
            </div>
        </div>

        {{-- ============ 7. FOOTER STATISTIKE ============ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
            <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-2 text-xs text-gray-500">
                <span>Sistem aktivan: <strong class="text-gray-700" x-text="data ? data.footer.activeDays : '—'"></strong> dana</span>
                <span>Ukupno unosa u bazi: <strong class="text-gray-700" x-text="data ? fmt(data.footer.totalEntriesAllTime, 0) : '—'"></strong></span>
                <span>Poslednja sinhronizacija: <strong class="text-gray-700" x-text="lastSync"></strong></span>
                <span>Verzija aplikacije: <strong class="text-gray-700" x-text="data ? data.footer.version : '—'"></strong></span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        function adminDashboard(cfg) {
            return {
                cfg: cfg,
                period: 'this_year',
                customStart: '',
                customEnd: '',
                data: null,
                disp: {
                    totalClients: 0, activeClients: 0, inactiveClients: 0, totalUsers: 0, totalPlans: 0, alertsCount: 0,
                    totalProduced: 0, totalDelivered: 0, totalInStorage: 0, totalEntries: 0, averagePerClient: 0,
                },
                greeting: '',
                clockDate: '',
                clockTime: '',
                lastSync: '—',
                chartType: 'bar',
                monthlyChartObj: null,
                topChartObj: null,
                showAllAlerts: false,
                pollTimer: null,
                periodOptions: [
                    { value: 'this_year', label: 'Ova godina' },
                    { value: 'last_year', label: 'Prošla godina' },
                    { value: 'this_month', label: 'Ovaj mesec' },
                    { value: 'custom', label: 'Prilagođeno' },
                ],

                init() {
                    this.setGreeting();
                    this.tickClock();
                    setInterval(() => this.tickClock(), 1000);
                    this.load();
                    this.pollTimer = setInterval(() => this.pollRecent(), 30000);
                },

                get periodLabel() {
                    return this.data ? this.data.period.label : 'Učitavanje podataka...';
                },

                get visibleAlerts() {
                    if (!this.data) return [];
                    return this.showAllAlerts ? this.data.alerts : this.data.alerts.slice(0, 8);
                },

                get deliveriesSummary() {
                    if (!this.data) return '';
                    var t = this.fmt(this.data.wasteStats.totalDelivered, 1);
                    var y = this.data.systemStats.activeClients;
                    return 'Ukupno predato (period): ' + t + ' t · ' + y + ' aktivnih klijenata';
                },

                setGreeting() {
                    var h = new Date().getHours();
                    if (h < 12) this.greeting = 'Dobro jutro';
                    else if (h < 18) this.greeting = 'Dobro popodne';
                    else this.greeting = 'Dobro veče';
                },

                tickClock() {
                    var d = new Date();
                    var days = ['Nedelja','Ponedeljak','Utorak','Sreda','Četvrtak','Petak','Subota'];
                    var months = ['januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar'];
                    this.clockDate = days[d.getDay()] + ', ' + d.getDate() + '. ' + months[d.getMonth()] + ' ' + d.getFullYear() + '.';
                    var p = function(n){ return n < 10 ? '0'+n : ''+n; };
                    this.clockTime = p(d.getHours()) + ':' + p(d.getMinutes()) + ':' + p(d.getSeconds());
                },

                applyPeriod(p) {
                    if (p === 'custom') { this.period = 'custom'; return; }
                    this.period = p;
                    this.load();
                },

                applyCustom() {
                    if (!this.customStart || !this.customEnd) return;
                    this.period = 'custom';
                    this.load();
                },

                buildUrl(only) {
                    var params = new URLSearchParams();
                    params.set('period', this.period);
                    if (this.period === 'custom') {
                        params.set('start', this.customStart);
                        params.set('end', this.customEnd);
                    }
                    if (only) params.set('only', 'recent');
                    return this.cfg.statsUrl + '?' + params.toString();
                },

                async load() {
                    this.data = null;
                    this.showAllAlerts = false;
                    try {
                        var res = await fetch(this.buildUrl(false), { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        var json = await res.json();
                        this.data = json;
                        this.lastSync = this.nowTime();
                        this.animateNumbers();
                        this.$nextTick(() => this.renderCharts());
                    } catch (e) {
                        console.error('Dashboard load failed', e);
                    }
                },

                async pollRecent() {
                    if (!this.data) return;
                    try {
                        var res = await fetch(this.buildUrl(true), { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        var json = await res.json();
                        this.data.recentActivity = json.recentActivity;
                        this.data.recentDeliveries = json.recentDeliveries;
                        this.lastSync = this.nowTime();
                    } catch (e) { /* tiho */ }
                },

                nowTime() {
                    var d = new Date();
                    var p = function(n){ return n < 10 ? '0'+n : ''+n; };
                    return p(d.getHours()) + ':' + p(d.getMinutes());
                },

                animateNumbers() {
                    var s = this.data.systemStats, w = this.data.wasteStats;
                    this.animateValue('totalClients', s.totalClients);
                    this.animateValue('activeClients', s.activeClients);
                    this.animateValue('inactiveClients', s.inactiveClients);
                    this.animateValue('totalUsers', s.totalUsers);
                    this.animateValue('totalPlans', s.totalPlans);
                    this.animateValue('alertsCount', s.alertsCount);
                    this.animateValue('totalProduced', w.totalProduced);
                    this.animateValue('totalDelivered', w.totalDelivered);
                    this.animateValue('totalInStorage', w.totalInStorage);
                    this.animateValue('totalEntries', w.totalEntries);
                    this.animateValue('averagePerClient', w.averagePerClient);
                },

                animateValue(key, target) {
                    var self = this;
                    var start = 0;
                    var duration = 800;
                    var startTime = null;
                    function step(ts) {
                        if (!startTime) startTime = ts;
                        var progress = Math.min((ts - startTime) / duration, 1);
                        var eased = 1 - Math.pow(1 - progress, 3);
                        self.disp[key] = start + (target - start) * eased;
                        if (progress < 1) requestAnimationFrame(step);
                        else self.disp[key] = target;
                    }
                    requestAnimationFrame(step);
                },

                fmt(n, dec) {
                    if (n === null || n === undefined || isNaN(n)) return '0';
                    return new Intl.NumberFormat('sr-RS', { minimumFractionDigits: dec, maximumFractionDigits: dec }).format(n);
                },

                trendClass(v) {
                    if (v > 0) return 'text-green-600';
                    if (v < 0) return 'text-red-600';
                    return 'text-gray-400';
                },

                trendText(v) {
                    var arrow = v > 0 ? '↑' : (v < 0 ? '↓' : '→');
                    return arrow + ' ' + this.fmt(Math.abs(v), 1) + '%';
                },

                statusLabel(s) {
                    if (s === 'success') return '✅ Uspešno';
                    if (s === 'pending') return '⏳ Na čekanju';
                    return '❌ Greška';
                },

                severityIcon(s) {
                    if (s === 'critical') return '🔴';
                    if (s === 'warning') return '🟡';
                    return '🔵';
                },

                timeAgo(iso) {
                    if (!iso) return '—';
                    var d = new Date(iso);
                    var diff = Math.floor((Date.now() - d.getTime()) / 1000);
                    if (diff < 60) return 'upravo';
                    if (diff < 3600) return Math.floor(diff/60) + ' min';
                    if (diff < 86400) return Math.floor(diff/3600) + ' h';
                    var p = function(n){ return n < 10 ? '0'+n : ''+n; };
                    return p(d.getDate()) + '.' + p(d.getMonth()+1) + '. ' + p(d.getHours()) + ':' + p(d.getMinutes());
                },

                scrollToAlerts() {
                    var el = document.getElementById('alerts-section');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                },

                setChartType(t) {
                    this.chartType = t;
                    this.renderMonthlyChart();
                },

                renderCharts() {
                    this.renderMonthlyChart();
                    this.renderTopChart();
                },

                renderMonthlyChart() {
                    if (!this.data || typeof Chart === 'undefined') return;
                    var ctx = this.$refs.monthlyCanvas;
                    if (!ctx) return;
                    if (this.monthlyChartObj) this.monthlyChartObj.destroy();

                    var labels = this.data.monthlyChart.map(function(m){ return m.month; });
                    var produced = this.data.monthlyChart.map(function(m){ return m.produced; });
                    var delivered = this.data.monthlyChart.map(function(m){ return m.delivered; });
                    var storage = this.data.monthlyChart.map(function(m){ return m.inStorage; });

                    var isLine = this.chartType !== 'bar';
                    var fill = this.chartType === 'area';

                    var mk = function(label, arr, color) {
                        return {
                            label: label,
                            data: arr,
                            backgroundColor: isLine ? (fill ? color + '33' : color) : color,
                            borderColor: color,
                            borderWidth: 2,
                            fill: isLine ? fill : true,
                            tension: 0.35,
                            pointRadius: isLine ? 2 : 0,
                            borderRadius: isLine ? 0 : 6,
                        };
                    };

                    this.monthlyChartObj = new Chart(ctx, {
                        type: isLine ? 'line' : 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                mk('Proizvedeno', produced, '#3b82f6'),
                                mk('Predato', delivered, '#22c55e'),
                                mk('Na skladištu', storage, '#9ca3af'),
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16, font: { size: 12 } } },
                                tooltip: {
                                    callbacks: {
                                        label: function(c){ return c.dataset.label + ': ' + c.parsed.y.toLocaleString('sr-RS', {maximumFractionDigits: 2}) + ' t'; }
                                    }
                                }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { callback: function(v){ return v + ' t'; } }, grid: { color: '#f1f1f1' } },
                                x: { grid: { display: false } },
                            },
                        },
                    });
                },

                renderTopChart() {
                    if (!this.data || typeof Chart === 'undefined') return;
                    var ctx = this.$refs.topCanvas;
                    if (!ctx) return;
                    if (this.topChartObj) this.topChartObj.destroy();
                    if (this.data.topClients.length === 0) return;

                    var labels = this.data.topClients.map(function(c){ return c.name; });
                    var amounts = this.data.topClients.map(function(c){ return c.amount; });
                    var pcts = this.data.topClients.map(function(c){ return c.percentage; });
                    var shades = ['#4338ca', '#5b52d6', '#7c74e0', '#9d97ea', '#beb9f3'];

                    this.topChartObj = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: amounts,
                                backgroundColor: shades.slice(0, amounts.length),
                                borderRadius: 6,
                                barThickness: 26,
                            }],
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(c){
                                            return c.parsed.x.toLocaleString('sr-RS', {maximumFractionDigits: 2}) + ' t (' + pcts[c.dataIndex] + '%)';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { beginAtZero: true, ticks: { callback: function(v){ return v + ' t'; } }, grid: { color: '#f1f1f1' } },
                                y: { grid: { display: false } },
                            },
                        },
                    });
                },

                exportReport() {
                    if (!this.data) return;
                    var rows = [];
                    rows.push(['Izveštaj dashboard-a', this.data.period.label]);
                    rows.push([]);
                    rows.push(['Sistemske metrike']);
                    rows.push(['Ukupno klijenata', this.data.systemStats.totalClients]);
                    rows.push(['Aktivni klijenti', this.data.systemStats.activeClients]);
                    rows.push(['Neaktivni klijenti', this.data.systemStats.inactiveClients]);
                    rows.push(['Ukupno korisnika', this.data.systemStats.totalUsers]);
                    rows.push(['Generisani planovi', this.data.systemStats.totalPlans]);
                    rows.push(['Upozorenja', this.data.systemStats.alertsCount]);
                    rows.push([]);
                    rows.push(['Metrike otpada (t)']);
                    rows.push(['Ukupno proizvedeno', this.data.wasteStats.totalProduced]);
                    rows.push(['Ukupno predato', this.data.wasteStats.totalDelivered]);
                    rows.push(['Na skladištu', this.data.wasteStats.totalInStorage]);
                    rows.push(['Broj unosa', this.data.wasteStats.totalEntries]);
                    rows.push(['Prosečno po klijentu', this.data.wasteStats.averagePerClient]);
                    rows.push([]);
                    rows.push(['Top klijenti', 'Količina (t)', 'Procenat (%)']);
                    this.data.topClients.forEach(function(c){ rows.push([c.name, c.amount, c.percentage]); });
                    rows.push([]);
                    rows.push(['Mesec', 'Proizvedeno', 'Predato', 'Na skladištu']);
                    this.data.monthlyChart.forEach(function(m){ rows.push([m.month, m.produced, m.delivered, m.inStorage]); });

                    var csv = rows.map(function(r){
                        return r.map(function(cell){
                            var s = ('' + (cell === undefined ? '' : cell)).replace(/"/g, '""');
                            return '"' + s + '"';
                        }).join(',');
                    }).join('\n');

                    var blob = new Blob(["\ufeff" + csv], { type: 'text/csv;charset=utf-8;' });
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'dashboard-izvestaj-' + this.data.period.key + '.csv';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                },
            };
        }
    </script>
    @endpush
</x-layouts.admin>

@php
    $formatKolicina = function ($val) {
        if ($val === null || $val === '') {
            return '';
        }
        $val = (float) $val;
        if ($val == 0.0) {
            return '';
        }

        return $val == floor($val)
            ? number_format($val, 0, ',', '.')
            : rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ',');
    };

    $oznaka = fn ($bool) => $bool ? 'X' : '';

    // Vertikalni natpis za uske kolone (DomPDF ne podržava rotate pouzdano)
    $v = fn (string $text) => str_replace(' ', '<br>', $text);
@endphp

<div class="deo1-document">
<table class="deo1-table {{ !empty($pageBreak) ? 'page-break' : '' }}">
    <colgroup>
        <col style="width:7%">
        <col style="width:7%">
        <col style="width:7%">
        <col style="width:7%">
        <col style="width:4%">
        <col style="width:4%">
        <col style="width:3.5%">
        <col style="width:4%">
        <col style="width:3.5%">
        <col style="width:4%">
        <col style="width:18%">
        <col style="width:10%">
    </colgroup>

    {{-- ===== NASLOV ===== --}}
    <tr>
        <td colspan="10" class="header header-title">
            Dnevna evidencija o otpadu proizvođača otpada<sup>1</sup>
        </td>
        <td colspan="2" class="header header-ref">
            Prilog 1.<br>Obrazac DEO 1
        </td>
    </tr>

    {{-- ===== META ZAGLAVLJE (6 redova) ===== --}}
    <tr>
        <td colspan="4" class="meta-label">Godina</td>
        <td colspan="2" class="meta-value">{{ $evidencija->godina }}</td>
        <td colspan="6" class="meta-label"></td>
    </tr>
    <tr>
        <td colspan="4" class="meta-label">Mesec</td>
        <td colspan="2" class="meta-value">{{ str_pad($evidencija->mesec, 2, '0', STR_PAD_LEFT) }}</td>
        <td colspan="6" class="meta-label"></td>
    </tr>
    <tr>
        <td colspan="4" class="meta-label">Indeksni broj otpada iz Kataloga otpada</td>
        <td colspan="2" class="meta-value">{{ $evidencija->indeksni_broj }}</td>
        <td colspan="6" class="meta-label"></td>
    </tr>
    <tr>
        <td colspan="4" class="meta-label">Naziv otpada</td>
        <td colspan="8" class="meta-value-wide">{{ $evidencija->naziv_otpada }}</td>
    </tr>
    <tr>
        <td colspan="4" class="meta-label">Opis otpada</td>
        <td colspan="8" class="meta-value-wide">{{ $evidencija->opis_otpada ?? '' }}</td>
    </tr>
    <tr>
        <td colspan="4" class="meta-label">Evidenciju vodi (Ime i prezime)</td>
        <td colspan="8" class="meta-value-wide">{{ $evidencija->lice_koje_vodi ?? '' }}</td>
    </tr>

    {{-- ===== SEKCIJE ===== --}}
    <tr>
        <td colspan="4" class="section">Proizvedene količine otpada</td>
        <td colspan="8" class="section">Otpad predat</td>
    </tr>

    {{-- ===== ZAGLAVLJE KOLONA ===== --}}
    <tr>
        <th rowspan="2" class="subsection">Datum</th>
        <th rowspan="2" class="subsection">Proizvedena<br>količina otpada (t)</th>
        <th rowspan="2" class="subsection">Predata<br>količina otpada (t)</th>
        <th rowspan="2" class="subsection">Stanje na<br>privremenom<br>skladištu (t)</th>
        <th rowspan="2" class="subsection-vertical">{!! $v('Sakupljaču²') !!}</th>
        <th rowspan="2" class="subsection-vertical">{!! $v('Operateru na ponovno iskorišćenje²') !!}</th>
        <th rowspan="2" class="subsection-vertical">{!! $v('R oznaka') !!}</th>
        <th rowspan="2" class="subsection-vertical">{!! $v('Operateru na odlaganje²') !!}</th>
        <th rowspan="2" class="subsection-vertical">{!! $v('D oznaka') !!}</th>
        <th rowspan="2" class="subsection-vertical">{!! $v('Izvoz²') !!}</th>
        <th rowspan="2" class="subsection">Naziv preduzeća<br>kojem je otpad predat</th>
        <th rowspan="2" class="subsection">Broj<br>dozvole</th>
    </tr>
    <tr></tr>

    {{-- ===== 31 REDOVA ===== --}}
    @for ($dan = 1; $dan <= 31; $dan++)
        @php $red = $redovi[$dan] ?? null; @endphp
        <tr class="data-row">
            @if ($red && $dan <= $brojDanaUMesecu)
                <td>{{ $red->datum->format('d.m.Y.') }}</td>
                <td class="cell-right">{{ $formatKolicina($red->proizvedena_kolicina) }}</td>
                <td class="cell-right">{{ $formatKolicina($red->predata_kolicina) }}</td>
                <td class="cell-right">{{ $formatKolicina($red->stanje_na_skladistu) }}</td>
                <td>{{ $oznaka($red->predat_sakupljacu && (float) $red->predata_kolicina > 0) }}</td>
                <td>{{ $oznaka($red->predat_operateru_r && (float) $red->predata_kolicina > 0) }}</td>
                <td></td>
                <td>{{ $oznaka($red->predat_operateru_d && (float) $red->predata_kolicina > 0) }}</td>
                <td></td>
                <td>{{ $oznaka($red->izvoz && (float) $red->predata_kolicina > 0) }}</td>
                <td class="cell-left">{{ $red->naziv_primaoca ?? '' }}</td>
                <td class="cell-left">{{ $red->broj_dozvole_primaoca ?? '' }}</td>
            @else
                <td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
                <td></td><td></td>
            @endif
        </tr>
    @endfor

    {{-- ===== UKUPNO ===== --}}
    <tr>
        <td class="ukupno-label">UKUPNO</td>
        <td class="ukupno-value cell-right">{{ $formatKolicina($ukupnoProizvedeno) }}</td>
        <td class="ukupno-value cell-right">{{ $formatKolicina($ukupnoPredato) }}</td>
        <td class="ukupno-value cell-right">{{ $formatKolicina($krajnjeStanje) }}</td>
        <td colspan="6" class="ukupno-block"></td>
        <td class="ukupno-value"></td>
        <td class="ukupno-value"></td>
    </tr>
</table>

<div class="footnotes">
    <sup>1</sup> Evidencija se vodi za svaku vrstu otpada posebno.<br>
    <sup>2</sup> Označiti sa X u odgovarajućem polju.
</div>

<p class="doc-meta-footer">
    {{ $firma->name ?? '' }}
    @if (!empty($firma->pib)) | PIB: {{ $firma->pib }} @endif
    | Period: {{ $mesecNaziv }} {{ $godina }}
    | Generisano: {{ $generisano }}
</p>
</div>

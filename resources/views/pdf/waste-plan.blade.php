<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Plan upravljanja otpadom — {{ $plan->company_name }}</title>
    <style>
        @page {
            margin: 2.5cm;
        }
        @page :first {
            margin: 2.5cm;
        }
        body {
            font-family: "DejaVu Serif", "Times New Roman", Georgia, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1a1a1a;
        }
        .page {
            page-break-after: always;
            position: relative;
            min-height: 24cm;
        }
        .page:last-child {
            page-break-after: auto;
        }
        .page-cover {
            text-align: center;
            padding-top: 3cm;
        }
        .page-cover .company-name {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.5cm;
        }
        .page-cover .company-meta {
            font-size: 11pt;
            margin-bottom: 2cm;
            color: #333;
        }
        .page-cover .doc-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 1.5cm 0;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 0.6cm 0;
        }
        .page-cover .doc-meta {
            font-size: 11pt;
            line-height: 1.8;
            margin-top: 1cm;
        }
        .page-header {
            font-size: 9pt;
            color: #444;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .page-header .left { float: left; width: 50%; }
        .page-header .right { float: right; width: 50%; text-align: right; }
        .page-footer {
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 4px;
            margin-top: 20px;
            overflow: hidden;
        }
        .page-footer .left { float: left; width: 50%; }
        .page-footer .right { float: right; width: 50%; text-align: right; }
        .page-body {
            text-align: justify;
        }
        .section-heading {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0.8cm 0 0.4cm 0;
            color: #1e2430;
        }
        .sub-heading {
            font-size: 11pt;
            font-weight: bold;
            margin: 0.5cm 0 0.25cm 0;
            color: #2d3748;
        }
        p {
            margin: 0 0 0.35cm 0;
            text-align: justify;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.4cm 0 0.6cm 0;
            font-size: 10pt;
        }
        .data-table th {
            background-color: #e8e8e8;
            border: 1px solid #999;
            padding: 6px 8px;
            font-weight: bold;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #999;
            padding: 6px 8px;
            vertical-align: top;
        }
        .toc { margin-top: 1cm; }
        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .toc-list li {
            padding: 6px 0;
            border-bottom: 1px dotted #ccc;
            font-size: 11pt;
        }
        .toc-page {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }
        .no-header-footer .page-header,
        .no-header-footer .page-footer {
            display: none;
        }
        .page-content-area {
            min-height: 22cm;
        }
    </style>
</head>
<body>
@foreach ($pages as $page)
    <div class="page {{ ($page['is_cover'] || $page['is_toc']) ? 'no-header-footer' : '' }}">
        @unless ($page['is_cover'] || $page['is_toc'])
            <div class="page-header">
                <span class="left">{{ $plan->company_name }}</span>
                <span class="right">Plan upravljanja otpadom {{ $year }}</span>
            </div>
        @endunless

        <div class="page-body page-content-area">
            @if ($page['is_cover'])
                <div class="page-cover">
                    <div class="company-name">{{ $plan->company_name }}</div>
                    <div class="company-meta">
                        @if (! empty($plan->form_data['pib']))
                            PIB: {{ $plan->form_data['pib'] }} |
                            MB: {{ $plan->form_data['maticni_broj'] ?? '—' }}<br>
                        @endif
                        {{ $plan->form_data['adresa_sedista'] ?? '' }}<br>
                        {{ $plan->form_data['mesto_opstina'] ?? '' }}
                    </div>
                    <div class="doc-title">Plan upravljanja otpadom</div>
                    <div class="doc-meta">
                        Za period: {{ $plan->form_data['rok_vazenja'] ?? '—' }}<br>
                        Datum donošenja: {{ $plan->generated_at->format('d.m.Y.') }}<br>
                        Odgovorno lice: {{ $plan->form_data['odgovorno_lice'] ?? '—' }}
                    </div>
                    <div style="margin-top: 2cm; text-align: left;">
                        {!! $page['content_html'] !!}
                    </div>
                </div>
            @else
                {!! $page['content_html'] !!}
            @endif
        </div>

        @unless ($page['is_cover'] || $page['is_toc'])
            <div class="page-footer">
                <span class="left">Poverljivo – interni dokument</span>
                <span class="right">Strana {{ $page['number'] }} od 15</span>
            </div>
        @endunless
    </div>
@endforeach
</body>
</html>

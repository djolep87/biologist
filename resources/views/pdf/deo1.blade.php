<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Obrazac DEO 1 — Dnevna evidencija o otpadu</title>
    @include('pdf.partials.deo1-official-styles')
</head>
<body>

@foreach ($sekcije as $index => $sekcija)
    @include('pdf.partials.deo1-official-table', [
        'evidencija' => $sekcija['evidencija'],
        'redovi' => $sekcija['redovi'],
        'brojDanaUMesecu' => $sekcija['brojDanaUMesecu'],
        'ukupnoProizvedeno' => $sekcija['ukupnoProizvedeno'],
        'ukupnoPredato' => $sekcija['ukupnoPredato'],
        'krajnjeStanje' => $sekcija['krajnjeStanje'],
        'firma' => $firma,
        'mesecNaziv' => $mesecNaziv,
        'godina' => $godina,
        'generisano' => $generisano,
        'pageBreak' => $index > 0,
    ])
@endforeach

</body>
</html>

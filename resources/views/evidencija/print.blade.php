<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Štampa — Evidencija otpada</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 p-0 bg-gray-100">
    <div class="no-print fixed top-0 left-0 right-0 z-10 flex items-center justify-between gap-4 bg-white border-b border-gray-200 px-4 py-3 shadow-sm">
        <p class="text-sm text-gray-600">Otvorite Excel u pregledaču i koristite Štampaj (Ctrl+P / Cmd+P).</p>
        <button
            type="button"
            onclick="window.print()"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-medium hover:bg-green-700"
        >
            Štampaj
        </button>
    </div>
    <iframe
        src="{{ $exportUrl }}"
        class="w-full border-0"
        style="height: calc(100vh - 56px); margin-top: 56px;"
        title="Evidencija otpada"
    ></iframe>
    <style>
        @media print {
            .no-print { display: none !important; }
            iframe { margin-top: 0 !important; height: 100vh !important; }
        }
    </style>
</body>
</html>

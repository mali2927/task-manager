<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'STMU MIS Task Manager') : config('app.name', 'STMU MIS Task Manager') }}
</title>

<link rel="icon" href="/stmu-logo.png" type="image/png">
<link rel="shortcut icon" href="/stmu-logo.png" type="image/png">
<link rel="apple-touch-icon" href="/stmu-logo.png">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

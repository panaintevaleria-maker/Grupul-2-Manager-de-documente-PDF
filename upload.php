<?php
require_once 'functions.php';

function afiseazaEroare(array $mesaje)
{
    http_response_code(400);
?>
    <!DOCTYPE html>
    <html lang="ro">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Eroare la încărcare</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    </head>

    <body class="flex min-h-screen items-center justify-center bg-gray-100 px-4 py-6 text-gray-900">
        <main class="w-full max-w-md rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h1 class="text-xl font-bold text-red-600">Fișierele nu au putut fi încărcate</h1>
            <ul class="mt-3 list-disc pl-5 text-sm text-gray-600 space-y-1">
                <?php foreach ($mesaje as $mesaj): ?>
                    <li><?= htmlspecialchars($mesaj, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="index.php" class="mt-5 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                Înapoi la lista de fișiere
            </a>
        </main>
    </body>

    </html>
<?php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$fisiere = fisiereTrimise();

if (empty($fisiere)) {
    afiseazaEroare(["Nu a fost selectat niciun fișier."]);
}

$erori = [];
foreach ($fisiere as $f) {
    $rez = valideazaPdf($f);
    if (isset($rez['eroare'])) {
        $erori[] = $rez['eroare'];
    }
}

if (!empty($erori)) {
    afiseazaEroare($erori);
}

asiguraDocumente();

foreach ($fisiere as $f) {
    $numeCuratat = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($f['name']));
    $caleDestinatie = DOC_DIR . '/' . $numeCuratat;

    // Prevenim suprascrierea dacă există deja un fișier cu același nume
    if (file_exists($caleDestinatie)) {
        $numeCuratat = pathinfo($numeCuratat, PATHINFO_FILENAME) . '_' . time() . '.pdf';
        $caleDestinatie = DOC_DIR . '/' . $numeCuratat;
    }

    if (!move_uploaded_file($f['tmp_name'], $caleDestinatie)) {
        afiseazaEroare(["Eroare la salvarea fișierului " . $f['name']]);
    }
}

header('Location: index.php');
exit;

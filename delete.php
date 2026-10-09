<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = basename($_POST['file'] ?? '');
    $path = DOC_DIR . '/' . $file;

    if (is_file($path) && estePdf($file)) {
        if (!unlink($path)) {
            http_response_code(500);
            exit('Fișierul nu a putut fi șters.');
        }
    }

    header('Location: index.php');
    exit;
}

// Confirmare ștergere GET
$file = basename($_GET['file'] ?? '');
$path = DOC_DIR . '/' . $file;

if ($file === '' || !is_file($path) || !estePdf($file)) {
    header('Location: index.php');
    exit;
}

$numeFisier = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmă ștergerea</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>

<body class="flex min-h-screen items-center justify-center bg-gray-100 px-4 py-6 text-gray-900">
    <main class="w-full max-w-md rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold">Ștergi acest document?</h1>

        <div class="mt-4 flex items-center rounded-md bg-red-50 p-4">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-red-100 text-xs font-bold text-red-600">
                PDF
            </div>
            <p class="ml-3 break-all text-sm font-medium text-gray-700">
                <?= $numeFisier; ?>
            </p>
        </div>

        <p class="mt-3 text-sm text-gray-600">
            Ești sigur că vrei să ștergi definitiv acest document? Actiona este ireversibilă.
        </p>

        <form action="delete.php" method="post" class="mt-5 flex items-center justify-end space-x-3">
            <input type="hidden" name="file" value="<?= $numeFisier; ?>">

            <a href="index.php" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Anulează
            </a>

            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                Șterge
            </button>
        </form>
    </main>
</body>

</html>
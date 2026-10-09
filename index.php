<?php
require_once 'functions.php';

$cautare = trim($_GET['q'] ?? '');
$sortare = $_GET['sort'] ?? 'data_desc';

$documente = listaDocumente($cautare, $sortare);
?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Documente PDF</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
</head>

<body class="min-h-screen bg-gray-100 text-gray-900">
    <main class="mx-auto max-w-4xl px-4 py-6 sm:px-6">
        <header class="mb-5 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Manager Documente PDF</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Încarcă, organizează și caută fișiere PDF în folderul "documente".
                </p>
            </div>

            <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-800">
                <?= count($documente); ?> <?= count($documente) === 1 ? 'document' : 'documente'; ?>
            </span>
        </header>

        <!-- Formular de Upload -->
        <section class="mb-5 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div>
                    <label for="files" class="mb-1 block text-sm font-medium text-gray-700">
                        Încarcă documente PDF
                    </label>
                    <input
                        type="file"
                        name="files[]"
                        id="files"
                        accept="application/pdf"
                        multiple
                        required
                        class="block w-full rounded-md border border-gray-300 bg-white p-2 text-sm text-gray-700 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">
                        Puteți selecta mai multe fișiere · doar PDF · maximum 5 MB per fișier
                    </p>
                </div>
                <button
                    type="submit"
                    class="mt-3 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    Încarcă PDF
                </button>
            </form>
        </section>

        <!-- Formular Căutare și Filtre -->
        <section class="mb-5 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <form method="get" action="index.php" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="flex-1">
                    <input
                        type="text"
                        name="q"
                        value="<?= htmlspecialchars($cautare, ENT_QUOTES, 'UTF-8'); ?>"
                        placeholder="Caută după nume sau dată (ex: 12.05.2024)..."
                        class="w-full rounded-md border border-gray-300 p-2 text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <select name="sort" class="w-full rounded-md border border-gray-300 p-2 text-sm focus:border-indigo-500 focus:outline-none">
                        <option value="data_desc" <?= $sortare === 'data_desc' ? 'selected' : ''; ?>>Cele mai recente</option>
                        <option value="data_asc" <?= $sortare === 'data_asc' ? 'selected' : ''; ?>>Cele mai vechi</option>
                        <option value="nume_asc" <?= $sortare === 'nume_asc' ? 'selected' : ''; ?>>Nume (A-Z)</option>
                        <option value="nume_desc" <?= $sortare === 'nume_desc' ? 'selected' : ''; ?>>Nume (Z-A)</option>
                        <option value="marime_desc" <?= $sortare === 'marime_desc' ? 'selected' : ''; ?>>Dimensiune</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Filtrează
                    </button>
                    <?php if ($cautare !== ''): ?>
                        <a href="index.php" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">
                            Resetează
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- Lista de fișiere PDF -->
        <section aria-label="Lista documente">
            <?php if (empty($documente)): ?>
                <p class="rounded-lg border border-dashed border-gray-300 bg-white px-4 py-8 text-center text-sm text-gray-500">
                    Nu a fost găsit niciun fișier PDF.
                </p>
            <?php else: ?>
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm divide-y divide-gray-200">
                    <?php foreach ($documente as $doc): ?>
                        <?php
                        $nume = htmlspecialchars($doc['nume'], ENT_QUOTES, 'UTF-8');
                        $link = rawurlencode($doc['nume']);
                        $marimeKb = round($doc['marime'] / 1024, 1);
                        ?>
                        <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                            <div class="flex items-center min-w-0 pr-4">
                                <div class="mr-3 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-red-100 text-xs font-bold text-red-700">
                                    PDF
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900"><?= $nume; ?></p>
                                    <p class="text-xs text-gray-500">
                                        <?= $marimeKb; ?> KB · <?= $doc['data']; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 items-center space-x-2">
                                <a
                                    href="download.php?file=<?= $link; ?>"
                                    class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700">
                                    Descarcă
                                </a>
                                <a
                                    href="delete.php?file=<?= $link; ?>"
                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                                    Șterge
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>

</html>
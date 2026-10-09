<?php

const DOC_DIR = 'documente';
const MAX_SIZE = 5 * 1024 * 1024; // 5 MB

function estePdf(string $fisier): bool
{
    return strtolower(pathinfo($fisier, PATHINFO_EXTENSION)) === 'pdf';
}

function asiguraDocumente(): void
{
    if (!is_dir(DOC_DIR)) {
        mkdir(DOC_DIR, 0755, true);
    }
}

function listaDocumente(string $cautare = '', string $sortare = 'data_desc'): array
{
    asiguraDocumente();

    $fisiere = [];
    foreach (scandir(DOC_DIR) as $element) {
        $cale = DOC_DIR . '/' . $element;
        if (is_file($cale) && estePdf($element)) {
            $mtime = filemtime($cale);
            $size = filesize($cale);
            $dataFormatata = date('d.m.Y H:i', $mtime);

            // Filtrare după căutare (nume sau dată)
            if ($cautare !== '') {
                $termen = mb_strtolower($cautare);
                $numeGásit = str_contains(mb_strtolower($element), $termen);
                $dataGasita = str_contains($dataFormatata, $termen);

                if (!$numeGásit && !$dataGasita) {
                    continue;
                }
            }

            $fisiere[] = [
                'nume' => $element,
                'cale' => $cale,
                'marime' => $size,
                'mtime' => $mtime,
                'data' => $dataFormatata
            ];
        }
    }

    // Sortare
    usort($fisiere, function ($a, $b) use ($sortare) {
        switch ($sortare) {
            case 'nume_asc':
                return strnatcasecmp($a['nume'], $b['nume']);
            case 'nume_desc':
                return strnatcasecmp($b['nume'], $a['nume']);
            case 'data_asc':
                return $a['mtime'] <=> $b['mtime'];
            case 'marime_desc':
                return $b['marime'] <=> $a['marime'];
            case 'data_desc':
            default:
                return $b['mtime'] <=> $a['mtime'];
        }
    });

    return $fisiere;
}

function fisiereTrimise(): array
{
    if (!isset($_FILES['files']) || !is_array($_FILES['files']['name'])) {
        return [];
    }

    $fisiere = [];
    foreach ($_FILES['files']['name'] as $i => $nume) {
        if ($_FILES['files']['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $fisiere[] = [
            'name'     => $nume,
            'size'     => $_FILES['files']['size'][$i],
            'tmp_name' => $_FILES['files']['tmp_name'][$i],
            'error'    => $_FILES['files']['error'][$i],
        ];
    }

    return $fisiere;
}

function valideazaPdf(array $f): array
{
    $nume = $f['name'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        return ['eroare' => "$nume: a apărut o eroare la încărcare."];
    }

    if (!estePdf($nume)) {
        return ['eroare' => "$nume: acceptăm doar fișiere PDF."];
    }

    if ($f['size'] > MAX_SIZE) {
        return ['eroare' => "$nume: depășește dimensiunea maximă admisă de 5 MB."];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tip = finfo_file($finfo, $f['tmp_name']);
    finfo_close($finfo);

    if ($tip !== 'application/pdf') {
        return ['eroare' => "$nume: fișierul nu este un PDF valid."];
    }

    return ['ok' => true];
}

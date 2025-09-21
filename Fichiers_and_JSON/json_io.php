<?php
declare(strict_types=1);

// Bdit b strict typing bash ntaakedo type dyal l data
// Hna kanqolo l PHP mayqblsh conversion awtomatiki

//! function tqra JSON mn fichier w trj3 array 
function loadJson(string $path): array {
    // Kanqraw l fichier, @ bash nshriw l errors
    $raw = @file_get_contents($path);
    if ($raw === false) {
        // Ida ma lqinash l fichier wla ma nqdrush nqraw, nrmiw exception
        throw new RuntimeException("Fichier introuvable ola ma nqdrush nqraw: $path");
    }
    try {
        // Kan7awlo JSON l array associatif, true bash nakhdo array w JSON_THROW_ON_ERROR bash nrmiw exception ida JSON ghalet
        /** @var array $data */
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return $data;
    } catch (JsonException $e) {
        // Ida kan JSON ghalet, nrmiw exception jdid b sabab l qdim
        throw new RuntimeException("JSON ghalet f $path", previous: $e);
    }
}

/** Dala tkteb array f fichier JSON */
function saveJson(string $path, array $data): void {
    // Kanakhdo l dossier mn l path w nshoufo wash mawjod
    $dir = dirname($path);
    if (!is_dir($dir)) {
        // Ida l dossier ma mawjodsh, nkhliwh b recursive true
        mkdir($dir, 0777, true);
    }

    try {
        // Kan7awlo l array l JSON b options bash ykon mrttab w ydmm l harf l khas
        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
        );
        // Ida l encode fashl, nrmiw exception
        if ($json === false) {
            throw new RuntimeException("Fashl f encodage JSON (rja3 false).");
        }
    } catch (Throwable $e) {
        // Ida kan chi error f l encodage, nrmiw exception
        throw new RuntimeException("Ma nqdrush n7awlo l JSON", previous: $e);
    }

    // Kankteb l JSON f fichier b LOCK_EX bash ntamnu ma yktebsh chi 7ad akhor f nafs l waqt
    $ok = @file_put_contents($path, $json . PHP_EOL, LOCK_EX);
    if ($ok === false) {
        // Ida ma nqdrush nkteb, nrmiw exception
        throw new RuntimeException("Ma nqdrush nkteb f: $path");
    }
}

/** Dala tkwwn string l slug (ex: "Salam 3likom" -> "salam-3likom") */
function slugify(string $value): string {
    // Kan7awlo l string l lowercase
    $s = strtolower($value);
    // Kanbdlo ay haja ma hiyash harf wla raqm b "-"
    $s = preg_replace('/[^a-z0-9]+/i', '-', $s) ?? '';
    // Kanqta3o l "-" mn bdaya w l akhir
    return trim($s, '-');
}

/** Data dyal l mkalat bash nktebhom f JSON */
$articles = [
    [
        'id'      => 1,
        'title'   => 'Fichiers & JSON m3a PHP',
        'slug'    => slugify('Fichiers & JSON m3a PHP'), // Kan7awlo title l slug
        'excerpt' => 'Qra w kteb l fichiers, encode w decode JSON b salama.',
        'tags'    => ['php', 'json'],
    ],
    [
        'id'      => 2,
        'title'   => 'T7dir seed dyal l mkalat',
        'slug'    => slugify('T7dir seed dyal l mkalat'),
        'excerpt' => 'Bni articles.seed.json l ynfa3 m3a Laravel.',
        'tags'    => ['seed', 'laravel'],
    ],
];

// Path dyal l fichier JSON l ghadi nkhtab fih
$seedPath = __DIR__ . '/storage/seeds/articles.seed.json';

try {
    // 1) Kankteb l seed f l fichier
    saveJson($seedPath, $articles);
    echo "[OK] Seed ktbna f: $seedPath" . PHP_EOL;

    // 2) Kanqraw l fichier w nt2akedo mn nta2ij
    $loaded = loadJson($seedPath);
    echo "[OK] Qrina: " . count($loaded) . " mkala(t)." . PHP_EOL;

    // 3) Kanprintiw l title dyal l mkala l wla
    echo "Title l wla: " . ($loaded[0]['title'] ?? 'N/A') . PHP_EOL;

    // Ida kolshi mrigel, nkhrjo b exit 0 (success)
    exit(0);
} catch (Throwable $e) {
    // Ida kan chi error, kanprintiw l message dyal l error
    fwrite(STDERR, "[ERR] " . $e->getMessage() . PHP_EOL);
    if ($e->getPrevious()) {
        // Kanprintiw l cause dyal l error ida mawjod
        fwrite(STDERR, "Cause: " . get_class($e->getPrevious()) . " — " . $e->getPrevious()->getMessage() . PHP_EOL);
    }
    // Nkhrjo b exit 1 (error)
    exit(1);
}
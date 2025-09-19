#!/usr/bin/env php
<?php
declare(strict_types=1);

// Objectif: Script bach nqraw CSV mn --input (mlf ola STDIN), nbdlo l JSON mnadam, o nktbo f STDOUT.

// Kods d khroj bach nchro l erreurs
const EXIT_OK = 0; // Kolchi mzyan
const EXIT_USAGE = 2; // Khata f l usage (mthln bla --input)
const EXIT_DATA_ERROR = 3; // Khata f data (mthln mlf ma kaynch)

// Fonction usage(): Tkhrj l aide f STDOUT
function usage(): void {
    $msg = <<<TXT
Seed Generator — Khiyarat:
    --input=PATH    Triq l mlf CSV ola '-' bch STDIN (wajib)
    --limit[=N]     Had nbre d articles l traitement (ikhtiyari)
    --published-only  Ghir l articles l menshourin
    -v              Mode verbeux (ywerri tfsiyat)
    --help          Ywerri hada l aide

    Mithal:
    php bin/seed_generator.php --input=/tmp/articles.csv --published-only --limit=2
    cat data.csv | php bin/seed_generator.php --input=-
TXT;
    fwrite(STDOUT, $msg . PHP_EOL);
}

// Fonction readCsvFrom(): Tqra CSV mn mlf ola STDIN
function readCsvFrom(string $input): array {
    // Ila input = '-', nqraw mn STDIN, sinon mn mlf
    $fh = ($input === '-') ? STDIN : @fopen($input, 'r');
    if ($fh === false) {
        fwrite(STDERR, "Khata: Ma nqdrsh n7lo l input '$input'\n");
        exit(EXIT_DATA_ERROR);
    }

    // Nqraw l header (a3mida d tableau)
    $header = fgetcsv($fh);
    if ($header === false || empty($header)) {
        fwrite(STDERR, "Khata: CSV khawi ola header machi mzyan\n");
        if ($fh !== STDIN) fclose($fh);
        exit(EXIT_DATA_ERROR);
    }

    // Nqraw l swarf o nkhzn f rows
    $rows = [];
    while (($line = fgetcsv($fh)) !== false) {
        if (count($line) === count($header)) {
            $rows[] = array_combine($header, $line);
        }
    }
    if ($fh !== STDIN) fclose($fh);

    if (empty($rows)) {
        fwrite(STDERR, "Khata: Ma lqinash data f CSV\n");
        exit(EXIT_DATA_ERROR);
    }

    return $rows;
}

// Fonction normalizeRow(): Tnaddm data d kol star
function normalizeRow(array $row): array {
    return [
        'title' => trim((string)($row['title'] ?? 'Bla smiya')), // Nnaddfo smiya
        'excerpt' => ($row['excerpt'] ?? null) !== '' ? (string)$row['excerpt'] : null, // Ila excerpt khawi, null
        'views' => (int)($row['views'] ?? 0), // Views -> nbre sahih
        'published' => in_array(strtolower((string)($row['published'] ?? 'true')), ['1', 'true', 'yes', 'y', 'on'], true), // Published -> bool
        'author' => (string)($row['author'] ?? 'N/A'), // Ila bla kateb, N/A
    ];
}

// Programme principal
$opts = getopt('v', ['input:', 'published-only', 'limit::', 'help']);

// Ila --help, nwrri l aide o nkhrjo
if (array_key_exists('help', $opts)) {
    usage();
    exit(EXIT_OK);
}

// Nchk iwsh --input wajib
$input = $opts['input'] ?? null;
if ($input === null) {
    fwrite(STDERR, "Khata: --input wajib (triq ola '-')\n\n");
    usage();
    exit(EXIT_USAGE);
}

// Nqraw bqa d khiyarat
$limit = isset($opts['limit']) ? max(1, (int)$opts['limit']) : null;
$publishedOnly = array_key_exists('published-only', $opts);
$verbose = array_key_exists('v', $opts);

// Ila verbose, nwrri tfsiyat
if ($verbose) {
    fwrite(STDOUT, "[Verbose] Nqraw mn " . ($input === '-' ? 'STDIN' : $input) . PHP_EOL);
}

// Nqraw CSV o nbdlo l data
try {
    $rows = readCsvFrom($input);
    $items = array_map('normalizeRow', $rows);
} catch (Throwable $e) {
    fwrite(STDERR, "Khata f CSV: " . $e->getMessage() . PHP_EOL);
    exit(EXIT_DATA_ERROR);
}

// Ila --published-only, nkhlijo ghir l menshourin
if ($publishedOnly) {
    $items = array_values(array_filter($items, fn($a) => $a['published']));
}

// Ila --limit, nhaddo nbre d swarf
if ($limit !== null) {
    $items = array_slice($items, 0, $limit);
}

// Nktbo JSON f STDOUT
try {
    echo json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, "Khata f JSON encode: " . $e->getMessage() . PHP_EOL);
    exit(EXIT_DATA_ERROR);
}

// Nkhrjo b nja7
exit(EXIT_OK);
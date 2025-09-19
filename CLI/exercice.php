<?php
declare(strict_types=1);


// Ta3rif d-thawabit bash n-nadmo l-exit codes
const EXIT_OK = 0; // Njah
const EXIT_USAGE = 2; // Kht2a f-l-usage (mthln: --input nqis)
const EXIT_DATA_ERROR = 3; // Kht2a f-l-data (mthln: file ma kaynsh)

// Fonction usage(): Tktb l-mos3ada (help) f-STDOUT
function usage(): void {
    $msg = <<<TXT
Seed Generator — Khiyarat:
    --input=PATH    Chemin d-l-file CSV aw '-' bash STDIN (mtlob)
    --limit[=N]     7dd 3adad l-articles lli ytsjlo (ikhtiyari)
    --published-only  Ma tkhdm ghyr 3la l-articles l-mnshurin
    -v              Mode verbose (yuri tafa9il akthar)
    --help          Yktb had l-mos3ada

    Mthal:
    php bin/seed_generator.php --input=/tmp/articles.csv --published-only --limit=2
    cat data.csv | php bin/seed_generator.php --input=-
TXT;
    fwrite(STDOUT, $msg . PHP_EOL);
}

// Fonction readCsvFrom(): Tqra l-CSV mn file aw STDIN
function readCsvFrom(string $input): array {
    // Ila l-input = '-', nqraw mn STDIN, sinon mn file
    $fh = ($input === '-') ? STDIN : @fopen($input, 'r');
    if ($fh === false) {
        fwrite(STDERR, "Kht2a: Ma nqdrush nfthu l-input '$input'\n");
        exit(EXIT_DATA_ERROR);
    }

    // Qra l-header (l-3nawin d-l-columns)
    $header = fgetcsv($fh);
    if ($header === false || empty($header)) {
        fwrite(STDERR, "Kht2a: CSV khawi aw header ghalt\n");
        if ($fh !== STDIN) fclose($fh);
        exit(EXIT_DATA_ERROR);
    }

    // Qra l-linjat w khzn f-rows
    $rows = [];
    while (($line = fgetcsv($fh)) !== false) {
        if (count($line) === count($header)) { // T2akd mn 3adad l-columns
            $rows[] = array_combine($header, $line);
        }
    }
    if ($fh !== STDIN) fclose($fh);

    if (empty($rows)) {
        fwrite(STDERR, "Kht2a: Ma lqinash data f-l-CSV\n");
        exit(EXIT_DATA_ERROR);
    }

    return $rows;
}

// Fonction normalizeRow(): Tnadm l-data d-kul ligne
function normalizeRow(array $row): array {
    return [
        'title' => trim((string)($row['title'] ?? 'Bla smiya')), // Nadi l-title
        'excerpt' => ($row['excerpt'] ?? null) !== '' ? (string)$row['excerpt'] : null, // Excerpt ila kayn
        'views' => (int)($row['views'] ?? 0), // Views -> int
        'published' => in_array(strtolower((string)($row['published'] ?? 'true')), ['1', 'true', 'yes', 'y', 'on'], true), // Published -> bool
        'author' => (string)($row['author'] ?? 'N/A'), // Author ila ma kaynsh -> N/A
    ];
}

// Main program
$opts = getopt('v', ['input:', 'published-only', 'limit::', 'help']);

// Ila --help, ktb l-mos3ada w khrj
if (array_key_exists('help', $opts)) {
    usage();
    exit(EXIT_OK);
}

// T2akd mn --input
$input = $opts['input'] ?? null;
if ($input === null) {
    fwrite(STDERR, "Kht2a: --input khas ikun mwjod (chemin aw '-')\n\n");
    usage();
    exit(EXIT_USAGE);
}

// Qra l-khiyarat l-baqin
$limit = isset($opts['limit']) ? max(1, (int)$opts['limit']) : null;
$publishedOnly = array_key_exists('published-only', $opts);
$verbose = array_key_exists('v', $opts);

// Ila verbose, ktb tafa9il
if ($verbose) {
    fwrite(STDOUT, "[Verbose] Nqraw mn " . ($input === '-' ? 'STDIN' : $input) . PHP_EOL);
}

// Qra l-CSV w n7awlo l-data
try {
    $rows = readCsvFrom($input);
    $items = array_map('normalizeRow', $rows);
} catch (Throwable $e) {
    fwrite(STDERR, "Kht2a f-l-CSV: " . $e->getMessage() . PHP_EOL);
    exit(EXIT_DATA_ERROR);
}

// Ila --published-only, sift ghyr l-mnshurin
if ($publishedOnly) {
    $items = array_values(array_filter($items, fn($a) => $a['published']));
}

// Ila --limit, 7dd 3adad l-linjat
if ($limit !== null) {
    $items = array_slice($items, 0, $limit);
}

// Ktb l-JSON f-STDOUT
try {
    echo json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, "Kht2a f-l-JSON encode: " . $e->getMessage() . PHP_EOL);
    exit(EXIT_DATA_ERROR);
}

// Khrj b-njah
exit(EXIT_OK);
?>

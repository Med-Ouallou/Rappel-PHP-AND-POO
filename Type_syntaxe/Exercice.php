<?php

function buildArticle(array $row): array {
    $row['title']     ??= 'Sans titre';
    $row['author']    ??= 'N/A';
    $row['published'] ??= true; 

    $title   = trim((string)$row['title']);
    $excerpt = isset($row['excerpt']) ? trim((string)$row['excerpt']) : null;
    $excerpt = ($excerpt === '') ? null : $excerpt;

    $views   = (int)($row['views'] ?? 0);
    $views   = max(0, $views);


    return [
        'title'     => $title,
        'excerpt'   => $excerpt,
        'views'     => $views,
        'published' => (bool)$row['published'],
        'author'    => trim((string)$row['author']),
    ];
}

//! function test

$tests = [

    //? Exemple 1 (tous les champs renseignés)
    [
        'title'   => 'PHP 8 en pratique',
        'excerpt' => '',
        'views'   => '300',
        'author'  => 'Yassine'
    ],

    //? Exemple 2 (titre vide)
    [
        'title'   => '',
        'excerpt' => '',
        'views'   => '',
        'author'  => ''
    ],

    //? Exemple 3 (tous les champs vide)
    [
        'title'   => '   Hello   ',
        'excerpt' => 'Résumé...',
        'views'   => '-5',
        'author'  => '   Mohamed   ',
        'published' => false
    ],

    //? Exemple 4 (excerpt vide)
    [
        'title'   => 'Laravel Framework',
        'views'   => '1200',
        'author'  => 'Yassin'
    ],

    //? Exemple 5 (tous les champs non renseignés)
    []
];


foreach ($tests as $i => $test) {
    echo "Test ----> " . ($i+1) . "\n";
    $result = buildArticle($test);
    print_r($result);
    echo "------------------\n\n";
}
























?>
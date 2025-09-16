<?php

// to work with structered data
declare(strict_types=1);  

$input = [
    'title'     => 'PHP 8 en pratique',
    'excerpt'   => '',
    'views'     => '300',
    // 'published' absent
    'author'    => 'Yassine'
];

//! the function take one parameter that coulde be null and return a string or null : ?string
function strOrNull(?string $s): ?string {
    $s = $s !== null ? trim($s) : null;
    return $s === '' ? null : $s;
}

//! this function take 1 varibale but its coulde be int or string or null and return an int or 0
function intOrZero(int|string|null $v): int {
    return max(0, (int)($v ?? 0));
}
//!-----------------|-------------(int)


$normalized = [
    'title'     => trim((string)($input['title'] ?? 'Sans titre')),
    'excerpt'   => strOrNull($input['excerpt'] ?? null),
    'views'     => intOrZero($input['views'] ?? null),
    'published' => $input['published'] ?? true, // défaut si non défini
    'author'    => trim((string)($input['author'] ?? 'N/A')),
    ];

    print_r($normalized);
/*
    Array
    (
    [title] => PHP 8 en pratique
    [excerpt] =>
    [views] => 300
    [published] => 1
    [author] => Yassine
)
*/

$defaults = [
    'per_page' => 10,
    'sort'     => 'created_desc',
];

$userQuery = ['per_page' => null]; // simulateur d'entrée
$userQuery['per_page'] ??= $defaults['per_page']; // 10
$userQuery['sort']     ??= $defaults['sort'];     // 'created_desc'














?>
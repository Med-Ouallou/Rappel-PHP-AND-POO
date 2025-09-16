<?php
declare(strict_types=1);

$age   = 21;           // int
$prix  = 19.99;        // float
$nom   = "Amina";      // string
$actif = true;         // bool
$vide  = null;         // null

// echo("hello world");

//? Tableaux associatifs

$article = [
    'title'     => 'Intro Laravel',
    'excerpt'   => null,
    'views'     => 120,
    'published' => true,
];

$article['author'] = 'Amina';   // ajout
$hasViews = array_key_exists('views', $article); // true même si null
echo($hasViews);

// ?? vs ?: (très différent

$val = 0;

// if the value is null or not defind output=> new value(42)
$a = $val ?? 42;   // => 0

// opérateur ternaire raccourci (truthy/falsy)
$b = $val ?: 42;   // => 42 (car 0 est falsy)

echo($a);



















?>
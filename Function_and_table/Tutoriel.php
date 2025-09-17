<?php

declare(strict_types=1);

$tax = 0.2;

//! Closure work with use to get the variable from the globale scope
//!--------------------------|---------------------------------------
$ttc = function (float $ht) use ($tax): float {
    return $ht * (1 + $tax);
};

//! Arrow function late you get the variable from the global scope  auto
//!--------------------------|---------------------------------------
$ttc2 = fn(float $ht): float => $ht * (1 + $tax);


//? =========================== Tutoriel guid ==================================

$articles = [
    ['id'=>1,'title'=>'Intro Laravel','category'=>'php','views'=>120,'author'=>'Amina','published'=>true,  'tags'=>['php','laravel']],
    ['id'=>2,'title'=>'PHP 8 en pratique','category'=>'php','views'=>300,'author'=>'Yassine','published'=>true,  'tags'=>['php']],
    ['id'=>3,'title'=>'Composer & Autoload','category'=>'outils','views'=>90,'author'=>'Amina','published'=>false, 'tags'=>['composer','php']],
    ['id'=>4,'title'=>'Validation FormRequest','category'=>'laravel','views'=>210,'author'=>'Sara','published'=>true,  'tags'=>['laravel','validation']],
];

//! strtolower return the title with lowercase
//! preg_replace katbadal ai character machi dimn a-z and 0-9 w katbadal ai space b -
function slugify(string $title): string {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-');
}

// foreach($articles as $article){
//     echo $article['title'] . " --> " . slugify($article['title']) . PHP_EOL;
// }



//! array_values katraja3 array jdida mratba mn index[0] -> index[n]
//! array_filter katraja3 array jdida binaa 3la condition explain :
//! ratchof kola article ida kan l key true ratkhalih kan false ratna9az 3lih donc ratraja3 array fiha ra published li true

$published = array_values(
    array_filter($articles, fn(array $a) => $a['published'] ?? false)
);
// print_r($published);

//! sta3malna array_map hit bighina light array mn lmother array but mabghinach ga3 l kay (id,title,slug,views)
$light = array_map(
    fn(array $a) => [
        'id'    => $a['id'],
        'title' => $a['title'],
        'slug'  => slugify($a['title']),
        'views' => $a['views'],
    ],
    $published
);
// print_r($light);

//! bach ndiro sort l top viewed article ransta3mlo usrot() 
//! kan9arno bin $a and $b bsti3mal <=> ida explain : 
//! ida kan $b > $a result ----> 1 kati hia lowla 
//! ida kan $b = $a result ----> 0 kaib9aw hda ba3tom
//! ida kan $b < $a result ----> -1 katji mora $a

$top = $light;
usort($top, fn($a, $b) => $b['views'] <=> $a['views']);
$top3 = array_slice($top, 0, 3);

//! 
$byAuthor = array_reduce(
    $published,
    function(array $acc, array $a): array {
        $author = $a['author'];
        $acc[$author] = ($acc[$author] ?? 0) + 1;
        return $acc;
    },
    []
);

//! 1-step : kanjibo ga3 tags dial kola article published [ ['php','laravel'] , ['php'] , ['laravel','validation'] ]
//! 2-step : kanmargiw ga3 larrays f one array bsti3mal array_merge ['php','laravel','php','laravel','validation']
//! 3-step : kanhsbo lcounter dial kola tag 
$allTags = array_merge(...array_map(fn($a) => $a['tags'], $published));

$tagFreq = array_reduce(
    $allTags,
    function(array $acc, string $tag): array {
        $acc[$tag] = ($acc[$tag] ?? 0) + 1;
        return $acc;
    },
    []
);

// print_r($tagFreq);


echo "Top 3 (views):\n";
foreach ($top3 as $a) {
    echo "- {$a['title']} ({$a['views']} vues) — {$a['slug']}\n";
    }

    echo "\nPar auteur:\n";
    foreach ($byAuthor as $author => $count) {
    echo "- $author: $count article(s)\n";
    }

    echo "\nTags:\n";
    foreach ($tagFreq as $tag => $count) {
    echo "- $tag: $count\n";
}










?>
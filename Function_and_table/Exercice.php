<?php

declare(strict_types=1);

$articles = [
    ['id'=>1,'slug'=>'intro-laravel','views'=>120,'author'=>'Amina','category'=>'php', 'published' => true],
    ['id'=>2,'slug'=>'php-8-nouveautes','views'=>300,'author'=>'Yassine','category'=>'php' , 'published' => true],
    ['id'=>3,'slug'=>'css-grid-guide','views'=>180,'author'=>'Mehdy','category'=>'css' , 'published' => false],
    ['id'=>4,'slug'=>'javascript-promises','views'=>250,'author'=>'Sara','category'=>'javascript' , 'published' => true],
];

function slugify(string $slug): string {
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-');
}

$published = array_values(
    array_filter($articles, fn(array $a) => $a['published'] ?? false)
);

$normalized = array_map(
    fn($a) => [
        'id'       => $a['id'],
        'slug'     => slugify($a['slug']),
        'views'    => $a['views'],
        'author'   => $a['author'],
        'category' => $a['category'],
    ],
    $published
);

usort($normalized, fn($a, $b) => $b['views'] <=> $a['views']);

$summary = array_reduce(
    $published,
    function(array $acc, array $a): array {
        $acc['count']      = ($acc['count'] ?? 0) + 1;
        $acc['views_sum']  = ($acc['views_sum'] ?? 0) + $a['views'];
        $cat = $a['category'];
        $acc['by_category'][$cat] = ($acc['by_category'][$cat] ?? 0) + 1;
        return $acc;
    },
    ['count'=>0, 'views_sum'=>0, 'by_category'=>[]]
);


print_r($normalized);
print_r($summary);

?>
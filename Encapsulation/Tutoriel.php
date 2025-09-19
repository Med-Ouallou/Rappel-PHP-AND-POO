<?php
declare(strict_types=1);

// Hada l-class dial Article: kanstokiw fih les données dial lmaqala (ID, title, slug, tags)
class Article {
    // id: readonly, ya3ni kansnaw lih ghi marra whda f constructor w ma nbdlwhch ba3d
    public readonly int $id;        
    // title, slug, tags: private, ya3ni ma nqdroch nwslo lhom direct, ghi b getters w setters
    private string $title;            
    private string $slug;             
    private array $tags = [];         

    // count: static, ya3ni mchrk bin kol les instances dial l-class, kanshfo bih chukayn lmaqalat
    private static int $count = 0;    

    // Constructor: kancreaw article jdida b ID, title, w tags (tags optional)
    public function __construct(int $id, string $title, array $tags = []) {
        // Kan-checkiw id ila kbir mn 0, sinon kanrj3o error
        if ($id <= 0) throw new InvalidArgumentException("id > 0 requis.");
        $this->id = $id; // kansnaw l-id (ma ymknch nbdlwh ba3d)
        $this->setTitle($title); // kanst3mlo setter bash n-checkiw title
        $this->tags = $tags; // kansnaw tags
        self::$count++; // kanzido l-count b 1
    }

    // fromTitle: static method, kanshriw biha article jdida mn ID w title bshkl sari3
    public static function fromTitle(int $id, string $title): static {
        // static:: katkhli l-method trj3 instance dial l-class lli nst3mlna (Article wla FeaturedArticle)
        return new static($id, $title);
    }

    // Getters: API sghira w safe bash nrj3o l-ma3lomat
    public function title(): string { return $this->title; } // kanrj3o title
    public function slug(): string { return $this->slug; } // kanrj3o slug
    public function tags(): array { return $this->tags; } // kanrj3o tags

    // Setter: kanbdlo title w kan-checkiw ila ma khawich, w kan-updateiw slug
    public function setTitle(string $title): void {
        $title = trim($title); // kanzilo l-spaces
        if ($title === '') throw new InvalidArgumentException("Titre requis.");
        $this->title = $title;
        $this->slug = static::slugify($title); // kan-updateiw slug b slugify
    }

    // addTag: kanzido tag jdid, kan-checkiw ila ma khawich
    public function addTag(string $tag): void {
        $t = trim($tag);
        if ($t === '') throw new InvalidArgumentException("Tag vide.");
        $this->tags[] = $t;
    }

    // count: static method, kanrj3o chukayn lmaqalat lli tcreaw
    public static function count(): int { return self::$count; }

    // slugify: method protected, kan7wlo string l slug (hrf sghar w "-" binathom)
    protected static function slugify(string $value): string {
        $s = strtolower($value); // kan7wlo l-kol l hrf sghar
        $s = preg_replace('/[^a-z0-9]+/i', '-', $s) ?? ''; // kanbdlo ay haja msh a-z wla 0-9 b "-"
        return trim($s, '-'); // kanzilo "-" mn l-bdiya w l-nihaya
    }
}

// FeaturedArticle: sub-class, kanwrto mn Article w kan3dlo slugify
class FeaturedArticle extends Article {
    // kan3dlo slugify bash nzido "featured-" f l-bdiya
    protected static function slugify(string $value): string {
        return 'featured-' . parent::slugify($value);
    }
}

// Demo: kantastiw l-code
$a = Article::fromTitle(1, 'Encapsulation & visibilité en PHP'); // kancreaw article
$b = FeaturedArticle::fromTitle(2, 'Lire moins, comprendre plus'); // kancreaw featured article
$b->addTag('best'); // kanzido tag

echo $a->slug() . PHP_EOL; // "encapsulation-visibilite-en-php"
echo $b->slug() . PHP_EOL; // "featured-lire-moins-comprendre-plus"
echo Article::count() . PHP_EOL; // 2 (chukayn lmaqalat lli tcreaw)
?>
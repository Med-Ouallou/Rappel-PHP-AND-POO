<?php

declare(strict_types=1);


$authors = [
    [
        'id' => 1,
        'name' => 'Mohamed Ouallou',
        'email' => 'mohamed@example.com',
        'bio' => 'Full-stack web developer from Tangier',
        'articlesCount' => 12,
    ],
    [
        'id' => 2,
        'name' => 'Amina Benali',
        'email' => 'amina@example.com',
        'bio' => 'PHP & Laravel enthusiast',
        'articlesCount' => 5,
    ],
    [
        'id' => 3,
        'name' => 'Youssef El Idrissi',
        'email' => 'youssef@example.com',
        'bio' => null,
        'articlesCount' => 0,
    ],
];


class User{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $bio = null,
        public int $articlesCount = 0,
    ) {}

    public function initials(): string {
        //! kat9asam name ljoj ajzaa mn space o kt3mlom f array
        $part = preg_split('/\s+/', trim($this->name));
        //! katjib lharf 1 mn kola part w karado majuscule
        $letters = array_map(
            fn($p) => mb_strtoupper(mb_substr($p, 0, 1)),
            $part
        );
        //! kaijma3 lhorof bla fasila(,)
        return implode('', $letters);
    }

    public function toArray(): array {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'bio'           => $this->bio,
            'articlesCount' => $this->articlesCount,
            'initials'      => $this->initials(),
        ];
    }

}

class UserFactory {
    public static function fromArray(array $u): User {
        $id    = max(1, (int)($u['id'] ?? 0));
        $name  = trim((string)($u['name'] ?? 'Inconnu'));
        $email = trim((string)($u['email'] ?? ''));
        if ($email === '') {
            throw new InvalidArgumentException('email requis');
        }
        $bio   = isset($u['bio']) ? (string)$u['bio'] : null;
        $count = (int)($u['articlesCount'] ?? 0);

        return new User($id, $name, $email, $bio, $count);
    }
}


$users = array_map(fn($u) => UserFactory::fromArray($u), $authors);

foreach ($users as $user) {
    echo "- {$user->name} ({$user->initials()}) — Articles: {$user->articlesCount}\n";
}

?>
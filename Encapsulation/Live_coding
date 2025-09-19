<?php
declare(strict_types=1);

class User {
  private string $name;          // caché au monde extérieur
  protected array $roles = [];   // visible par les sous-classes
  public function __construct(string $name) { $this->setName($name); }

  public function name(): string { return $this->name; }

  public function setName(string $name): void {
    $name = trim($name);
    if ($name === '') throw new InvalidArgumentException("Nom requis.");
    $this->name = $name;
  }

  public function addRole(string $role): void {
    if ($role === '') throw new InvalidArgumentException("Role vide.");
    $this->roles[] = $role;
  }

  public function roles(): array { return $this->roles; }
}

$u = new User('Amina');
$u->addRole('author');
// $u->name = 'X';         // ❌ Erreur : propriété privée
// echo $u->roles[0];      // ❌ Erreur : propriété protégée
echo $u->name();           // ✅ OK via API publique
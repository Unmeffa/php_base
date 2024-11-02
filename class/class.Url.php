<?php

class Url
{
    protected ?int $id = null;
    protected string $slug;
    protected string $locale;
    protected string $type;
    protected $createdAt;
    protected $updatedAt = null;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->slug = isset($data['slug']) ? (string) $data['slug'] : '';
        $this->locale = $data['locale'] ?? 'fr';
        $this->type = $data['type'] ?? 'page';
        $this->createdAt = $data['createdAt'] ?? new DateTime();
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    public function set($name, $value)
    {
        if ($name === 'slug') {
            if (!is_string($value)) {
                throw new TypeError("Expected string for slug, got " . gettype($value));
            }
        }

        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            throw new Exception("Propriété '$name' non définie.");
        }
    }

    // Méthode get pour accéder dynamiquement aux propriétés
    public function get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new Exception("Propriété '$name' non définie.");
        }
    }

    public function __toString(): string
    {
        return $this->slug;
    }

    public function create(): self
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        // Générer automatiquement les dates
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();

        $query = "INSERT INTO " . $tablePrefix . "_url (slug, locale, type, createdAt, updatedAt) 
                  VALUES (:slug, :locale, :type, :createdAt, :updatedAt)";
        $stmt = $pdo->prepare($query);

        $stmt->bindValue(':slug', $this->slug);
        $stmt->bindValue(':locale', $this->locale);
        $stmt->bindValue(':type', $this->type);
        $stmt->bindValue(':createdAt', $this->createdAt->format('Y-m-d H:i:s'));
        $stmt->bindValue(':updatedAt', $this->updatedAt->format('Y-m-d H:i:s'));

        if ($stmt->execute()) {
            $this->id = $pdo->lastInsertId();
        }

        return $this;
    }

    public function update(): self
    {
        if (!$this->id) {
            throw new Exception("Impossible de mettre à jour : l'ID est manquant.");
        }

        $this->updatedAt = new DateTime();

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "UPDATE " . $tablePrefix . "_url 
                  SET slug = :slug, locale = :locale, type = :type, updatedAt = :updatedAt 
                  WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':slug', $this->slug);
        $stmt->bindValue(':locale', $this->locale);
        $stmt->bindValue(':type', $this->type);
        $stmt->bindValue(':updatedAt', $this->updatedAt->format('Y-m-d H:i:s'));
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();

        return $this;
    }

    public function delete(): bool
    {
        if (!$this->id) {
            throw new Exception("Impossible de supprimer : l'ID est manquant.");
        }

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "DELETE FROM " . $tablePrefix . "_url WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function handleEntity(array $data): self
    {
        $url = new static($data);
        if (isset($data['id']) && $data['id'] > 0) {
            return $url->update();
        } else {
            return $url->create();
        }
    }

    public static function slugify(string $name): string
    {
        // Fonction pour générer un slug à partir d'un nom
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        return $slug;
    }

    public static function isSlugUnique(string $slug): bool
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT COUNT(*) FROM " . $tablePrefix . "_url WHERE slug = :slug";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() == 0;
    }

    public static function getBySlugAndLocale(string $slug, string $locale): ?self
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT * FROM " . $tablePrefix . "_url WHERE slug = :slug AND locale = :locale LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':locale', $locale, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new self($result) : null;
    }

    public static function getById(int $id): ?self
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT * FROM " . $tablePrefix . "_url WHERE id = :id LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new self($result); // Crée et retourne une nouvelle instance de Url avec les données trouvées
        }

        return null; // Retourne null si aucun enregistrement n'est trouvé
    }
}

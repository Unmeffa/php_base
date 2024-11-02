<?php
require_once(__DIR__ . '/abstract/class.AbstractDetails.php');

class PhotoDetail extends AbstractDetails
{

    protected ?int $photoId;
    protected string $name;
    protected string $description;
    protected string $alt;

    public function __construct(array $tab)
    {
        parent::__construct($tab);
        $this->photoId = $tab['photoId'];
        $this->name = $tab['name'];
        $this->description = $tab['description'];
        $this->alt = $tab['alt'];
    }

    public static function getPhotoDetail(int $id): ?self
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT * FROM " . $tablePrefix . "_photodetail WHERE id = :id LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new self($result) : null;
    }

    public function create(): self
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "INSERT INTO " . $tablePrefix . "_photodetail 
            (photoId, locale, name, description, alt, createdAt, updatedAt) 
            VALUES (:photoId, :locale, :name, :description, :alt, NOW(), NOW())";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':photoId', $this->photoId, PDO::PARAM_INT);
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':locale', $this->locale);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':alt', $this->alt);

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

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "UPDATE " . $tablePrefix . "_photodetail 
            SET photoId = :photoId, 
                locale = :locale, 
                name = :name, 
                description = :description, 
                alt = :alt, 
                updatedAt = NOW() 
            WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':photoId', $this->photoId, PDO::PARAM_INT);
        $stmt->bindValue(':locale', $this->locale);
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':alt', $this->alt);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();
        return $this;
    }

    public function delete(): bool
    {
        // Suppression du PageDetail
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "DELETE FROM " . $tablePrefix . "_photodetail WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function handleEntity(array $data): self
    {
        $detail = new static($data);
        return $detail;
    }
}

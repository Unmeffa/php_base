<?php
require_once(__DIR__ . '/abstract/class.AbstractDetails.php');
require_once(__DIR__ . '/class.Zone.php');

class ZoneDetail extends AbstractDetails
{
    protected ?int $zoneId;
    protected ?string $hName;

    public function __construct(array $tab)
    {
        parent::__construct($tab);
        $this->zoneId = $tab['zoneId'];
        $this->hName = $tab['hName'] ?? '';
    }

    public static function getZoneDetail(int $id): ?self
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT * FROM " . $tablePrefix . "_zonedetail WHERE id = :id LIMIT 1";
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

        $query = "INSERT INTO " . $tablePrefix . "_zonedetail 
            (zoneId, locale, name, hName, description, createdAt, updatedAt) 
            VALUES (:zoneId, :locale, :name, :hName, :description, NOW(), NOW())";


        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':zoneId', $this->zoneId, PDO::PARAM_INT);
        $stmt->bindValue(':locale', $this->locale);
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':hName', $this->hName);
        $stmt->bindValue(':description', $this->description);

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

        $query = "UPDATE " . $tablePrefix . "_zonedetail 
            SET zoneId = :zoneId, 
                locale = :locale, 
                name = :name, 
                hName = :hName,
                description = :description, 
                updatedAt = NOW() 
            WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':zoneId', $this->zoneId, PDO::PARAM_INT);
        $stmt->bindValue(':locale', $this->locale);
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':hName', $this->hName);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();

        if ($this->locale == 'fr') {
            $zone = Zone::getZone($this->zoneId);
            $zone->set('name', $this->name);
            $zone->update();
        }

        return $this;
    }

    public function delete(): bool
    {
        // Suppression du PageDetail
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "DELETE FROM " . $tablePrefix . "_zonedetail WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function handleEntity(array $data): self
    {
        $zoneDetail = new static($data);

        if (isset($data['id']) && $data['id'] > 0) {
            $zoneDetail->id = $data['id'];
            return $zoneDetail->update();
        } else {
            return $zoneDetail->create();
        }
    }
}

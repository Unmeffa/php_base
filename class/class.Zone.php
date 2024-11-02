<?php

require_once(__DIR__ . '/abstract/class.AbstractEntity.php');
require_once(__DIR__ . '/class.Fonction.php');
require_once(__DIR__ . '/class.Photo.php');
require_once(__DIR__ . '/class.ZoneDetail.php');

class Zone extends AbstractEntity
{
    protected ?int $parentId;
    protected string $type;
    protected string $gabarit;
    protected string $subtype;
    protected string $headtype;

    public ?ZoneDetail $details = null;


    public function __construct(array $tab)
    {
        parent::__construct($tab);
        $this->parentId = !empty($tab['parentId']) ? (int) $tab['parentId'] : null;
        $this->type = $tab['type'] ?? 'page';
        $this->gabarit = $tab['gabarit'] ?? 'text';
        $this->subtype = $tab['subtype'] ?? 'normal';
        $this->headtype = $tab['headtype'] ?? '2';

        $this->details = null;
    }

    public function create(): self
    {

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $pagePrio = $this->countSimilarZones($this->parentId, $this->type);
        $this->prio = $pagePrio + 1;

        $query = "INSERT INTO " . $tablePrefix . "_zone (name, parentId, active, prio, type, createdAt, updatedAt, gabarit, subtype) 
        VALUES (:name, :parentId, :active, :prio, :type, :createdAt, :updatedAt, :gabarit, :subtype)";
        $request = $pdo->prepare($query);

        $request->bindValue(':name', $this->name);
        $request->bindValue(':parentId', $this->parentId);
        $request->bindValue(':active', $this->active, PDO::PARAM_BOOL);
        $request->bindValue(':prio', $this->prio, PDO::PARAM_INT);
        $request->bindValue(':type', $this->type);
        $request->bindValue(':gabarit', $this->gabarit);
        $request->bindValue(':subtype', $this->subtype);
        $request->bindValue(':createdAt', $this->createdAt->format('Y-m-d H:i:s'));
        $request->bindValue(':updatedAt', $this->createdAt->format('Y-m-d H:i:s'));

        if ($request->execute()) {
            $this->id = $pdo->lastInsertId();
        }

        $this->addDetails();
        return $this;
    }

    private function addDetails(): void
    {

        $langs = DB::getLangs();
        foreach ($langs as $locale => $isActive) {
            if ($isActive == 1) {

                $data = [
                    "name" => $this->name,
                    'locale' => $locale,
                    'zoneId' => $this->id,
                ];

                $pageDetail = new ZoneDetail($data);
                $pageDetail->create();
            }
        }
    }


    public function update(): self
    {
        if (!$this->id) {
            throw new Exception("Impossible de mettre à jour : l'ID est manquant.");
        }

        $this->updatedAt = new DateTime();

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();


        $query = "UPDATE " . $tablePrefix . "_zone 
        SET name = :name, 
            parentId = :parentId, 
            active = :active, 
            prio = :prio, 
            type = :type,
            gabarit = :gabarit, 
            subtype = :subtype,
            headtype = :headtype,
            updatedAt = :updatedAt WHERE id = :id";

        $request = $pdo->prepare($query);
        $request->bindValue(':name', $this->name);
        $request->bindValue(':parentId', $this->parentId);
        $request->bindValue(':active', $this->active, PDO::PARAM_BOOL);
        $request->bindValue(':prio', $this->prio, PDO::PARAM_INT);
        $request->bindValue(':type', $this->type);
        $request->bindValue(':gabarit', $this->gabarit);
        $request->bindValue(':subtype', $this->subtype);
        $request->bindValue(':headtype', $this->headtype);
        $request->bindValue(':updatedAt', $this->updatedAt->format('Y-m-d H:i:s'));
        $request->bindValue(':id', $this->id, PDO::PARAM_INT);

        $request->execute();

        return $this;
    }

    public function delete(): bool
    {
        if (!$this->id) {
            throw new Exception("Impossible de supprimer : l'ID est manquant.");
        }

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        /*$zoneDetails = Fonction::recup('zonedetail', "WHERE zoneId = " . $this->id);
        foreach ($zoneDetails as $detailData) {
            $zoneDetails = new PageDetail($detailData);
            $pageDetail->delete();
        }*/


        $photos = $this->getPhotos();
        foreach ($photos as $photo) {
            $photo->delete();
        }

        $query = "DELETE FROM " . $tablePrefix . "_zone WHERE id = :id";
        $request = $pdo->prepare($query);
        $request->bindValue(':id', $this->id, PDO::PARAM_INT);

        if ($request->execute()) {

            $this->reorderPriorities($this->parentId, $this->type);
            return true;
        }
        return false;
    }


    public static function handleEntity(array $data): self
    {
        $page = new static($data);
        if (isset($data['id']) && $data['id'] > 0) {
            return $page->update();
        } else {
            return $page->create();
        }
    }

    function countSimilarZones(int $parentId, string $type): int
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT COUNT(*) FROM " . $tablePrefix . "_page WHERE parentId = :parentId AND type = :type";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':parentId', $parentId, PDO::PARAM_INT);
        $stmt->bindValue(':type', $type);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function updatePrio(int $newPrio = null, int $newParentId = null)
    {
        $currentParentId = $this->get('parentId');
        $currentPrio = $this->get('prio');
        $type = $this->get('type');

        $pages = Fonction::recup('page', "WHERE parentId = " . $currentParentId . " AND type = '" . $type . "' ORDER BY prio ASC");

        $maxPrio = count($pages);
        if ($newPrio < 1 || $newPrio > $maxPrio) {
            throw new Exception('Priorité invalide.');
        }

        foreach ($pages as $otherPage) {
            if ($otherPage['prio'] == $newPrio) {
                $otherPageObj = new Page($otherPage);
                $otherPageObj->set('prio', $currentPrio); // Echanger les priorités
                $otherPageObj->update();
                break;
            }
        }
        $this->set('prio', $newPrio);
        $this->update();
        $this->reorderPriorities($currentParentId, $type);
    }

    private function reorderPriorities(int $parentId, string $type)
    {
        $whereClause = "WHERE parentId = " . $parentId . " AND type = '" . $type . "'";
        $pages = Fonction::recup('zone', $whereClause . " ORDER BY prio ASC");
        $prio = 1;
        foreach ($pages as $pageData) {
            $page = new Zone($pageData);
            $page->set('prio', $prio++);
            $page->update();
        }
    }

    public function getGabaritName()
    {
        $name = Fonction::getZoneTypeLabel($this->gabarit);
        if ($this->subtype != 'normal') {
            $name .= Fonction::getZoneVariation($this->subtype);
        }

        return $name;
    }

    public static function getZone(int $id, string $locale = "fr")
    {
        if (!$id || $id < 1) {
            throw new Exception("Impossible de récupérer la zone : l'ID est manquant.");
        }

        $zone = Fonction::recup('zone', "where id = " . $id);
        if ($zone[0]) {
            $zoneObject = new self($zone[0]);
            $zoneDetail = Fonction::recup('zonedetail', "where zoneId = " . $id . " and locale = '" . $locale . "'");
            if ($zoneDetail[0]) {
                $zDetailObject = new ZoneDetail($zoneDetail[0]);
                $zoneObject->set('details', $zDetailObject);
            }
            return $zoneObject;
        }
        return null;
    }

    public function getParentsProps()
    {
        return ['parentId' => $this->parentId, 'type' => $this->type];
    }

    public function getPhotos()
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT * FROM " . $tablePrefix . "_photo WHERE parentId = :parentId AND type = :type order by prio ASC";
        $request = $pdo->prepare($query);
        $request->bindValue(':parentId', $this->id, PDO::PARAM_INT);
        $request->bindValue(':type', 'zone');

        $request->execute();
        $photosData = $request->fetchAll(PDO::FETCH_ASSOC);

        $photos = [];
        foreach ($photosData as $photoData) {
            $photo = new Photo($photoData); // Instancier chaque Page
            $photos[] = $photo; // Ajout de l'instance Page au tableau
        }

        return $photos;
    }

    public function getDetails()
    {
        return $this->details;
    }
}

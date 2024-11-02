<?php

require_once(__DIR__ . '/abstract/class.AbstractEntity.php');
require_once(__DIR__ . '/class.PhotoDetail.php');

class Photo extends AbstractEntity
{
    protected string $originalName;
    protected string $name;
    protected string $type;
    protected ?int $parentId;
    protected ?string $mimeType;
    protected int $size;
    protected int $width;
    protected int $height;

    protected ?PhotoDetail $details;

    public function __construct(array $tab)
    {
        parent::__construct($tab);
        $this->parentId = !empty($tab['parentId']) ? (int) $tab['parentId'] : null;
        $this->originalName = $tab['originalName'] ?? '';
        $this->name = $tab['name'] ?? '';
        $this->type = $tab['type'] ?? '';
        $this->mimeType = $tab['mimeType'] ?? null;
        $this->size = $tab['size'] ?? 0;
        $this->width = $tab['width'] ?? 0;
        $this->height = $tab['height'] ?? 0;

        $this->details = null;
    }

    public function create(): self
    {

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $pagePrio = $this->countPhotosByParent($this->parentId, $this->type);
        $this->prio = $pagePrio + 1;

        $query = "INSERT INTO " . $tablePrefix . "_photo 
    (originalName, name, parentId, type, prio, mimeType, size, width, height, createdAt) 
    VALUES (:originalName, :name, :parentId, :type, :prio, :mimeType, :size, :width, :height, :createdAt)";
        $request = $pdo->prepare($query);

        $request->bindValue(':name', $this->name);
        $request->bindValue(':originalName', $this->originalName);
        $request->bindValue(':parentId', $this->parentId);
        $request->bindValue(':type', $this->type);
        $request->bindValue(':prio', $this->prio, PDO::PARAM_INT);
        $request->bindValue(':mimeType', $this->mimeType);
        $request->bindValue(':size', $this->size, PDO::PARAM_INT);
        $request->bindValue(':width', $this->width, PDO::PARAM_INT);
        $request->bindValue(':height', $this->height, PDO::PARAM_INT);
        $request->bindValue(':prio', $this->prio, PDO::PARAM_INT);
        $request->bindValue(':createdAt', $this->createdAt->format('Y-m-d H:i:s'));

        if ($request->execute()) {
            $this->id = $pdo->lastInsertId();
        }

        return $this;
    }

    public function update(): self
    {
        return $this;
    }

    public function delete(): bool
    {
        if (!$this->id) {
            throw new Exception("Impossible de supprimer : l'ID est manquant.");
        }


        $photoDetails = Fonction::recup('photodetail', "where photoId = " . $this->id);
        foreach ($photoDetails as $detail) {
            $photoDetail = new PhotoDetail(($detail));
            $photoDetail->delete();
        }


        $filePath = $this->getRelativePath();
        if (file_exists($filePath)) {
            unlink($filePath); // Supprime le fichier si trouvé
        }

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();
        $query = "DELETE FROM " . $tablePrefix . "_photo WHERE id = :id";
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

    private function getRelativePath(): string
    {
        return __DIR__ . "/../photo/" . $this->type . "/" . $this->parentId . "/" . $this->name;
    }


    public function getFilePath(): string
    {
        return BASE_URL . "/photo/" . $this->type . "/" . $this->parentId . "/" . $this->name;
    }

    function countPhotosByParent(int $parentId, string $type): int
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT COUNT(*) FROM " . $tablePrefix . "_photo WHERE parentId = :parentId and type = :type";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':parentId', $parentId, PDO::PARAM_INT);
        $stmt->bindValue(':type', $type);

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function updatePrio(int $newPrio = null)
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
                $otherPageObj = new Photo($otherPage);
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
        $pages = Fonction::recup('photo', $whereClause . " ORDER BY prio ASC");
        $prio = 1;
        foreach ($pages as $pageData) {
            $page = new Photo($pageData);
            $page->set('prio', $prio++);
            $page->update();
        }
    }

    public static function getPhoto(int $id, string $locale = "fr")
    {
        if (!$id || $id < 1) {
            throw new Exception("Impossible de récupérer la photo : l'ID est manquant.");
        }

        $photo = Fonction::recup('photo', "where id = " . $id);
        if ($photo[0]) {
            $photoObject = new self($photo[0]);
            $photoDetail = Fonction::recup('photodetail', "where photoId = " . $id . " and locale = '" . $locale . "'");
            if ($photoDetail[0]) {
                $photoDetailObject = new PhotoDetail($photoDetail[0]);
                $photoObject->set('details', $photoDetailObject);
                return $photoObject;
            }
            return $photoObject;
        }
        return null;
    }

    public function getDetails()
    {
        return $this->details;
    }
}

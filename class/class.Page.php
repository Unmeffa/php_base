<?php

require_once(__DIR__ . '/abstract/class.AbstractEntity.php');
require_once(__DIR__ . '/class.PageDetail.php');

class Page extends AbstractEntity
{
    protected ?int $parentId;
    protected string $type;
    public array $children = [];
    public ?PageDetail $details = null;

    public function __construct(array $tab)
    {
        parent::__construct($tab);
        $this->parentId = !empty($tab['parentId']) ? (int) $tab['parentId'] : null;
        $this->type = $tab['type'] ?? 'page';
    }

    public function create(): self
    {

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $pagePrio = $this->countPagesByParentId($this->parentId);
        $this->prio = $pagePrio + 1;

        $query = "INSERT INTO " . $tablePrefix . "_page (name, parentId, active, prio, type, createdAt, updatedAt) 
        VALUES (:name, :parentId, :active, :prio, :type, :createdAt, :updatedAt)";
        $request = $pdo->prepare($query);

        $request->bindValue(':name', $this->name);
        $request->bindValue(':parentId', $this->parentId);
        $request->bindValue(':active', $this->active, PDO::PARAM_BOOL);
        $request->bindValue(':prio', $this->prio, PDO::PARAM_INT);
        $request->bindValue(':type', $this->type);
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

                $baseSlug = Url::slugify($this->name);
                $slug = $this->generateUniqueSlugForLocale($baseSlug, $locale);

                $data = [
                    "name" => $this->name,
                    'locale' => $locale,
                    'pageId' => $this->id,
                    'url' => $slug,
                ];

                $pageDetail = new PageDetail($data);
                $pageDetail->create();
            }
        }
    }

    private function generateUniqueSlugForLocale(string $baseSlug, string $locale): string
    {
        // Création d'un slug unique en ajoutant le suffixe `locale` si nécessaire
        $slug = $locale !== 'fr' ? $baseSlug . '-' . $locale : $baseSlug;
        $suffix = 1;

        // Boucle pour garantir l'unicité du slug
        while (!Url::isSlugUnique($slug)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    public function update(): self
    {
        if (!$this->id) {
            throw new Exception("Impossible de mettre à jour : l'ID est manquant.");
        }

        $this->updatedAt = new DateTime();

        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();


        $query = "UPDATE " . $tablePrefix . "_page 
        SET name = :name, 
            parentId = :parentId, 
            active = :active, 
            prio = :prio, 
            type = :type,
            updatedAt = :updatedAt WHERE id = :id";

        $request = $pdo->prepare($query);
        $request->bindValue(':name', $this->name);
        $request->bindValue(':parentId', $this->parentId);
        $request->bindValue(':active', $this->active, PDO::PARAM_BOOL);
        $request->bindValue(':prio', $this->prio, PDO::PARAM_INT);
        $request->bindValue(':type', $this->type);
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

        $pageDetails = Fonction::recup('pagedetail', "WHERE pageId = " . $this->id);
        foreach ($pageDetails as $detailData) {
            $pageDetail = new PageDetail($detailData);
            if ($pageDetail->get('urlId')) {
                $url = Url::getById($pageDetail->get('urlId'));
                if ($url) {
                    $url->delete();
                }
            }

            $pageDetail->delete();
        }

        $pages = Fonction::recup('page', "WHERE parentId = " . $this->id . " ORDER BY prio ASC");
        if (count($pages) > 0) {
            foreach ($pages as $child) {
                $childPage = new Page($child);
                $childPage->set('active', 0);
                $childPage->set('parentId', null);
                $childPage->update();
            }
            $this->reorderPriorities(null);
        }

        $zones = $this->getZones();
        foreach ($zones as $zone) {
            $zone->delete();
        }



        $query = "DELETE FROM " . $tablePrefix . "_page WHERE id = :id";
        $request = $pdo->prepare($query);
        $request->bindValue(':id', $this->id, PDO::PARAM_INT);

        if ($request->execute()) {
            $this->reorderPriorities($this->parentId);
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

    function countPagesByParentId(?int $parentId): int
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        if (is_null($parentId)) {
            $query = "SELECT COUNT(*) FROM " . $tablePrefix . "_page WHERE parentId IS NULL";
            $stmt = $pdo->prepare($query);
        } else {
            $query = "SELECT COUNT(*) FROM " . $tablePrefix . "_page WHERE parentId = :parentId";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':parentId', $parentId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function updatePrio(int $newPrio = null, int $newParentId = null)
    {
        $currentParentId = $this->get('parentId');
        $currentPrio = $this->get('prio');
        $id = $this->get('id');

        if (!is_null($newParentId) && $newParentId !== $currentParentId) {
            $this->set('parentId', $newParentId);
            $this->update();
            $this->reorderPriorities($currentParentId);
            $this->reorderPriorities($newParentId);
            return;
        }

        if ($newPrio !== null) {

            $whereClause = is_null($currentParentId) ? "WHERE parentId IS NULL" : "WHERE parentId = " . $currentParentId;
            $pages = Fonction::recup('page', $whereClause . " ORDER BY prio ASC");

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
        }

        $this->reorderPriorities($currentParentId);
    }

    private function reorderPriorities(int $parentId = null)
    {
        $whereClause = is_null($parentId) ? "WHERE parentId IS NULL" : "WHERE parentId = " . $parentId;
        $pages = Fonction::recup('page', $whereClause . " ORDER BY prio ASC");
        $prio = 1;
        foreach ($pages as $pageData) {
            $page = new Page($pageData);
            $page->set('prio', $prio++);
            $page->update();
        }
    }

    public static function getPage(int $id, string $locale = "fr")
    {
        if (!$id || $id < 1) {
            throw new Exception("Impossible de récupérer la page : l'ID est manquant.");
        }

        $page = Fonction::recup('page', "where id = " . $id);
        if ($page[0]) {
            $pageObject = new self($page[0]);
            $pageDetail = Fonction::recup('pagedetail', "where pageId = " . $id . " and locale = '" . $locale . "'");
            if ($pageDetail[0]) {
                $pageDetailObject = new PageDetail($pageDetail[0]);
                $pageObject->set('details', $pageDetailObject);
                return $pageObject;
            }
        }
        return null;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getUrl()
    {
        if ($this->details->get('urlId') !== null) {
            return $this->details->getUrl();
        }
        return "";
    }

    public function getZones()
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT * FROM " . $tablePrefix . "_zone WHERE parentId = :parentId AND type = :type order by prio ASC";
        $request = $pdo->prepare($query);
        $request->bindValue(':parentId', $this->id, PDO::PARAM_INT);
        $request->bindValue(':type', 'page');

        $request->execute();
        $zonesData = $request->fetchAll(PDO::FETCH_ASSOC);

        $zones = [];
        foreach ($zonesData as $zoneData) {
            $zone = new Zone($zoneData); // Instancier chaque Page
            $zones[] = $zone; // Ajout de l'instance Page au tableau
        }

        return $zones;
    }
}

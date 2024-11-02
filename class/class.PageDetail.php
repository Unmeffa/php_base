<?php
require_once(__DIR__ . '/abstract/class.AbstractDetails.php');
require_once(__DIR__ . '/trait/MetasTrait.php');
require_once(__DIR__ . '/class.Page.php');

class PageDetail extends AbstractDetails
{
    use MetasTrait;
    protected ?int $pageId;
    protected ?int $urlId = null; // Clé étrangère vers la table URL
    public ?Url $url = null;

    public function __construct(array $tab)
    {
        parent::__construct($tab);
        $this->pageId = $tab['pageId'];

        $this->metaTitle = $tab['metaTitle'] ?? '';
        $this->metaDescription = $tab['metaDescription'] ?? '';
        if (!empty($tab['urlId'])) {
            $this->urlId = $tab['urlId'];
            $this->url = Url::getById($this->urlId);
        } elseif (isset($tab['url'])) {
            $this->url = Url::getBySlugAndLocale($tab['url'], $tab['locale']) ?? new Url([
                'slug' => $tab['url'],
                'locale' => $tab['locale'],
                'type' => 'page'
            ]);
        }
    }

    protected function generateUniqueSlug(string $name, string $locale): string
    {
        $baseSlug = Url::slugify($name); // Slug de base à partir du nom de la page
        $slug = "{$baseSlug}-{$locale}"; // Inclure le locale dès le départ
        $suffix = 1;

        // Boucle pour vérifier l'unicité du slug
        while (!Url::isSlugUnique($slug)) {
            $slug = "{$baseSlug}-{$suffix}-{$locale}";
            $suffix++;
        }

        return $slug;
    }

    public function updateOrCreateUrl(?string $customUrl): void
    {
        // Sauvegarde temporaire de l'urlId actuel
        $currentUrlId = $this->urlId;

        try {
            $slug = $customUrl ? Url::slugify($customUrl) : $this->generateUniqueSlug($this->name, $this->locale);
            $existingUrl = Url::getBySlugAndLocale($slug, $this->locale);
            if ($existingUrl) {
                $associatedPageDetail = PageDetail::getByUrlId($existingUrl->get('id'));
                if ($associatedPageDetail && $associatedPageDetail->get('id') != $this->id) {
                    throw new Exception("L'URL '$slug' est déjà utilisé pour une autre page.");
                }

                // Associe l'URL existante si aucune exception n'est levée
                $this->url = $existingUrl;
                $this->urlId = $existingUrl->get('id');
                $this->url->set('slug', $slug);
                $this->url->update();
            } else {
                // Crée une nouvelle URL si le slug n'existe pas déjà
                $urlData = [
                    'slug' => $slug,
                    'locale' => $this->locale,
                    'type' => 'page'
                ];
                $this->url = Url::handleEntity($urlData);
                $this->urlId = $this->url->get('id');
            }
            $this->update();
        } catch (Exception $e) {
            $this->urlId = $currentUrlId;
            throw new Exception("Erreur lors de la mise à jour ou de la création de l'URL : " . $e->getMessage());
        }
    }

    public static function getPageDetail(int $id): ?self
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT * FROM " . $tablePrefix . "_pagedetail WHERE id = :id LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new self($result) : null;
    }

    public static function getByUrlId(int $urlId): ?self
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "SELECT * FROM " . $tablePrefix . "_pagedetail WHERE urlId = :urlId LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':urlId', $urlId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return new self($result);
        }

        return null; // Retourne null si aucun enregistrement n'est trouvé
    }

    public function create(): self
    {
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        // Créer ou récupérer l'ID de l'URL
        if ($this->url !== null) {
            $urlData = [
                'slug' => $this->url,
                'locale' => $this->locale,
                'type' => 'page'
            ];
            $urlInstance = Url::handleEntity($urlData);
            $this->urlId = $urlInstance->get('id'); // Assigne l'ID de l'URL à urlId
        }

        // Insère le détail de la page dans la base de données
        $query = "INSERT INTO " . $tablePrefix . "_pagedetail (name, description, locale, pageId, urlId, createdAt) 
              VALUES (:name, :description, :locale, :pageId, :urlId, :createdAt)";
        $stmt = $pdo->prepare($query);

        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':locale', $this->locale);
        $stmt->bindValue(':pageId', $this->pageId, PDO::PARAM_INT);
        $stmt->bindValue(':urlId', $this->urlId, PDO::PARAM_INT);
        $stmt->bindValue(':createdAt', $this->createdAt->format('Y-m-d H:i:s'));

        $stmt->execute();
        if ($this->locale == 'fr') {
            $page = Page::getPage($this->pageId);
            $page->set('name', $this->name);
            $page->update();
        }
        return $this;
    }

    public function update(): self
    {
        // Mettre à jour le PageDetail dans la base de données
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "UPDATE " . $tablePrefix . "_pagedetail 
                  SET name = :name, description = :description, locale = :locale, urlId = :urlId, updatedAt = :updatedAt 
                  WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':locale', $this->locale);
        $stmt->bindValue(':urlId', $this->urlId, PDO::PARAM_INT);
        $stmt->bindValue(':updatedAt', $this->updatedAt->format('Y-m-d H:i:s'));
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();

        return $this;
    }

    public function delete(): bool
    {
        // Suppression du PageDetail
        $tablePrefix = DB::getTablePrefix();
        $pdo = DB::getInstance();

        $query = "DELETE FROM " . $tablePrefix . "_pagedetail WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function handleEntity(array $data): self
    {
        $pageDetail = new static($data);
        return $pageDetail;
    }

    public function getUrl(): ?string
    {
        return $this->url ? $this->url->get('slug') : null;
    }
}

<?php

require_once(__DIR__ . '/../trait/DateTrait.php');

abstract class AbstractDetails
{

    use DateTrait;

    protected $id;
    protected string $name;
    protected string $description;
    protected string $locale;
    protected ?Url $url = null;

    public function __construct(array $tab)
    {
        $this->id = $tab['id'] ?? null;
        $this->name = $tab['name'] ?? '';
        $this->locale = $tab['locale'] ?? 'fr';
        $this->description = $tab['description'] ?? '';

        $this->initializeDates($tab);
    }

    public function set($name, $value)
    {
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

    abstract public function create(): self;
    abstract public function update(): self;
    abstract public function delete(): bool;
    abstract public static function handleEntity(array $data): self;
}

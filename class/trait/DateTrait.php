<?php

trait DateTrait
{
    protected DateTime $createdAt;
    protected ?DateTime $updatedAt = null;

    public function initializeDates(array $tab): void
    {
        // Conversion de createdAt en DateTime si nécessaire
        $this->createdAt = isset($tab['createdAt']) && !$tab['createdAt'] instanceof DateTime
            ? new DateTime($tab['createdAt'])
            : ($tab['createdAt'] ?? new DateTime());

        // Conversion de updatedAt en DateTime si nécessaire
        $this->updatedAt = isset($tab['updatedAt']) && !$tab['updatedAt'] instanceof DateTime
            ? new DateTime($tab['updatedAt'])
            : ($tab['updatedAt'] ?? null);
    }
}

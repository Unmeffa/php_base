<?php

trait MetasTrait
{
    protected string $metaTitle;
    protected string $metaDescription;

    public function __construct(array $tab)
    {
        $this->metaTitle = $tab['metaTitle'];
        $this->metaDescription = $tab['metaDescription'];
    }
}

<?php

declare(strict_types=1);

namespace LaminasTestApiToolsDb\Entity;

class Product
{
    protected $id;

    protected $version;

    public function getId()
    {
        return $this->id;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version): void
    {
        $this->version = $version;
    }
}

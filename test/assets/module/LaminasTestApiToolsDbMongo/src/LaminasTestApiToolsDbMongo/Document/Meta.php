<?php

declare(strict_types=1);

namespace LaminasTestApiToolsDbMongo\Document;

use DateTime;

class Meta
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    protected $name;

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;

        return $this;
    }

    protected $createdAt;

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $value)
    {
        $this->createdAt = $value;
    }

    public function getArrayCopy()
    {
        return [
            'name'      => $this->getName(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }

    public function exchangeArray($values)
    {
        $this->setName($values['name'] ?? null);
        $this->setCreatedAt($values['createdAt'] ?? null);
    }
}

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

    /**
     * @return static
     */
    public function setName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    protected $createdAt;

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $value): void
    {
        $this->createdAt = $value;
    }

    /**
     * @return array
     *
     * @psalm-return array{name: mixed, createdAt: mixed}
     */
    public function getArrayCopy(): array
    {
        return [
            'name'      => $this->getName(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }

    public function exchangeArray($values): void
    {
        $this->setName($values['name'] ?? null);
        $this->setCreatedAt($values['createdAt'] ?? null);
    }
}

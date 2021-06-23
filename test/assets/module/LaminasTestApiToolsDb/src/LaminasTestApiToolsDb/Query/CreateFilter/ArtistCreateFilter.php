<?php

declare(strict_types=1);

namespace LaminasTestApiToolsDb\Query\CreateFilter;

use Laminas\ApiTools\Doctrine\Server\Query\CreateFilter\AbstractCreateFilter;
use Laminas\ApiTools\Rest\ResourceEvent;

class ArtistCreateFilter extends AbstractCreateFilter
{
    /**
     * @param string $entityClass
     * @param array $data
     * @return array
     */
    public function filter(ResourceEvent $event, $entityClass, $data)
    {
        $this->getObjectManager();
        return $data;
    }
}

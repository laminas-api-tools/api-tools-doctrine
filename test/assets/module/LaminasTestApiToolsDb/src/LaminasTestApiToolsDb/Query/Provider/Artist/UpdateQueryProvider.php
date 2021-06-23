<?php

declare(strict_types=1);

namespace LaminasTestApiToolsDb\Query\Provider\Artist;

use Laminas\ApiTools\Doctrine\Server\Query\Provider\AbstractQueryProvider;
use Laminas\ApiTools\Rest\ResourceEvent;

class UpdateQueryProvider extends AbstractQueryProvider
{
    /**
     * @param string $entityClass
     * @param array $parameters
     * @return mixed This will return an ORM or ODM Query\Builder
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters)
    {
        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder
            ->select('row')
            ->from($entityClass, 'row');

        return $queryBuilder;
    }
}

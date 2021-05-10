<?php

namespace Laminas\ApiTools\Doctrine\Server\Query\Provider;

use Laminas\ApiTools\Rest\ResourceEvent;

class DefaultOrm extends AbstractQueryProvider
{
    /**
     * @param ResourceEvent $event
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

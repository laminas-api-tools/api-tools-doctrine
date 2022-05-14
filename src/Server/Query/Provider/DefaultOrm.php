<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Server\Query\Provider;

use Doctrine\ORM\QueryBuilder;
use Laminas\ApiTools\Rest\ResourceEvent;

class DefaultOrm extends AbstractQueryProvider
{
    /**
     * @param string $entityClass
     * @param array $parameters
     */
    public function createQuery(ResourceEvent $event, $entityClass, $parameters): QueryBuilder
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getObjectManager()->createQueryBuilder();
        $queryBuilder
            ->select('row')
            ->from($entityClass, 'row');

        return $queryBuilder;
    }
}

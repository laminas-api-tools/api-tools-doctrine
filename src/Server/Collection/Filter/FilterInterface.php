<?php

namespace Laminas\ApiTools\Doctrine\Server\Collection\Filter;

interface FilterInterface
{
    public function filter($queryBuilder, $metadata, $option);
}

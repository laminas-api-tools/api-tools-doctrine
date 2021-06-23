<?php

declare(strict_types=1);

namespace LaminasTestApiToolsDb\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

use function md5;
use function mt_rand;
use function strrev;
use function time;

class RevGenerator extends AbstractIdGenerator
{
    public function generate(EntityManager $em, $entity)
    {
        do {
            $value = md5(time() . mt_rand());
        } while ($value === strrev($value));

        return $value;
    }
}

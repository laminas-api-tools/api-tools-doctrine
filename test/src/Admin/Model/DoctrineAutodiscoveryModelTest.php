<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Admin\Model;

use Laminas\ApiTools\Doctrine\Admin\Model\DoctrineAutodiscoveryModel;
use LaminasTest\ApiTools\Doctrine\TestCase;
use LaminasTestApiToolsDb\Entity\Album;
use LaminasTestApiToolsDb\Entity\Artist;
use LaminasTestApiToolsDb\Entity\Product;
use LaminasTestApiToolsDbMongo\Document\Meta;

use function strcasecmp;
use function usort;

class DoctrineAutodiscoveryModelTest extends TestCase
{
    public function testORMAutodiscoveryEntitiesWithFields(): void
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../config/ORM/application.config.php'
        );

        $model = new DoctrineAutodiscoveryModel([]);
        $model->setServiceLocator($this->getApplicationServiceLocator());

        $result = $model->fetchFields(null, null, 'doctrine.entitymanager.orm_default');

        $this->assertIsArray($result);
        $this->assertCount(3, $result);

        usort($result, function (array $a, array $b) {
            return strcasecmp($a['service_name'], $b['service_name']);
        });

        $this->assertEquals(Album::class, $result[0]['entity_class']);
        $this->assertEquals('Album', $result[0]['service_name']);
        $this->assertCount(2, $result[0]['fields']);

        $this->assertEquals(Artist::class, $result[1]['entity_class']);
        $this->assertEquals('Artist', $result[1]['service_name']);
        $this->assertCount(2, $result[1]['fields']);

        $this->assertEquals(Product::class, $result[2]['entity_class']);
        $this->assertEquals('Product', $result[2]['service_name']);
        $this->assertCount(1, $result[2]['fields']);
    }
}

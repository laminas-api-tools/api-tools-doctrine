<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Doctrine\Server\Event\Listener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Instantiator\InstantiatorInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Laminas\ApiTools\Doctrine\Server\Event\Listener\CollectionListener;
use Laminas\Hydrator\HydratorInterface;
use LaminasTestApiToolsDb\Entity\Artist;

class CollectionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider trueFalseProvider
     * @param bool $withEntityFactory
     * @return void
     */
    public function testProcessNewEntity($withEntityFactory)
    {
        $artist = $this->getMockBuilder(Artist::class)->getMock();
        $data = [];

        /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject $om */
        $om = $this->getMockBuilder(ObjectManager::class)->getMock();
        $classMetadata = $this->getMockBuilder(ClassMetadata::class)
                ->disableOriginalConstructor()
                ->getMock();
        $classMetadata->expects(self::once())
            ->method('getIdentifierFieldNames')
            ->with(Artist::class)
            ->willReturn(['id']);

        $om->expects(self::once())
            ->method('getClassMetadata')
            ->with(Artist::class)
            ->willReturn($classMetadata);

        $om->expects(self::once())
            ->method('persist')
            ->with(self::isInstanceOf(Artist::class));

        $hydrator = $this->getMockBuilder(HydratorInterface::class)->getMock();
        $hydrator->expects(self::once())
            ->method('hydrate')
            ->with($data, self::isInstanceOf(Artist::class));

        if ($withEntityFactory) {
            /** @var InstantiatorInterface|\PHPUnit_Framework_MockObject_MockObject $entityFactory */
            $entityFactory = $this->getMockBuilder(InstantiatorInterface::class)->getMock();

            $entityFactory->expects(self::once())
                ->method('instantiate')
                ->with(Artist::class)
                ->willReturn($artist);
        } else {
            $entityFactory = null;
        }

        $listener = new CollectionListener($entityFactory);
        $listener->setObjectManager($om);

        $hydratorMapProperty = new \ReflectionProperty($listener, 'entityHydratorMap');
        $hydratorMapProperty->setAccessible(true);
        $hydratorMapProperty->setValue($listener, [Artist::class => $hydrator]);

        $method = new \ReflectionMethod($listener, 'processEntity');
        $method->setAccessible(true);
        $method->invokeArgs($listener, [Artist::class, $data]);
    }

    public function trueFalseProvider()
    {
        return [[false], [true]];
    }
}

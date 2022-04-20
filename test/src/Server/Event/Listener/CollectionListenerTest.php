<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Server\Event\Listener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Instantiator\InstantiatorInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Laminas\ApiTools\Doctrine\Server\Event\Listener\CollectionListener;
use Laminas\Hydrator\HydratorInterface;
use LaminasTestApiToolsDb\Entity\Artist;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionMethod;
use ReflectionProperty;

class CollectionListenerTest extends TestCase
{
    /**
     * @dataProvider trueFalseProvider
     *
     * @param bool $withEntityFactory
     *
     * @return void
     */
    public function testProcessNewEntity($withEntityFactory)
    {
        $artist = $this->getMockBuilder(Artist::class)->getMock();
        $data = [];

        /** @var EntityManager|MockObject $om */
        $om = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()->getMock();
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
            /** @var InstantiatorInterface|PHPUnit_Framework_MockObject_MockObject $entityFactory */
            $entityFactory = $this->getMockBuilder(InstantiatorInterface::class)
                ->getMock();

            $entityFactory->expects(self::once())
                ->method('instantiate')
                ->with(Artist::class)
                ->willReturn($artist);
        } else {
            $entityFactory = null;
        }

        $listener = new CollectionListener($entityFactory);
        $listener->setObjectManager($om);

        $hydratorMapProperty = new ReflectionProperty(
            $listener, 'entityHydratorMap'
        );
        $hydratorMapProperty->setAccessible(true);
        $hydratorMapProperty->setValue($listener, [Artist::class => $hydrator]);

        $method = new ReflectionMethod($listener, 'processEntity');
        $method->setAccessible(true);
        $method->invokeArgs($listener, [Artist::class, $data]);
    }

    /** @psalm-return array<array-key, array{0: bool}> */
    public function trueFalseProvider(): array
    {
        return [[false], [true]];
    }
}

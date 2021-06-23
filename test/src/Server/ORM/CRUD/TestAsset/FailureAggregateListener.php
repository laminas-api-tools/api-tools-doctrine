<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine\Server\ORM\CRUD\TestAsset;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Doctrine\Server\Event\DoctrineResourceEvent;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;

use function sprintf;

class FailureAggregateListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /** @var string */
    private $eventName;

    /**
     * @param string $eventName
     */
    public function __construct($eventName)
    {
        $this->eventName = $eventName;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach($this->eventName, [$this, 'failure']);
    }

    /**
     * @return ApiProblem
     */
    public function failure(DoctrineResourceEvent $event)
    {
        $event->stopPropagation();
        return new ApiProblem(400, sprintf('LaminasTestFailureAggregateListener: %s', $event->getName()));
    }
}

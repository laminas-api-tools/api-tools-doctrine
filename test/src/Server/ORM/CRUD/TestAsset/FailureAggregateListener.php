<?php

namespace LaminasTest\ApiTools\Doctrine\Server\ORM\CRUD\TestAsset;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\ApiTools\Doctrine\Server\Event\DoctrineResourceEvent;
use Laminas\ApiTools\ApiProblem\ApiProblem;

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
     * @param DoctrineResourceEvent $event
     * @return ApiProblem
     */
    public function failure(DoctrineResourceEvent $event)
    {
        $event->stopPropagation();
        return new ApiProblem(400, sprintf('LaminasTestFailureAggregateListener: %s', $event->getName()));
    }
}

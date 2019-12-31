<?php

namespace LaminasTestApiToolsDb\EventListener;

use Laminas\ApiTools\Doctrine\Server\Event\DoctrineResourceEvent;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;

class ArtistAggregateListener implements ListenerAggregateInterface
{
    protected $listeners = [];

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            DoctrineResourceEvent::EVENT_CREATE_POST,
            [$this, 'createPost']
        );
    }

    public function createPost(DoctrineResourceEvent $event)
    {
        $objectManager = $event->getObjectManager();

        $event->getEntity();
        $event->getData();
        $event->getResourceEvent();
        $event->getEntityClassName();
        $event->getEntityId();
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}

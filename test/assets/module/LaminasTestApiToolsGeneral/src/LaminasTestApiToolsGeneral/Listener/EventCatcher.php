<?php

declare(strict_types=1);

namespace LaminasTestApiToolsGeneral\Listener;

use Laminas\ApiTools\Doctrine\DoctrineResource;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;

use function array_unique;
use function method_exists;

class EventCatcher
{
    const EVENT_IDENTIFIER = DoctrineResource::class;

    /** @var array */
    protected $listeners = [];

    /** @var array */
    protected $caughtEvents = [];

    public function attachShared(SharedEventManagerInterface $events)
    {
        $listener = $events->attach(self::EVENT_IDENTIFIER, '*', [$this, 'listen']);

        if (! $listener) {
            $listener = [$this, 'listen'];
        }

        $this->listeners[] = $listener;
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        $eventManagerVersion = method_exists($events, 'getEvents') ? 2 : 3;

        foreach ($this->listeners as $index => $listener) {
            switch ($eventManagerVersion) {
                case 2:
                    if ($events->detach(self::EVENT_IDENTIFIER, $listener)) {
                        unset($this->listeners[$index]);
                    }
                    break;
                case 3:
                    if ($events->detach($listener, self::EVENT_IDENTIFIER, '*')) {
                        unset($this->listeners[$index]);
                    }
                    break;
            }
        }
    }

    public function listen(Event $e)
    {
        $this->caughtEvents[] = $e->getName();
        array_unique($this->caughtEvents);
    }

    /**
     * @return array
     */
    public function getCaughtEvents()
    {
        return $this->caughtEvents;
    }
}

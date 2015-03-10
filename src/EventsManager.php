<?php

namespace Minetro\Events;

/**
 * EventsManager
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class EventsManager
{

    /** @var array */
    protected $listeners = [];

    /**
     * Register event
     *
     * @param string $event
     * @param callable $handler
     */
    public function on($event, $handler)
    {
        $this->listeners[$event][] = $handler;
    }

    /**
     * Fire events
     *
     * @param string $event
     */
    public function trigger($event)
    {
        $args = func_get_args();
        array_shift($args);

        $listeners = isset($this->listeners[$event]) ? $this->listeners[$event] : [];
        foreach ($listeners as $listener) {
            call_user_func_array($listener, $args);
        }
    }

    /**
     * Attach subscriber and register events
     *
     * @param EventsSubscriber $subscriber
     */
    public function attach(EventsSubscriber $subscriber)
    {
        $subscriber->onEvents($this);
    }

}

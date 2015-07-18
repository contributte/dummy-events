<?php

namespace Minetro\Events;

use Nette\DI\Container;

/**
 * EventsManager
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class EventsManager
{

    /** @var array */
    protected $listeners = [];

    /** @var array */
    protected $lazyListeners = [];

    /** @var array */
    protected $attachedServices = [];

    /** @var Container */
    private $container;

    /**
     * @param Container $container
     */
    function __construct(Container $container)
    {
        $this->container = $container;
    }

    /** API********************************************************************/

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
     * @param mixed  ...$args
     */
    public function trigger($event/*, ...$args*/)
    {
        if (isset($this->lazyListeners[$event])) {
            foreach ($this->lazyListeners[$event] as $name) {
                if (!isset($this->attachedServices[$name])) {
                    $this->attach($this->container->getService($name));
                    $this->attachedServices[$name] = TRUE;
                }
            }

            // Unset all lazy listeners from array, cause all listeners are already attached
            unset($this->lazyListeners[$event]);
        }

        if (isset($this->listeners[$event])) {
            $args = func_get_args();
            array_shift($args);

            foreach ($this->listeners[$event] as $listener) {
                call_user_func_array($listener, $args);
            }
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

    /**
     * Attach lazy subscriber and register events
     *
     * @param array $events
     * @param string $service
     */
    public function attachLazy(array $events, $service)
    {
        foreach ($events as $id => $event) {
            $this->lazyListeners[$event][] = $service;
        }
    }

}

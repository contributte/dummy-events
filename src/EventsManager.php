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
    protected $lazyServicesAttached = [];

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
     */
    public function trigger($event)
    {
        $args = func_get_args();
        array_shift($args);

        if (isset($this->lazyListeners[$event])) {
            foreach ($this->lazyListeners[$event] as $idx => $name) {
                if (in_array($name, $this->lazyServicesAttached)) {
                    unset($this->lazyListeners[$event][$idx]);
                    continue;
                }
                $this->attach($this->container->getService($name));
                $this->lazyServicesAttached[] = $name;
            }

            unset($this->lazyListeners[$event]);
        }

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

    /**
     * Attach lazy subscriber and register events
     *
     * @param array $tags
     * @param string $service
     */
    public function attachLazy(array $tags, $service)
    {
        foreach ($tags as $tag => $value) {
            $this->lazyListeners[$tag][] = $service;
        }
    }

}

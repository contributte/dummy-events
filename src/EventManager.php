<?php

namespace TomasKubat\Nette\Events;

use Nette\DI\Container;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 * @author Tomas Kubat <tomas.kubat@hotmail.com>
 */
class EventManager
{

    /** @var array */
    protected $monitors = [];

    /** @var array */
    protected $lazyMonitors = [];

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
     * Fire event
     *
     * @param IEvent $event
     */
    public function push(IEvent $event)
    {
        $this->connectLazyMonitors($event);

        if (!isset($this->monitors[$event->getName()])) {
            return;
        }

        foreach ($this->monitors[$event->getName()] as $monitor) {
            if (!$this->isEventContextMonitored($monitor['context'], $event->getContext())) {
                continue;
            }
            call_user_func_array($monitor['callback'], func_get_args());
        }
    }

    /**
     * Connect lazy listeners
     *
     * @param IEvent $event
     */
    private function connectLazyMonitors($event)
    {
        $eventName = $event->getName();

        if (!isset($this->lazyMonitors[$eventName])) {
            return;
        }

        foreach ($this->lazyMonitors[$eventName] as $serviceName => $monitorParameters) {
            if (!$this->isEventContextMonitored($monitorParameters['context'], $event->getContext())) {
                continue;
            }
            $this->monitors[$eventName][] = array(
                'context' => $monitorParameters['context'],
                'callback' => array($this->container->getService($serviceName), $monitorParameters['callback'])
            );
            unset($this->lazyMonitors[$eventName][$serviceName]);
        }
    }

    /**
     * @param string $monitorContext
     * @param string $eventContext
     * @return bool
     */
    private function isEventContextMonitored($monitorContext, $eventContext)
    {
        return ($monitorContext == $eventContext || $monitorContext == '*');
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
            $this->lazyMonitors[$tag][$service] = $value;
        }
    }

}

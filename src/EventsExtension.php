<?php

namespace TomasKubat\Nette\Events;

use Nette\DI\CompilerExtension;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 * @author Tomas Kubat <tomas.kubat@hotmail.com>
 */
class EventsExtension extends CompilerExtension
{

    /**
     * Processes configuration data. Intended to be overridden by descendant.
     *
     * @return void
     */
    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('manager'))
            ->setClass('TomasKubat\Nette\Events\EventManager');
    }

    /**
     * Adjusts DI container before is compiled to PHP class. Intended to be overridden by descendant.
     *
     * @return void
     */
    public function beforeCompile()
    {
        $container = $this->getContainerBuilder();

        $manager = $container->getDefinition($this->prefix('manager'));

        // Gets all services which implement IEventMonitor
        foreach ($container->findByType('TomasKubat\Nette\Events\IEventMonitor') as $serviceName => $subscriberService) {
            if (count($subscriberService->getTags()) === 0) {
                continue;
            }
            // Attach to manager as lazy listener
            $manager->addSetup('attachLazy', [$subscriberService->getTags(), $serviceName]);
        }
    }
}

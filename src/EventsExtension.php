<?php

namespace Minetro\Events;

use Nette\DI\CompilerExtension;

/**
 * EventsExtension
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
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
            ->setClass('Minetro\Events\EventsManager');
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

        // Gets all services which implement EventsSubscriber
        foreach ($container->findByType('Minetro\Events\EventsSubscriber') as $name => $subscriber) {
            if (count($subscriber->getTags()) > 0) {
                // Attach to manager as lazy listener
                $manager->addSetup('attachLazy', [$subscriber->getTags(), $name]);
            } else {
                // Attach listener to manager
                $manager->addSetup('attach', [$subscriber]);
            }
        }
    }
}

<?php

namespace Minetro\Events;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;

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
        foreach ($container->findByType('Minetro\Events\EventsSubscriber') as $def) {
            // Attach to manager
            $manager->addSetup('attach', [$def]);
        }
    }

    /**
     * Adjusts DI container compiled to PHP class. Intended to be overridden by descendant.
     *
     * @return void
     */
    public function afterCompile(ClassType $class)
    {
    }

}

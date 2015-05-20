<?php

namespace Minetro\Events;

use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\Utils\Strings;

/**
 * EventsExtension
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class EventsExtension extends CompilerExtension
{

    /** Constants */
    const EVENT_TAG_NAME= 'events';
    const EVENT_TAG_PREFIX = 'event';

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
            $tags = $this->parseSubscriberTags($subscriber);
            if (count($tags) > 0) {
                // Attach to manager as lazy listener
                $manager->addSetup('attachLazy', [$tags, $name]);
            } else {
                // Attach listener to manager
                $manager->addSetup('attach', [$subscriber]);
            }
        }
    }

    /**
     * @param ServiceDefinition $service
     * @return array
     */
    private function parseSubscriberTags(ServiceDefinition $service)
    {
        $tags = $service->getTags();

        // Array contains EVENT_TAG_NAME key
        if (isset($tags[self::EVENT_TAG_NAME]) && is_array($tags[self::EVENT_TAG_NAME])) {
            return $tags[self::EVENT_TAG_NAME];
        }

        // Array contains other tags
        $etags = [];
        foreach ($service->getTags() as $tag => $value) {
            if (Strings::startsWith($tag, self::EVENT_TAG_PREFIX)) {
                $etags[] = trim(str_replace(self::EVENT_TAG_PREFIX, NULL, $tag), '.:');
            }
        }

        return $etags;
    }
}

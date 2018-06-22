<?php declare(strict_types = 1);

namespace Contributte\Events\DI;

use Contributte\Events\EventsManager;
use Contributte\Events\EventsSubscriber;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;

class EventsExtension extends CompilerExtension
{

	public const EVENT_TAG_NAME = 'events';
	public const EVENT_TAG_PREFIX = 'event';

	public function loadConfiguration(): void
	{
		$container = $this->getContainerBuilder();

		$container->addDefinition($this->prefix('manager'))
			->setClass(EventsManager::class);
	}

	public function beforeCompile(): void
	{
		$container = $this->getContainerBuilder();

		$manager = $container->getDefinition($this->prefix('manager'));

		// Gets all services which implement EventsSubscriber
		foreach ($container->findByType(EventsSubscriber::class) as $name => $subscriber) {
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
	 * @return string[]
	 */
	private function parseSubscriberTags(ServiceDefinition $service): array
	{
		$tags = $service->getTags();

		// Array contains EVENT_TAG_NAME key
		if (isset($tags[self::EVENT_TAG_NAME]) && is_array($tags[self::EVENT_TAG_NAME])) {
			return $tags[self::EVENT_TAG_NAME];
		}

		// Array contains other tags
		$etags = [];
		foreach ($tags as $tag => $value) {
			if (strncmp($tag, self::EVENT_TAG_PREFIX, strlen(self::EVENT_TAG_PREFIX)) === 0) {
				$etags[] = trim(substr($tag, strlen(self::EVENT_TAG_PREFIX)), '.:');
			}
		}

		return $etags;
	}

}

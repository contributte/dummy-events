<?php declare(strict_types = 1);

namespace Contributte\DummyEvents;

use Nette\DI\Container;

class EventsManager
{

	/** @var callable[] */
	protected $listeners = [];

	/** @var callable[] */
	protected $lazyListeners = [];

	/** @var bool[] */
	protected $attachedServices = [];

	/** @var Container */
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Register event
	 */
	public function on(string $event, callable $handler): void
	{
		$this->listeners[$event][] = $handler;
	}

	/**
	 * Fire events
	 */
	public function trigger(string $event): void
	{
		if (isset($this->lazyListeners[$event])) {
			foreach ($this->lazyListeners[$event] as $name) {
				if (!isset($this->attachedServices[$name])) {
					$this->attach($this->container->getService($name));
					$this->attachedServices[$name] = true;
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
	 * Attach an EventSubscriber
	 */
	public function attach(EventsSubscriber $subscriber): void
	{
		$subscriber->onEvents($this);
	}

	/**
	 * Attach lazy subscriber and register events
	 *
	 * @param string[] $events
	 */
	public function attachLazy(iterable $events, string $service): void
	{
		foreach ($events as $id => $event) {
			$this->lazyListeners[$event][] = $service;
		}
	}

}

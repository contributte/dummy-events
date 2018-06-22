<?php declare(strict_types = 1);

namespace Contributte\Events;

interface EventsSubscriber
{

	public function onEvents(EventsManager $em): void;

}

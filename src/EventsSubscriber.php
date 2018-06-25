<?php declare(strict_types = 1);

namespace Contributte\DummyEvents;

interface EventsSubscriber
{

	public function onEvents(EventsManager $em): void;

}

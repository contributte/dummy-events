<?php declare(strict_types = 1);

namespace Tests\Fixtures\Mocks\Subscribers;

use Contributte\DummyEvents\EventsManager;
use Contributte\DummyEvents\EventsSubscriber;
use Nette\InvalidArgumentException;

class MockTestSubscriber implements EventsSubscriber
{

	public function onEvents(EventsManager $em): void
	{
		$em->on('event', function ($param): void {
			throw new InvalidArgumentException('Given param ' . $param);
		});
	}

}

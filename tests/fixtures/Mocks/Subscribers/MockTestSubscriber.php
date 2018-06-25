<?php declare(strict_types = 1);

namespace Tests\Fixtures\Mocks\Subscribers;

use Contributte\Events\EventsManager;
use Contributte\Events\EventsSubscriber;
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

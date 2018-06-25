<?php declare(strict_types = 1);

namespace Tests\Fixtures\Mocks\Subscribers;

use Contributte\Events\EventsManager;
use Contributte\Events\EventsSubscriber;
use Nette\InvalidStateException;

final class MockExceptionSubscriber implements EventsSubscriber
{

	public function onEvents(EventsManager $em): void
	{
		throw new InvalidStateException();
	}

}

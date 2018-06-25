<?php declare(strict_types = 1);

namespace Tests\Fixtures\Mocks\Subscribers;

use Contributte\DummyEvents\EventsManager;
use Contributte\DummyEvents\EventsSubscriber;
use Nette\InvalidStateException;

final class MockExceptionSubscriber implements EventsSubscriber
{

	public function onEvents(EventsManager $em): void
	{
		throw new InvalidStateException();
	}

}

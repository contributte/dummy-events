<?php declare(strict_types = 1);

namespace Tests\Fixtures\Mocks\Subscribers;

use Contributte\DummyEvents\EventsManager;
use Contributte\DummyEvents\EventsSubscriber;

final class MockSubscriber implements EventsSubscriber
{

	/** @var int */
	public $calls = 0;

	/** @var int */
	public $innerCalls = 0;

	public function onEvents(EventsManager $em): void
	{
		$this->calls++;
		$em->on('tester.test1', function () {
			$this->innerCalls++;
			return ['tester.test1' => func_get_args()];
		});
		$em->on('tester.test2', function () {
			$this->innerCalls++;
			return ['tester.test2' => func_get_args()];
		});
	}

}

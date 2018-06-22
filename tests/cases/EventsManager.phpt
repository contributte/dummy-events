<?php declare(strict_types = 1);

use Contributte\Events\DI\EventsExtension;
use Contributte\Events\EventsManager;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\InvalidStateException;
use Tester\Assert;
use Tester\FileMock;
use Tests\Fixtures\Mocks\DI\MockContainer;
use Tests\Fixtures\Mocks\Subscribers\MockSubscriber;

require __DIR__ . '/../bootstrap.php';


test(function (): void {
	$em = new EventsManager(new MockContainer());

	$fparam = null;
	$em->on('event', function ($param) use (&$fparam): void {
		$fparam = $param;
	});

	$param = 'test';
	$em->trigger('event', $param);

	Assert::same($param, $fparam);
});

test(function (): void {
	$em = new EventsManager(new MockContainer());

	$fparam = null;
	$em->on('event.event', function ($param) use (&$fparam): void {
		$fparam = $param;
	});

	$param = 'test';
	$em->trigger('event', $param);

	Assert::notEqual($param, $fparam);
});

test(function (): void {
	Assert::throws(function (): void {
		$em = new EventsManager(new MockContainer());
		$em->on('event', function ($param1): void {
		});

		$em->trigger('event');
	}, ArgumentCountError::class);
});

test(function (): void {
	$configurator = new Configurator();
	$configurator->setTempDirectory(TEMP_DIR);
	$configurator->addConfig(FileMock::create('
services:
	service: \Tests\Fixtures\Mocks\Subscribers\MockExceptionSubscriber
', 'neon'));

	$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
		$compiler->addExtension('events', new EventsExtension());
	};

	$context = $configurator->createContainer();

	Assert::throws(function () use ($context): void {
		$em = $context->getByType(EventsManager::class);
	}, InvalidStateException::class);
});

test(function (): void {
	$configurator = new Configurator();
	$configurator->setTempDirectory(TEMP_DIR);
	$configurator->addConfig(FileMock::create('
services:
	- {class: \Tests\Fixtures\Mocks\Subscribers\MockSubscriber, tags: [events: [tester.test1, tester.test2]]}
', 'neon'));

	$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
		$compiler->addExtension('events', new EventsExtension());
	};

	$context = $configurator->createContainer();
	$em = $context->getByType(EventsManager::class);
	$subscriber = $context->getByType(MockSubscriber::class);

	Assert::equal(0, $subscriber->calls);
	Assert::equal(0, $subscriber->innerCalls);

	$em->trigger('tester.test1');

	Assert::equal(1, $subscriber->calls);
	Assert::equal(1, $subscriber->innerCalls);

	$em->trigger('tester.test2');

	Assert::equal(1, $subscriber->calls);
	Assert::equal(2, $subscriber->innerCalls);
});

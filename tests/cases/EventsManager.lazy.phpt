<?php declare(strict_types = 1);

use Contributte\Events\DI\EventsExtension;
use Contributte\Events\EventsManager;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\InvalidStateException;
use Tester\Assert;
use Tester\FileMock;

require __DIR__ . '/../bootstrap.php';

test(function (): void {
	$configurator = new Configurator();
	$configurator->setTempDirectory(TEMP_DIR);
	$configurator->addConfig(FileMock::create('
services:
	service1: {class: \Tests\Fixtures\Mocks\Subscribers\MockExceptionSubscriber, tags: [events: [test.create]]}
	service2: {class: \Tests\Fixtures\Mocks\Subscribers\MockExceptionSubscriber, tags: [event.test.update]}
', 'neon'));

	$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
		$compiler->addExtension('events', new EventsExtension());
	};

	$context = $configurator->createContainer();
	$em = $context->getByType(EventsManager::class);

	Assert::throws(function () use ($em): void {
		$em->trigger('test.create', time());
	}, InvalidStateException::class);

	Assert::throws(function () use ($em): void {
		$em->trigger('test.update', time());
	}, InvalidStateException::class);
});

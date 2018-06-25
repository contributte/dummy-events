<?php declare(strict_types = 1);

use Contributte\Events\DI\EventsExtension;
use Contributte\Events\EventsManager;
use Contributte\Events\EventsSubscriber;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Tester\Assert;
use Tester\FileMock;

require __DIR__ . '/../../bootstrap.php';

test(function (): void {
	$configurator = new Configurator();
	$configurator->setTempDirectory(TEMP_DIR);
	$configurator->addConfig(FileMock::create('
services:
	test: {class: \Tests\Fixtures\Mocks\Subscribers\MockTestSubscriber, tags: [events: [event]]}
', 'neon'));

	$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
		$compiler->addExtension('events', new EventsExtension());
	};

	/** @var Container $container */
	$container = $configurator->createContainer();

	/** @var EventsManager $em */
	$em = $container->getByType(EventsManager::class);

	Assert::count(1, $container->findByType(EventsSubscriber::class));
	Assert::throws(function () use ($em): void {
		$em->trigger('event', 'test');
	}, InvalidArgumentException::class, 'Given param test');
});

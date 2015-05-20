<?php

/**
 * Test: EventsManager - lazy attaching
 */

use Minetro\Events\EventsExtension;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\InvalidStateException;
use Tester\Assert;
use Tester\FileMock;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../mocks.php';

test(function () {
    $configurator = new Configurator();
    $configurator->setTempDirectory(TEMP_DIR);
    $configurator->addConfig(FileMock::create('
services:
	service1: {class: MockExceptionSubscriber, tags: [events: [test.create]]}
	service2: {class: MockExceptionSubscriber, tags: [event.test.update]}
', 'neon'));

    $configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) {
        $compiler->addExtension('events', new EventsExtension());
    };

    $context = $configurator->createContainer();
    $em = $context->getByType('Minetro\Events\EventsManager');

    Assert::throws(function () use ($em) {
        $em->trigger('test.create', time());
    }, 'Nette\InvalidStateException');

    Assert::throws(function () use ($em) {
        $em->trigger('test.update', time());
    }, 'Nette\InvalidStateException');

});

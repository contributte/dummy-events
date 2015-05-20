<?php

/**
 * Test: EventsManager
 */

use Minetro\Events\EventsExtension;
use Minetro\Events\EventsManager;
use Minetro\Events\EventsSubscriber;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\InvalidStateException;
use Tester\Assert;
use Tester\FileMock;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../mocks.php';

test(function () {
    $em = new EventsManager(new MockContainer());

    $fparam = NULL;
    $em->on('event', function ($param) use (&$fparam) {
        $fparam = $param;
    });

    $param = 'test';
    $em->trigger('event', $param);

    Assert::same($param, $fparam);
});

test(function () {
    $em = new EventsManager(new MockContainer());

    $fparam = NULL;
    $em->on('event.event', function ($param) use (&$fparam) {
        $fparam = $param;
    });

    $param = 'test';
    $em->trigger('event', $param);

    Assert::notEqual($param, $fparam);
});

test(function () {
    Assert::error(function () {
        $em = new EventsManager(new MockContainer());
        $em->on('event', function ($param1) {
        });

        $em->trigger('event');
    }, E_WARNING, 'Missing argument 1 for {closure}()');
});

test(function () {
    $configurator = new Configurator();
    $configurator->setTempDirectory(TEMP_DIR);
    $configurator->addConfig(FileMock::create('
services:
	service: MockExceptionSubscriber
', 'neon'));

    $configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) {
        $compiler->addExtension('events', new EventsExtension());
    };

    $context = $configurator->createContainer();

    Assert::throws(function () use ($context) {
        $em = $context->getByType('Minetro\Events\EventsManager');
    }, 'Nette\InvalidStateException');
});

test(function () {
    $configurator = new Configurator();
    $configurator->setTempDirectory(TEMP_DIR);
    $configurator->addConfig(FileMock::create('
services:
	- {class: MockSubscriber, tags: [events: [tester.test1, tester.test2]]}
', 'neon'));

    $configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) {
        $compiler->addExtension('events', new EventsExtension());
    };

    $context = $configurator->createContainer();
    $em = $context->getByType('Minetro\Events\EventsManager');
    $subscriber = $context->getByType('MockSubscriber');

    Assert::equal(0, $subscriber->calls);
    Assert::equal(0, $subscriber->innerCalls);

    $em->trigger('tester.test1');

    Assert::equal(1, $subscriber->calls);
    Assert::equal(1, $subscriber->innerCalls);

    $em->trigger('tester.test2');

    Assert::equal(1, $subscriber->calls);
    Assert::equal(2, $subscriber->innerCalls);
});

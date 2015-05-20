<?php

/**
 * Test: EventsExtension
 */

use Minetro\Events\EventsExtension;
use Minetro\Events\EventsManager;
use Minetro\Events\EventsSubscriber;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\FileMock;

require __DIR__ . '/../bootstrap.php';

class TestSubcriber implements EventsSubscriber
{

    /**
     * @param EventsManager $em
     */
    function onEvents(EventsManager $em)
    {
        $em->on('event', function ($param) {
            throw new InvalidArgumentException('Given param ' . $param);
        });
    }

}

test(function () {
    $loader = new ContainerLoader(TEMP_DIR);
    $class = $loader->load('', function (Compiler $compiler) {
        $compiler->addExtension('events', new EventsExtension());
        $compiler->addConfig(['services' => ['test' => 'TestSubcriber']]);
    });
    /** @var Container $container */
    $container = new $class;

    /** @var EventsManager $manager */
    $manager = $container->getByType('Minetro\Events\EventsManager');

    Assert::count(1, $container->findByType('Minetro\Events\EventsSubscriber'));
    Assert::throws(function () use ($manager) {
        $manager->trigger('event', 'test');
    }, 'InvalidArgumentException', 'Given param test');
});

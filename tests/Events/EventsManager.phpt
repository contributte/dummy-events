<?php

/**
 * Test: EventsManager
 */

use Minetro\Events\EventsManager;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

test(function () {
    $em = new EventsManager();

    $fparam = NULL;
    $em->on('event', function ($param) use (&$fparam) {
        $fparam = $param;
    });

    $param = 'test';
    $em->trigger('event', $param);

    Assert::same($param, $fparam);
});

test(function () {
    $em = new EventsManager();

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
        $em = new EventsManager();
        $em->on('event', function ($param1) {
        });

        $em->trigger('event');
    }, E_WARNING, 'Missing argument 1 for {closure}()');
});

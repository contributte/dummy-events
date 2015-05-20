<?php

use Minetro\Events\EventsManager;
use Minetro\Events\EventsSubscriber;
use Nette\DI\Container;
use Nette\InvalidStateException;

/**
 * CONTAINERS **************************************************************
 */
final class MockContainer extends Container
{
}

/**
 * SUBSCRIBERS **************************************************************
 */
final class MockSubscriber implements EventsSubscriber
{

    /** @var int */
    public $calls = 0;

    /** @var int */
    public $innerCalls = 0;

    /**
     * @param EventsManager $em
     */
    public function onEvents(EventsManager $em)
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

final class MockExceptionSubscriber implements EventsSubscriber
{

    /**
     * @param EventsManager $em
     * @throws InvalidStateException
     */
    public function onEvents(EventsManager $em)
    {
        throw new InvalidStateException();
    }
}
<?php

namespace Minetro\Events;

/**
 * EventsSubscriber
 *
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface EventsSubscriber
{

    /**
     * @param EventsManager $em
     */
    function onEvents(EventsManager $em);

}

<?php

namespace TomasKubat\Nette\Events;

/**
 * @author Tomas Kubat <tomas.kubat@hotmail.com>
 */
interface IEvent
{
    public function getName();
    
    public function getContext();

    public function getParameters();
}

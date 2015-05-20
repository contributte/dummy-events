<?php

namespace TomasKubat\Nette\Events;

/**
 * @author Tomas Kubat <tomas.kubat@hotmail.com>
 */
class Event extends \Nette\Object implements IEvent
{

    /** @var string */
    private $name;

    /** @var string */
    private $context;

    /** @var array */
    private $parameters = array();

    /**
     * @param string $name
     * @param string $context
     * @param array $parameters
     */
    function __construct($name, $context, $parameters = array())
    {
        list($this->name, $this->context, $this->parameters) = func_get_args();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
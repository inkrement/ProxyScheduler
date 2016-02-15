<?php

namespace Inkrement\ProxyScheduler\Exception;

use InvalidArgumentException;

class InitException extends InvalidArgumentException
{
    private $name;

    private $data;

    public function __construct($name, array $data = [], $message = '')
    {
        parent::__construct($message);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        return $this->data;
    }
}

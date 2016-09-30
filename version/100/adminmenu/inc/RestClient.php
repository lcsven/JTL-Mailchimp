<?php

class RestClient
{
    public function __construct()
    {
        return $this;
    }

    public function getArray()
    {
        return(array('foo' => 'bar', 'baz' => null));
    }
}

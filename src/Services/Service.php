<?php

namespace Jsanbae\SIIAPI\Services;

abstract class Service
{
    protected $endpoint;

    public function __construct($_endpoint)
    {
        $this->endpoint = $_endpoint;
    }
}

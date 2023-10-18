<?php

namespace Jsanbae\SIIAPI\Entities;

class LibroCompraDocumento 
{
    private $data = [];
    
    public function __construct(object $_data_from_endpoint)
    {
        $this->data = $_data_from_endpoint;
    }
    
}
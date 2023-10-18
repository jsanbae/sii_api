<?php

namespace Jsanbae\SIIAPI\Entities;

use Jsanbae\SIIAPI\Contracts\Arrayable;
use Jsanbae\SIIAPI\Contracts\Libro;
use Jsanbae\SIIAPI\Contracts\LibroDetalle;
use Jsanbae\SIIAPI\Contracts\LibroResumen;

class LibroVenta implements Libro, Arrayable
{
    private $libro = [];

    public function __construct(LibroResumen $_resumen, LibroDetalle $_detalle)
    {
        $this->libro = [
            'resumen' => $_resumen,
            'detalle' => $_detalle,
        ];
    }

    public function resumen():LibroResumen
    {
        return $this->libro['resumen'];
    }

    public function detalle():LibroDetalle
    {
        return $this->libro['detalle'];
    }
    
    public function isEmpty():bool 
    {
        return empty($this->libro['detalle']->toArray());
    }
    
    public function toArray():array
    {
        return [
            'resumen' => $this->libro['resumen']->toArray(),
            'detalle' => $this->libro['detalle']->toArray(),
        ];
    }

}

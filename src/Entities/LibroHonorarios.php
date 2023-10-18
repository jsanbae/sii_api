<?php

namespace Jsanbae\SIIAPI\Entities;

use Jsanbae\SIIAPI\Contracts\Libro;
use Jsanbae\SIIAPI\Contracts\Arrayable;
use Jsanbae\SIIAPI\Contracts\LibroDetalle;
use Jsanbae\SIIAPI\Contracts\LibroResumen;

class LibroHonorarios implements Libro, Arrayable
{
    private $resumen;
    private $detalle;

    public function __construct(LibroResumen $_resumen, LibroDetalle $_detalle)
    {
        $this->resumen = $_resumen;
        $this->detalle = $_detalle;
    }
    public function resumen(): LibroResumen
    {   
        return $this->resumen;
    }

    public function detalle(): LibroDetalle
    {
        return $this->detalle;
    }

    public function toArray(): array
    {
        return [
            'resumen' => $this->resumen()->toArray(),
            'detalle' => $this->detalle()->toArray()
        ];
    }

    public function isEmpty(): bool
    {
        return $this->detalle()->isEmpty();
    }
}

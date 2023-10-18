<?php

namespace Jsanbae\SIIAPI\Contracts;

interface Libro
{
    public function resumen(): LibroResumen;
    public function detalle(): LibroDetalle;
    public function isEmpty():bool;
}

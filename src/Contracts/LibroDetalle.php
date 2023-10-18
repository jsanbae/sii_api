<?php

namespace Jsanbae\SIIAPI\Contracts;

interface LibroDetalle
{
    public function add(int $doc_code, array $_data_from_endpoint):void;
    public function getTypes():array;
}

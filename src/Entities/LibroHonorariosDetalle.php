<?php

namespace Jsanbae\SIIAPI\Entities;

use Jsanbae\SIIAPI\Contracts\LibroDetalle;
use Jsanbae\SIIAPI\Contracts\Arrayable;

class LibroHonorariosDetalle implements LibroDetalle, Arrayable
{
    private $data = [];

    public function add(int $_doc_code, array $_data_from_endpoint):void
    {
        $this->data[$_doc_code] = $_data_from_endpoint;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function getTypes(): array
    {
        return array_keys($this->data);
    }
}

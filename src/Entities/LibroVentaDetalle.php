<?php

namespace Jsanbae\SIIAPI\Entities;

use Jsanbae\SIIAPI\Constants\RCV;
use Jsanbae\SIIAPI\Contracts\Arrayable;
use Jsanbae\SIIAPI\Contracts\LibroDetalle;

class LibroVentaDetalle implements LibroDetalle, Arrayable
{
    private $data = [];

    public function add(int $doc_code, array $_data_from_endpoint, string $_namespace = null):void
    {
        if (!is_null($_namespace) && $_namespace !== RCV::LIBRO_VENTAS_DETALLE_NAMESPACE) {
            throw new \InvalidArgumentException('Data from endpoint is not valid');
        }

        $this->data[$doc_code] = $this->populate($doc_code, $_data_from_endpoint);
    }
    
    private function populate(int $_doc_code, array $_data_from_endpoint):array
    {
        $docs = $_data_from_endpoint;
        $data_detalle = [];

        foreach ($docs as $doc) {
            $doc->detCodigoTipoDoc = $_doc_code;
            $data_detalle[] = $doc;
        }

        return $data_detalle;
    }

    public function getTypes():array
    {
        return array_keys($this->data);
    }

    public function toArray():array
    {
        return $this->data;
    }
}

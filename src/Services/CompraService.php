<?php

namespace Jsanbae\SIIAPI\Services;

use Jsanbae\SIIAPI\Contracts\Endpoint;
use Jsanbae\SIIAPI\Contracts\Libro;
use Jsanbae\SIIAPI\Entities\LibroCompra;
use Jsanbae\SIIAPI\Entities\LibroCompraResumen;
use Jsanbae\SIIAPI\Entities\LibroCompraDetalle;

class CompraService extends Service implements RCVService
{

    public function __construct(Endpoint $_endpoint)
    {
        parent::__construct($_endpoint);
    }

    public function Libro(int $_periodo, int $_mes): Libro
    {
        $compras_resumen_response = $this->endpoint->LibroResumen($_periodo, $_mes);
        $body_resumen = json_decode($compras_resumen_response->getBody()->getContents());
        $resumen = new LibroCompraResumen($body_resumen);
        
        $detalle = new LibroCompraDetalle();
        
        if (isset($resumen->toArray()['documentos']) && !empty($resumen->toArray()['documentos'])) {
            $docs_tipos = array_keys($resumen->toArray()['documentos']);
            foreach ($docs_tipos as $doc_tipo) {
                $compras_detalle_response = $this->endpoint->LibroDetalleByDocType($doc_tipo, $_periodo, $_mes);
                $body_detalle = json_decode($compras_detalle_response->getBody()->getContents());

                if (is_null($body_detalle->data)) continue;

                $detalle->add($doc_tipo, $body_detalle->data, $body_detalle->metaData->namespace);
            }
        }

        $libro = new LibroCompra($resumen, $detalle);

        return $libro;
    }
}

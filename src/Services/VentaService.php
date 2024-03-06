<?php

namespace Jsanbae\SIIAPI\Services;

use Jsanbae\SIIAPI\Contracts\Endpoint;
use Jsanbae\SIIAPI\Contracts\Libro;
use Jsanbae\SIIAPI\Entities\LibroVenta;
use Jsanbae\SIIAPI\Entities\LibroVentaResumen;
use Jsanbae\SIIAPI\Entities\LibroVentaDetalle;

class VentaService extends Service implements RCVService
{

    public function __construct(Endpoint $_endpoint)
    {
        parent::__construct($_endpoint);
    }

    public function Libro(int $_periodo, int $_mes): Libro
    {
        $ventas_resumen_response = $this->endpoint->LibroResumen($_periodo, $_mes);
        $body_resumen = json_decode($ventas_resumen_response->getBody()->getContents());
        $resumen = new LibroVentaResumen($body_resumen);
        
        $detalle = new LibroVentaDetalle();
        
        if (isset($resumen->toArray()['documentos']) && !empty($resumen->toArray()['documentos'])) {
            $docs_tipos = array_keys($resumen->toArray()['documentos']);
            foreach ($docs_tipos as $doc_tipo) {
                $ventas_detalle_response = $this->endpoint->LibroDetalleByDocType($doc_tipo, $_periodo, $_mes);
                $body_detalle = json_decode($ventas_detalle_response->getBody()->getContents());

                if (is_null($body_detalle->data)) continue;

                $detalle->add($doc_tipo, $body_detalle->data, $body_detalle->metaData->namespace);
            }
        }

        $libro = new LibroVenta($resumen, $detalle);

        return $libro;
    }
}

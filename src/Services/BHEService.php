<?php

namespace Jsanbae\SIIAPI\Services;

use Jsanbae\SIIAPI\Contracts\Libro;
use Jsanbae\SIIAPI\Services\Service;
use Jsanbae\SIIAPI\Constants\DocTypes;
use Jsanbae\SIIAPI\Entities\LibroHonorarios;
use Jsanbae\SIIAPI\Entities\LibroHonorariosDetalle;
use Jsanbae\SIIAPI\Entities\LibroHonorariosResumen;
use Jsanbae\SIIAPI\DomParser\BHEInformeBoletasRecibidasParser;
use Jsanbae\SIIAPI\Concerns\Barcode;
use Jsanbae\SIIAPI\Concerns\Comunas;

class BHEService extends Service
{
    use Barcode, Comunas;

    public function __construct($_endpoint)
    {
        parent::__construct($_endpoint);
    }

    public function LibroHonorarios(int $_periodo, int $_mes):Libro
    {
        $response = $this->endpoint->InformeBoletasRecibidas($_periodo, $_mes);
        $body = $response->getBody()->getContents();

        $data = (new BHEInformeBoletasRecibidasParser($body))();
        $data = $this->data_with_binary_pdf($data);

        $resumen = new LibroHonorariosResumen($data);
        $detalle = new LibroHonorariosDetalle();
        $detalle->add(DocTypes::BOLETA_HONORIARIOS_ELECTRONICA, $data);

        $libro = new LibroHonorarios($resumen, $detalle);

        return $libro;
    }

    public function BoletaRecibidaPDFBinary(string $_codigo_barras, string $_nombre_comuna)
    {
        $cod39 = $this->ConvertToCode39($_codigo_barras, 0);
        $response = $this->endpoint->BoletaRecibidaPDF($_codigo_barras, $cod39, $_nombre_comuna);
        $body = $response->getBody()->getContents();

        return $body;
    }

    private function data_with_binary_pdf($_data):array
    {
        $data = $_data;

        for ($i = 0; $i < count($data); $i++) {
            $doc = $data[$i];
            $codigo_barra = $doc['codigobarras'];
            $comuna_nombre = $this->getComunaByCode((int) $doc['cod_comuna']);
            $pdf_binary = $this->BoletaRecibidaPDFBinary($codigo_barra, $comuna_nombre);
            // $data[$i]['pdf_binary'] = base64_encode($pdf_binary);
            $data[$i]['pdf_binary'] = $pdf_binary;
        }

        return $data;
    }
}

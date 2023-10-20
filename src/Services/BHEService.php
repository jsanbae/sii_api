<?php

namespace Jsanbae\SIIAPI\Services;

use Jsanbae\SIIAPI\Concerns\Barcode;
use Jsanbae\SIIAPI\Concerns\Comunas;
use Jsanbae\SIIAPI\Concerns\BoletaHonorarioMapper;
use Jsanbae\SIIAPI\Constants\DocTypes;
use Jsanbae\SIIAPI\Contracts\Libro;
use Jsanbae\SIIAPI\DomParser\BTEInformeEmitidasParser;
use Jsanbae\SIIAPI\DomParser\BTEInformeEmitidasSummaryParser;
use Jsanbae\SIIAPI\DomParser\BHEInformeRecibidasParser;
use Jsanbae\SIIAPI\Entities\LibroHonorarios;
use Jsanbae\SIIAPI\Entities\LibroHonorariosDetalle;
use Jsanbae\SIIAPI\Entities\LibroHonorariosResumen;
use Jsanbae\SIIAPI\Services\Service;

class BHEService extends Service
{
    use Barcode, Comunas, BoletaHonorarioMapper;

    public function __construct($_endpoint)
    {
        parent::__construct($_endpoint);
    }

    public function Libro(int $_periodo, int $_mes):Libro
    {
        $boletas = [];

        $responseBHE = $this->endpoint->InformeBoletasRecibidas($_periodo, $_mes);
        $bodyBHE = $responseBHE->getBody()->getContents();
        $parsed_boletas = $this->mapBoletas((new BHEInformeRecibidasParser($bodyBHE))());

        $boletas = array_merge($boletas, $parsed_boletas);
        
        $responseBTE = $this->endpoint->InformeBTEEmitidas($_periodo, $_mes, 1);
        $bodyBTE = $responseBTE->getBody()->getContents();
        $dataSummary = (new BTEInformeEmitidasSummaryParser($bodyBTE))();
        $parsed_boletas_bte = $this->mapBoletas((new BTEInformeEmitidasParser($bodyBTE))());

        $boletas = array_merge($boletas, $parsed_boletas_bte);

        $current_page = $dataSummary['current_page'];
        $total_pages = $dataSummary['total_pages'];
        while ($current_page < $total_pages) {
            $responseBTE = $this->endpoint->InformeBTEEmitidas($_periodo, $_mes, $current_page + 1);
            $bodyBTE = $responseBTE->getBody()->getContents();
            $parsed_boletas_bte_paginated = $this->mapBoletas((new BTEInformeEmitidasParser($bodyBTE))());

            $boletas = array_merge($boletas, $parsed_boletas_bte_paginated);

            $dataSummary = (new BTEInformeEmitidasSummaryParser($bodyBTE))();
            $current_page = $dataSummary['current_page'];
        }      
        
        // $data = $this->data_with_binary_pdf($data);

        $resumen = new LibroHonorariosResumen($boletas);

        $detalle = new LibroHonorariosDetalle();
        $detalle->add(DocTypes::BOLETA_HONORIARIOS_ELECTRONICA, $boletas);

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

<?php

namespace Jsanbae\SIIAPI\Entities;

use Jsanbae\SIIAPI\Constants\RCV;
use Jsanbae\SIIAPI\Constants\RCVType;
use Jsanbae\SIIAPI\Contracts\Arrayable;
use Jsanbae\SIIAPI\Contracts\LibroResumen;

class LibroVentaResumen implements LibroResumen, Arrayable
{
    private $data = [];

    public function __construct(object $_data_from_endpoint)
    {       
        if ($_data_from_endpoint->metaData->namespace !== RCV::RCV_RESUMEN_NAMESPACE) {
            throw new \InvalidArgumentException('Data from endpoint is not valid');
        } 

        $this->populate($_data_from_endpoint);
    }

    private function populate($_data_from_endpoint):array
    {
        $data_resumen = $_data_from_endpoint->data;

        if (is_null($data_resumen) || empty($data_resumen)) return [];

        $data_cabecera = $_data_from_endpoint->dataCabecera;
        $periodo = (int) substr($data_cabecera->dcvPtributario, 0, 4);
        $mes = (int) substr($data_cabecera->dcvPtributario, 4);

        $total_exento = 0;
        $total_neto = 0;
        $total_iva = 0;
        $total_no_recuperable = 0;
        $total_uso_comun = 0;
        $total_monto = 0;
        $documentos = [];
        
        foreach ($data_resumen as $resumen) {
            $total_exento += $resumen->rsmnMntExe;
            $total_neto += $resumen->rsmnMntNeto;
            $total_iva += $resumen->rsmnMntIVA;
            $total_no_recuperable += $resumen->rsmnMntIVANoRec;
            $total_uso_comun += $resumen->rsmnIVAUsoComun;
            $total_monto += $resumen->rsmnMntTotal;
            
            $documentos[$resumen->rsmnTipoDocInteger]= [
                'nombre' => $resumen->dcvNombreTipoDoc,
                'total_documentos' => $resumen->rsmnTotDoc,
                'monto_exento' => $resumen->rsmnMntExe,
                'monto_neto' => $resumen->rsmnMntNeto,
                'monto_iva' => $resumen->rsmnMntIVA,
                'monto_iva_no_recuperable' => $resumen->rsmnMntIVANoRec,
                'monto_iva_uso_comun' => $resumen->rsmnIVAUsoComun,
                'monto_total' => $resumen->rsmnMntTotal,
            ];
        }

        $this->data = [
            'rut_emisor' => $data_cabecera->dcvRutEmisor,
            'dv_emisor' => $data_cabecera->dcvDvEmisor,
            'tipo' => RCVType::COMPRA,
            'periodo' => $periodo,
            'mes' => $mes,
            'factor_proporcionalidad' => (float) $data_cabecera->dcvFctProp,
            'fecha_creacion' => new \DateTimeImmutable($data_cabecera->dcvFecCreacion),
            'fecha_modificacion' => new \DateTimeImmutable($data_cabecera->dcvFecModificacion),
            'total_documentos' => $_data_from_endpoint->totDocRes,
            'monto_exento' => $total_exento,
            'monto_neto' => $total_neto,
            'monto_iva' => $total_iva,
            'monto_iva_no_recuperable' => $total_no_recuperable,
            'monto_iva_uso_comun' => $total_uso_comun,
            'monto_total' => $total_monto,
            'documentos' => $documentos
        ];

        return $this->data;
    }

    public function toArray():array
    {
        return $this->data;
    }
}

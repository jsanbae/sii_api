<?php

namespace Jsanbae\SIIAPI\Entities;

use Jsanbae\SIIAPI\Contracts\LibroResumen;
use Jsanbae\SIIAPI\Contracts\Arrayable;

class LibroHonorariosResumen implements LibroResumen, Arrayable
{
    private $resumen = [];

    public function __construct(array $_detalle_boletas)
    {       
        $this->populate($_detalle_boletas);
    }

    private function populate(array $_detalle_boletas):array
    {
        $data_resumen = $_detalle_boletas;

        if (is_null($data_resumen) || empty($data_resumen)) return [];

        $vigentes = 0;
        $anulados = 0;
        $bruto = 0;
        $retencion_terceros = 0;
        $retencion_contribuyente = 0;
        $pagado = 0;

        foreach ($data_resumen as $boleta) {
            $vigentes = ($boleta->isVigente()) ? $vigentes + 1 : $vigentes;
            $anulados = (!$boleta->isVigente()) ? $anulados + 1 : $anulados;
            $bruto += $boleta->getBruto();
            $retencion_terceros += $boleta->getRetencion();
            $pagado += $boleta->getPagado();            
        }
        
        $this->resumen[] = [
            'cantidad_documentos' => count($data_resumen),
            'vigentes' => $vigentes,
            'anulados' => $anulados,
            'bruto' => $bruto,
            'retencion_terceros' => $retencion_terceros,
            'retencion_contribuyente' => $retencion_contribuyente,
            'pagado' => $pagado
        ];
        
        return $this->resumen;
    }

    public function toArray(): array
    {
        return $this->resumen;
    }
    
}

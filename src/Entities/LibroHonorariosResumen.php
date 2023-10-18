<?php

namespace Jsanbae\SIIAPI\Entities;

use Jsanbae\SIIAPI\Contracts\LibroResumen;
use Jsanbae\SIIAPI\Contracts\Arrayable;

class LibroHonorariosResumen implements LibroResumen, Arrayable
{
    private $resumen = [];

    public function __construct(array $_data_from_endpoint)
    {       
        $this->populate($_data_from_endpoint);
    }

    private function populate(array $_data_from_endpoint):array
    {
        $data_resumen = $_data_from_endpoint;

        if (is_null($data_resumen) || empty($data_resumen)) return [];

        $vigentes = 0;
        $anulados = 0;
        $honorario_bruto = 0;
        $retencion_terceros = 0;
        $retencion_contribuyente = 0;
        $total_liquido = 0;

        foreach ($data_resumen as $data) {
            $vigentes = ($data['estado'] === 'N') ? $vigentes + 1 : $vigentes;
            $anulados = ($data['estado'] !== 'N') ? $anulados + 1 : $anulados;
            $honorario_bruto = $honorario_bruto + $data['totalhonorarios'];
            $retencion_terceros = $retencion_terceros + $data['retencion_receptor'];
            $total_liquido = $total_liquido + $data['honorariosliquidos'];            
        }
        
        $this->resumen[] = [
            'cantidad_documentos' => count($data_resumen),
            'vigentes' => $vigentes,
            'anulados' => $anulados,
            'honoriario_bruto' => $honorario_bruto,
            'retencion_terceros' => $retencion_terceros,
            'retencion_contribuyente' => $retencion_contribuyente,
            'total_liquido' => $total_liquido
        ];
        
        return $this->resumen;
    }

    public function toArray(): array
    {
        return $this->resumen;
    }

}

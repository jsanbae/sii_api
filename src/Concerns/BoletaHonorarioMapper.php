<?php

namespace Jsanbae\SIIAPI\Concerns;

use Jsanbae\SIIAPI\Entities\BoletaHonorario;

trait BoletaHonorarioMapper {

    public function mapBoletas($_boletas):array 
    {
        $boletas = [];
        foreach ($_boletas as $boleta) {
            $boletas[] = $this->mapBoleta($boleta);
        }

        return $boletas;
    }

    public function mapBoleta($boleta):BoletaHonorario 
    {        
        $nro_boleta = isset($boleta['nro_boleta']) ? $boleta['nro_boleta'] : $boleta['nroboleta'];
        $rut_emisor = isset($boleta['rut_emisor']) ? explode('-',$boleta['rut_emisor'])[0] : $boleta['rutemisor'];
        $dv_emisor = isset($boleta['rut_emisor']) ? explode('-',$boleta['rut_emisor'])[1] : $boleta['dvemisor'];
        $bruto = isset($boleta['bruto']) ? $boleta['bruto'] : $boleta['totalhonorarios'];
        $retenido = isset($boleta['retenido']) ? $boleta['retenido'] : $boleta['retencion_receptor'];
        $pagado = isset($boleta['pagado']) ? $boleta['pagado'] : $boleta['honorariosliquidos'];
        
        return new BoletaHonorario(
            $nro_boleta,
            $boleta['estado'],
            $rut_emisor,
            $dv_emisor,
            $boleta['nombre_emisor'],
            $boleta['fecha_boleta'],
            $bruto,
            $retenido,
            $pagado,
            $boleta['es_soc_profesional'] ?? null,
            $boleta['cod_comuna'] ?? null,
            $boleta['fechaanulacion'] ?? null,
            $boleta['codigobarras'] ?? null
        );

    }
}
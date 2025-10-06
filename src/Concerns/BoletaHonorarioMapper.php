<?php

namespace Jsanbae\SIIAPI\Concerns;

use DateTimeImmutable;
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

    // Unify data from BHE and BTE
    public function mapBoleta($boleta):BoletaHonorario 
    {        
        $nro_boleta = isset($boleta['nro_boleta']) ? $boleta['nro_boleta'] : $boleta['nroboleta'];
        $nombre_emisor = isset($boleta['receptor_nombre']) ? $boleta['receptor_nombre'] : $boleta['nombre_emisor'];
        $rut_emisor = isset($boleta['receptor_rut']) ? explode('-',$boleta['receptor_rut'])[0] : $boleta['rutemisor'];
        $dv_emisor = isset($boleta['receptor_rut']) ? explode('-',$boleta['receptor_rut'])[1] : $boleta['dvemisor'];
        $bruto = isset($boleta['bruto']) ? $boleta['bruto'] : $boleta['totalhonorarios'];
        $retenido = isset($boleta['retenido']) ? $boleta['retenido'] : $boleta['retencion_receptor'];
        $pagado = isset($boleta['pagado']) ? $boleta['pagado'] : $boleta['honorariosliquidos'];
        $fecha_anulacion = ($boleta['fechaanulacion']) ? DateTimeImmutable::createFromFormat('d/m/Y', $boleta['fechaanulacion']) : null;
        
        return new BoletaHonorario(
            $nro_boleta,
            $boleta['estado'],
            $rut_emisor,
            $dv_emisor,
            $nombre_emisor,
            $boleta['fecha_boleta'],
            $bruto,
            $retenido,
            $pagado,
            $boleta['es_soc_profesional'] ?? null,
            $boleta['cod_comuna'] ?? null,
            $fecha_anulacion,
            $boleta['codigobarras'] ?? null
        );

    }
}
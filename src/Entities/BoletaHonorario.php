<?php

namespace Jsanbae\SIIAPI\Entities;

use DateTimeImmutable;
use Jsanbae\SIIAPI\Contracts\Arrayable;

class BoletaHonorario implements Arrayable
{
    private $nro_boleta;
    private $estado;
    private $rut_emisor;
    private $dv_emisor;
    private $nombre_emisor;
    private $fecha_boleta;
    private $bruto;
    private $retencion;
    private $pagado;
    private $es_soc_profesional;
    private $cod_comuna;
    private $fecha_anulacion;
    private $codigo_barras;

    public function __construct(
            int $_nro_boleta, 
            string $_estado, 
            int $_rut_emisor, 
            string $_dv_emisor, 
            string $_nombre_emisor, 
            DateTimeImmutable $_fecha_boleta, 
            int $_bruto, 
            int $_retencion, 
            int $_pagado, 
            string $es_soc_profesional = null, 
            int $cod_comuna = null, 
            DateTimeImmutable $fecha_anulacion = null, 
            string $codigo_barras = null
        )
    {
        $this->nro_boleta = $_nro_boleta;
        $this->estado = $_estado;
        $this->rut_emisor = $_rut_emisor;
        $this->dv_emisor = $_dv_emisor;
        $this->nombre_emisor = $_nombre_emisor;
        $this->fecha_boleta = $_fecha_boleta;
        $this->bruto = $_bruto;
        $this->retencion = $_retencion;
        $this->pagado = $_pagado;
        $this->es_soc_profesional = $es_soc_profesional;
        $this->cod_comuna = $cod_comuna;
        $this->fecha_anulacion = $fecha_anulacion;
        $this->codigo_barras = $codigo_barras;
    }

    public function getNroBoleta():int
    {
        return $this->nro_boleta;
    }

    public function getEstado():string
    {
        return $this->estado;
    }

    public function getRutEmisor():int
    {
        return $this->rut_emisor;
    }

    public function getDvEmisor():string
    {
        return $this->dv_emisor;
    }

    public function getNombreEmisor():string
    {
        return $this->nombre_emisor;
    }

    public function getFechaBoleta():DateTimeImmutable
    {
        return $this->fecha_boleta;
    }

    public function getBruto():int
    {
        return $this->bruto;
    }

    public function getRetencion():int
    {
        return $this->retencion;
    }

    public function getPagado():int
    {
        return $this->pagado;
    }

    public function getEsSocProfesional():?string
    {
        return $this->es_soc_profesional;
    }

    public function getCodComuna():?int
    {
        return $this->cod_comuna;
    }

    public function getFechaAnulacion():?DateTimeImmutable
    {
        return $this->fecha_anulacion;
    }

    public function getCodigoBarras():?string
    {
        return $this->codigo_barras;
    }

    public function isVigente():bool
    {
        return in_array($this->getEstado(), ['N', 'VIG']);
    }

    public function toArray():array
    {
        return [
            'nro_boleta' => $this->nro_boleta,
            'estado' => $this->estado,
            'rut_emisor' => $this->rut_emisor,
            'dv_emisor' => $this->dv_emisor,
            'nombre_emisor' => $this->nombre_emisor,
            'fecha_boleta' => $this->fecha_boleta,
            'bruto' => $this->bruto,
            'retencion' => $this->retencion,
            'pagado' => $this->pagado,
            'es_soc_profesional' => $this->es_soc_profesional,
            'cod_comuna' => $this->cod_comuna,
            'fecha_anulacion' => $this->fecha_anulacion,
            'codigo_barras' => $this->codigo_barras,
        ];
    }
}

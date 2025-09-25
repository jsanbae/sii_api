<?php

namespace Jsanbae\SIIAPI\DomParser;

use DOMNode;
use DOMDocument;
use DOMNodeList;
use DateTimeImmutable;
use DOMElement;
use DOMXPath;

class BTEInformeRecibidasParser 
{
    private $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * Parsea el HTML y extrae la información de las boletas
     * @return array
     */
    public function __invoke():array
    {
        return $this->parsearHTML($this->body);
    }

    /**
     * Parsea el HTML y extrae la información de las boletas
     * @param string $html
     * @return array
     */
    public function parsearHTML(string $html): array
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        
        $boletas = [];
        
        // Buscar la tabla que contiene las boletas
        $tablas = $dom->getElementsByTagName('table');
        
        foreach ($tablas as $tabla) {
            // Verificar si esta tabla contiene boletas (buscar encabezados específicos)
            if ($this->esTablaBoletas($tabla)) {
                $boletas = $this->extraerBoletasDesdeTabla($tabla);
                break;
            }
        }
        
        return $boletas;
    }
    
    /**
     * Verifica si una tabla es la que contiene las boletas
     * @param DOMElement $tabla
     * @return bool
     */
    private function esTablaBoletas(DOMElement $tabla): bool
    {
        $contenido = $tabla->textContent;
        return strpos($contenido, 'Boleta') !== false && 
               strpos($contenido, 'Emisor') !== false &&
               strpos($contenido, 'Honorarios') !== false;
    }
    
    /**
     * Extrae las boletas individuales de la tabla
     * @param DOMElement $tabla
     * @return array
     */
    private function extraerBoletasDesdeTabla(DOMElement $tabla): array
    {
        $boletas = [];
        $filas = $tabla->getElementsByTagName('tr');
        
        // Saltar las dos primeras filas (encabezados)
        for ($i = 2; $i < $filas->length; $i++) {
            $fila = $filas->item($i);
            
            // Verificar si es una fila de totales
            if (strpos($fila->textContent, 'Totales') !== false) {
                continue;
            }
            
            $boleta = $this->extraerDatosFila($fila);
            if ($boleta) {
                $boletas[] = $boleta;
            }
        }
        
        return $boletas;
    }
    
    /**
     * Extrae los datos de una fila individual
     * @param DOMElement $fila
     * @return array|null
     */
    private function extraerDatosFila(DOMElement $fila): ?array
    {
        $celdas = $fila->getElementsByTagName('td');
        
        // Una fila válida de boleta debe tener al menos 10 celdas
        if ($celdas->length < 10) {
            return null;
        }
        
        // Extraer enlace de ver boleta
        $enlaces = $fila->getElementsByTagName('a');
        $urlBoleta = '';
        if ($enlaces->length > 0) {
            $enlace = $enlaces->item(0);
            $href = $enlace->getAttribute('href');
            if ($href && strpos($href, 'bte_indiv_cons3') !== false) {
                $urlBoleta = 'https://zeus.sii.cl' . $href;
            }
        }
        
        return [
            'url_ver' => $urlBoleta,
            'numero' => trim($celdas->item(1)->textContent),
            'estado' => trim($celdas->item(2)->textContent),
            'fecha_boleta' => trim($celdas->item(3)->textContent),
            'rut_emisor' => trim($celdas->item(4)->textContent),
            'nombre_emisor' => trim($celdas->item(5)->textContent),
            'fecha_emision' => trim($celdas->item(6)->textContent),
            'honorarios_brutos' => $this->limpiarMonto(trim($celdas->item(7)->textContent)),
            'honorarios_retenidos' => $this->limpiarMonto(trim($celdas->item(8)->textContent)),
            'honorarios_pagados' => $this->limpiarMonto(trim($celdas->item(9)->textContent))
        ];
    }
    
    /**
     * Limpia y convierte montos a formato numérico
     * @param string $monto
     * @return float
     */
    private function limpiarMonto(string $monto): float
    {
        // Remover puntos de miles y convertir coma decimal a punto
        $monto = str_replace('.', '', $monto);
        $monto = str_replace(',', '.', $monto);
        return floatval($monto);
    }
}

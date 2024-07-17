<?php

namespace Jsanbae\SIIAPI\DomParser;

use DOMNode;
use DOMDocument;
use DOMNodeList;
use DateTimeImmutable;

class BTEInformeEmitidasParser
{
    private $body;
    private $fields_configuration = [
        'nro_boleta' => "int", 
        'estado' => "string", 
        'fecha_boleta' => "date", 
        'rut_emisor' => "rut", 
        'nombre_emisor' => "string", 
        'emision_boleta_fecha' => "date", 
        'receptor_rut' => "string", 
        'receptor_nombre' => "string", 
        'bruto' => "int", 
        'retenido' => "int", 
        'pagado' => "int", 
    ];

    public function __construct(string $_body)
    {
        $this->body = $_body;
    }

    public function __invoke():array
    {
        return $this->BTEInformeEmitidasParser();
    }

    private function BTEInformeEmitidasParser()
    {
        $documentos = [];
        
        $sContent = mb_convert_encoding($this->body, 'HTML-ENTITIES', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $doc = new DOMDocument();      
        $doc->loadHTML($sContent);
        libxml_use_internal_errors($internalErrors);

        $tables = $doc->getElementsByTagName('table');
        if ($tables->length == 0) return [];

        // $table = $tables[2];// totales
        $table = $tables[3];// detalle
        $filas = $table->getElementsByTagName('tr');

        if ($filas->length == 0) return [];

        //Las 2 primeras filas son el titulos y la última es el total
        for ($i = 2; $i < $filas->length - 1; $i++) {
            $fila = $filas[$i];

            $columnas = $fila->getElementsByTagName('td');
            if ($columnas->length == 0) continue;

            $documento = [];
            foreach($columnas as $index => $columna) {
                if ($index == 0) continue;//link boleta pdf

                $celda = trim($columna->nodeValue);
                $key = array_keys($this->fields_configuration)[$index-1];
                $documento[$key] = $this->sanatize($this->fields_configuration[$key], $celda);
            }

            $documentos[] = $documento;
        }

        return $documentos;
    }

    private function sanatize(string $_type, string $_value)
    {
        $value = trim($_value);

        if ($_type === 'int') return (int) str_replace('.', '', $value);
        if ($_type === 'date') return DateTimeImmutable::createFromFormat('d-m-Y',$value);

        return $value;
    }
}
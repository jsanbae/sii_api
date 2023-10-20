<?php

namespace Jsanbae\SIIAPI\DomParser;

use DOMNode;
use DOMDocument;
use DOMNodeList;
use DateTimeImmutable;

class BTEInformeEmitidasSummaryParser
{
    private $body;

    public function __construct(string $_body)
    {
        $this->body = $_body;
    }

    public function __invoke():array
    {
        return $this->BTEInformeEmitidasSummaryParser();
    }

    private function BTEInformeEmitidasSummaryParser():array
    {
        $summary = ['total' => 0, 'vigentes' => 0, 'anuladas' => 0, 'current_page' => 1, 'total_pages' => 1];

        $doc = new DOMDocument();
        $doc->loadHtml($this->body);

        $tables = $doc->getElementsByTagName('table');
        if ($tables->length == 0) return 0;

        $table = $tables[2];// totales

        $filas = $table->getElementsByTagName('tr');
      
        if ($filas->length == 0) return 0;

        $fila = $filas[0];

        $columnas = $fila->getElementsByTagName('td');
        
        if ($columnas->length == 0) return [];
        
        $summary['total'] = (int) explode(':', $columnas[0]->nodeValue)[1];
        $summary['vigentes'] = (int) explode(':', $columnas[1]->nodeValue)[1];
        $summary['anuladas'] = (int) explode(':', $columnas[2]->nodeValue)[1];
        
        preg_match_all('/\d+/', $columnas[3]->nodeValue,  $matches);

        $summary['current_page'] = (int) $matches[0][0];
        $summary['total_pages'] = (int) $matches[0][1];

        return $summary;
    }

}
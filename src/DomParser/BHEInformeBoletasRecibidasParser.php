<?php

namespace Jsanbae\SIIAPI\DomParser;

use DOMNode;
use DOMDocument;
use DOMNodeList;

class BHEInformeBoletasRecibidasParser
{
    private $body;
    private $fields_configuration = [
        'nroboleta' => "int", 
        'rutemisor' => "int", 
        'dvemisor' => "string", 
        'nombre_emisor' => "string", 
        'fecha_boleta' => "date", 
        'totalhonorarios' => "int", 
        'retencion_receptor' => "int", 
        'honorariosliquidos' => "int", 
        'es_soc_profesional' => "string", 
        'cod_comuna' => "int", 
        'estado' => "string", 
        'fechaanulacion' => "date", 
        'codigobarras' => "string",
    ];

    public function __construct(string $_body)
    {
        $this->body = $_body;
    }

    public function __invoke():array
    {
        return $this->BHEInformeBoletasRecibidasParser();
    }

    public function BHEInformeBoletasRecibidasParser():array
    {
        $doc = new DOMDocument();
        $doc->loadHtml($this->body);

        $scripts = $doc->getElementsByTagName('script');
        if ($scripts->length == 0) return [];

        $script = $this->findScript($scripts);
        $script_str = $script->nodeValue;
        $script_lines = explode("\n", $script_str);

        $boletas = [];
        $current_boleta_index = 0;
        foreach ($script_lines as $line) {
            if (strpos($line, 'nroboleta_') !== false) {
                $boleta = [];
                $current_boleta_index++;
            }

            if ($current_boleta_index < 1) continue;
            
            $boleta = $this->extractDatafromLine($line, $boleta);
            
            if (count($boleta) === count($this->fields_configuration)) {
                $boletas[] = $boleta;
                $boleta = [];
            }
        }

        return $boletas;
    }

    private function extractDatafromLine(string $_data_line, array $_boleta = []):array
    {
        $attributes = array_keys($this->fields_configuration);
        
        if (!preg_match('/' . implode('|', $attributes) . '/i', $_data_line, $matches)) return $_boleta;

        [$clave, $valor] = explode('=', $_data_line);
        
        $boleta_key = $matches[0];
        $sanitized_value = trim(str_replace(['"', ';', '\'','(',')','formatMiles',',','.'], '', $valor));

        if ($this->fields_configuration[$boleta_key] === "int" && !empty($sanitized_value)) $sanitized_value = (int) $sanitized_value;
        // if ($this->fields_configuration[$boleta_key] === "date" && !empty($sanitized_value)) $sanitized_value = new \DateTimeImmutable($sanitized_value);
        if ($this->fields_configuration[$boleta_key] === "string" && !empty($sanitized_value)) $sanitized_value = (string) $sanitized_value;
        if (empty($sanitized_value)) $sanitized_value = null;

        $_boleta = array_merge($_boleta, [$boleta_key => $sanitized_value]);

        return $_boleta;
    }

    // Obtiene del DOM el script correcto que se genera la vista
    private function findScript(DOMNodeList $_scripts): ?DOMNode
    {
        
        foreach ($_scripts as $index => $script) {
            $script_str = $script->nodeValue;

            if (strpos($script_str, 'nroboleta_') !== false) return $script;

            $script = null;
        }

        return $script;
    }

    private function sanitizeField($_field)
    {

    }

}

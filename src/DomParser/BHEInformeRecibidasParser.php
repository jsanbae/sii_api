<?php

namespace Jsanbae\SIIAPI\DomParser;

use DOMNode;
use DOMDocument;
use DOMNodeList;
use DateTimeImmutable;

class BHEInformeRecibidasParser
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

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    public function __invoke():array
    {
        $boletas = $this->BHEInformeRecibidasParser();
        
        $boletas_formatted = [];
        foreach ($boletas as $boleta) {
            $boletas_formatted[] = $this->formatBoleta($boleta);
        }

        return $boletas_formatted;
    }


    /**
     * Obtiene las boletas del informe de recibidas
     * desde el body del html que se obtiene de la respuesta de la API
     * 
     * arr_informe_mensual['nroboleta_1']            =       "135554";
     * arr_informe_mensual['rutemisor_1']            =       "12883789";
     * arr_informe_mensual['dvemisor_1']             =       "2";
     * arr_informe_mensual['nombre_emisor_1']                =       "LUIS IGNACIO MANQUEHUAL MERY";
     * arr_informe_mensual['fecha_boleta_1']         =       "25/09/2025";
     * arr_informe_mensual['totalhonorarios_1']      =       formatMiles("25000",'.');
     * arr_informe_mensual['honorariosliquidos_1']   =       formatMiles("25000",'.');
     * arr_informe_mensual['es_soc_profesional_1']   =       "NO";
     * arr_informe_mensual['cod_comuna_1']           =       "13101";
     * arr_informe_mensual['retencion_receptor_1']   =       formatMiles("0",'.');
     * arr_informe_mensual['estado_1']               =       "N";      
     * arr_informe_mensual['fechaanulacion_1']               =       " ";        
     * arr_informe_mensual['codigobarras_1']         =       "12883789135554A051FC";  
     * 
     * @return array
     */
    public function BHEInformeRecibidasParser():array
    {
        $boletas = [];
    
        // Dividir por líneas
        $lineas = explode("\n", $this->body);
        $regex = '/arr_informe_mensual\[\'([a-zA-Z0-9_]+)\'\]\s*=\s*(.*?);$/';
        // $regex = "/arr_informe_mensual\[\s*'([a-z_]+?)_(\d+)'\s*]\s*=\s*(.+?);/i";

        foreach ($lineas as $linea) {
            $linea = trim($linea);
            
            // Buscar asignaciones de arr_informe_mensual
            if (preg_match($regex, $linea, $match)) {
                $claveCompleta = $match[1];
                $valorRaw = trim($match[2]);
                
                // Extraer el nombre del campo y el número de fila
                if (preg_match('/^([a-z_]+)_(\d+)$/', $claveCompleta, $claveMatch)) {
                    $campo = $claveMatch[1];  // Ej: 'nroboleta'
                    $fila = $claveMatch[2];   // Ej: '1'
                    
                    // Procesar diferentes formatos de valores
                    if (preg_match('/^formatMiles\("([^"]*)",\'\.\'\)$/', $valorRaw, $valorMatch)) {
                        $valor = $valorMatch[1];
                    } elseif (preg_match('/^"([^"]*)"$/', $valorRaw, $valorMatch)) {
                        $valor = $valorMatch[1];
                    } elseif ($valorRaw === '""') {
                        $valor = '';
                    } else {
                        $valor = $valorRaw;
                    }
                    
                    // Crear la estructura anidada
                    if (!isset($boletas[$fila])) {
                        $boletas[$fila] = [];
                    }
                    
                    $boletas[$fila][$campo] = trim($valor);
                }
            }
        }

        return $boletas;
    }

    /**
     * Formatea la boleta según la configuración de campos
     * @param array $boleta
     * @return array
     */
    private function formatBoleta(array $boleta = []): array
    {
        $formatted_boleta = [];

        foreach ($this->fields_configuration as $key => $type) {
            $formatted_boleta[$key] = $this->applyFormatValue($type, $boleta[$key]);

            if (empty($formatted_boleta[$key]) && $formatted_boleta[$key] != "0") $formatted_boleta[$key] = null;
        }

        return $formatted_boleta;
    }

    /**
     * Formatea el valor de la boleta según el tipo de dato
     * @param string $type
     * @param string $value
     * @return mixed
     */
    private function applyFormatValue(string $type, string $value): mixed
    {
        if ($type === "int") return (int) $value;
        if ($type === "date") return DateTimeImmutable::createFromFormat('d/m/Y',$value);
        if ($type === "string") return (string) $value;
        if (empty($value) && $value != "0") $value = null;

        return $value;
    }

}

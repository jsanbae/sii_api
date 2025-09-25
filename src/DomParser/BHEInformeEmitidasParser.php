<?php

namespace Jsanbae\SIIAPI\DomParser;

use DOMNode;
use DOMDocument;
use DOMNodeList;
use DateTimeImmutable;

class BHEInformeEmitidasParser
{
    private $body;
    private $fields_configuration = [
        'nroboleta' => "int", 
        'usuemisor' => "string",
        'fechaemision' => "date", 
        'rutreceptor' => "string", 
        'dvreceptor' => "string", 
        'nombrereceptor' => "string", 
        'totalhonorarios' => "int", 
        'retencion_emisor' => "int",
        'retencion_receptor' => "int",
        'honorariosliquidos' => "int", 
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
        $boletas = $this->BHEInformeEmitidasParser();
        
        $boletas_formatted = [];
        foreach ($boletas as $boleta) {
            $boletas_formatted[] = $this->formatBoleta($boleta);
        }

        return $boletas_formatted;
    }

    /**
     * Obtiene las boletas del informe de emitidas
     * desde el body del html que se obtiene de la respuesta de la API
     * 
     * arr_informe_mensual[\'nroboleta_1\']            =        "999";
     * arr_informe_mensual[\'usuemisor_1\']            =        "JUANITO PEREZ";
     * arr_informe_mensual[\'fechaemision_1\']         =        "26/08/2025";
     * arr_informe_mensual[\'rutreceptor_1\']          =        "12345678";
     * arr_informe_mensual[\'dvreceptor_1\']           =        "2";
     * arr_informe_mensual[\'nombrereceptor_1\']       =        "RECEPTOR S A";
     * arr_informe_mensual[\'fecha_boleta_1\']         =        "26/08/2025";
     * arr_informe_mensual[\'totalhonorarios_1\']      =        formatMiles("384879",\'.\');
     * arr_informe_mensual[\'es_soc_profesional_1\']   =        "NO";
     * arr_informe_mensual[\'email_envio_1\']          =        "juanito@gmail.com";
     * arr_informe_mensual[\'retencion_emisor_1\']     =        formatMiles("0",\'.\');
     * arr_informe_mensual[\'retencion_receptor_1\']   =        formatMiles("55807",\'.\');
     * arr_informe_mensual[\'honorariosliquidos_1\']   =        formatMiles("329072",\'.\');
     * arr_informe_mensual[\'estado_1\']               =        "S";
     * arr_informe_mensual[\'fechaanulacion_1\']       =        "27/08/2025";
     * arr_informe_mensual[\'codigobarras_1\']         =        "99999999999999999999";
     * 
     * @return array
     */
    public function BHEInformeEmitidasParser(): array
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

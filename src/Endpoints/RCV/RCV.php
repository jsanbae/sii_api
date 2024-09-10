<?php

namespace Jsanbae\SIIAPI\Endpoints\RCV;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\Constants\Auth as AuthConstants;
use Jsanbae\SIIAPI\Constants\RCVType;
use Jsanbae\SIIAPI\Constants\RCV as RCVConstants;
use Jsanbae\SIIAPI\Contracts\Endpoint;
use Jsanbae\SIIAPI\Exceptions\ConnectionErrorException;
use Jsanbae\SIIAPI\Exceptions\UnauthorizedResourceException;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Cookie\CookieJar;
use \Psr\Http\Message\ResponseInterface;

abstract class RCV implements Endpoint
{
    private $credential;
    private $csessionid;
    private $auth_cookies_jar;
    private $type;

    public function __construct(APICredential $_credential, CookieJar $_auth_cookies_jar, string $_type)
    {
        $this->credential = $_credential;
        $this->auth_cookies_jar = $_auth_cookies_jar;
        $this->csessionid = $_auth_cookies_jar->getCookieByName(AuthConstants::CSESSIONID_COOKIE_NAME)->getValue();
        $this->type = $_type;
    }

    public function LibroResumen(int $_periodo, int $_mes)
    {
        $client = new Client(['cookies' => true, 'verify' => false]);
        
        $mes = str_pad($_mes, 2, '0', STR_PAD_LEFT);

        $periodo_tributario = $_periodo.$mes;

        $response = $client->request('POST', RCVConstants::RCV_RESUMEN_ENDPOINT, [
            RequestOptions::COOKIES => $this->auth_cookies_jar,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'es-CL,es;q=0.8,en-US;q=0.5,en;q=0.3',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
                'Referer' => RCVConstants::RCV_REFERER,
                'Origin' =>  RCVConstants::RCV_ORIGIN,
                'Connection' => 'keep-alive'
            ],
            RequestOptions::JSON => [
                'data' => [
                    'rutEmisor' => $this->credential->getUser(),
                    'dvEmisor' => strtoupper($this->credential->attributes()->getByName('dv')),
                    "ptributario" => $periodo_tributario,
                    "estadoContab" => "REGISTRO",
                    "operacion" => $this->type,
                    "busquedaInicial" => true
                ],
                'metaData' => [
                    'namespace' => RCVConstants::RCV_RESUMEN_NAMESPACE,
                    'conversationId' => $this->csessionid,
                    'transactionId' => 'd31de60e-1b42-43ef-ad3f-a7389f5cd61e',
                    'page' => null
                ]
            ],
        ]);

        if ($response->getStatusCode() === 401) throw new UnauthorizedResourceException("No se pudo acceder al recurso por no estar autorizado");
        if ($response->getStatusCode() !== 200) throw new ConnectionErrorException("No se pudo conectar al recurso " . $response->getStatusCode());

        return $response;
    }

    public function LibroDetalleByDocType(int $_tipo_doc, int $_periodo, int $_mes): ResponseInterface
    {
        $client = new Client(['cookies' => true, 'verify' => false]);
        
        $_mes = str_pad($_mes, 2, '0', STR_PAD_LEFT);
        
        $periodo_tributario = $_periodo.$_mes;

        if ($this->type == RCVType::COMPRA) {
            $namespace = RCVConstants::LIBRO_COMPRAS_DETALLE_NAMESPACE;
            $endpoint = RCVConstants::LIBRO_COMPRAS_DETALLE_ENDPOINT;
        } 
        
        if ($this->type == RCVType::VENTA) {
            $namespace = RCVConstants::LIBRO_VENTAS_DETALLE_NAMESPACE;
            $endpoint = RCVConstants::LIBRO_VENTAS_DETALLE_ENDPOINT;
        }
        
        if (!isset($namespace)) throw new \InvalidArgumentException("Tipo de libro " . $this->type . " no soportado");

        $response = $client->request('POST', $endpoint, [
            RequestOptions::COOKIES => $this->auth_cookies_jar,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'es-CL,es;q=0.8,en-US;q=0.5,en;q=0.3',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
                'Referer' => RCVConstants::RCV_REFERER,
                'Origin' =>  RCVConstants::RCV_ORIGIN,
                'Connection' => 'keep-alive'
            ],
            RequestOptions::JSON => [
                'data' => [
                    'rutEmisor' => $this->credential->getUser(),
                    'dvEmisor' => $this->credential->attributes()->getByName('dv'),
                    "ptributario" => $periodo_tributario,
                    "estadoContab" => "REGISTRO",
                    "operacion" => $this->type,
                    // "estadoContab" => "",
                    // "operacion" => "",
                    "codTipoDoc" => $_tipo_doc
                ],
                'metaData' => [
                    'namespace' => $namespace,
                    'conversationId' => $this->csessionid,
                    'transactionId' => '2ffcbf82-29bc-42f9-8b31-9bb851dcb3d3',
                    'page' => null
                ]
            ],
        ]);

        if ($response->getStatusCode() === 401) throw new UnauthorizedResourceException("No se pudo acceder al recurso por no estar autorizado");
        if ($response->getStatusCode() !== 200) throw new ConnectionErrorException("No se pudo conectar al recurso " . $response->getStatusCode());

        return $response;
    }

}

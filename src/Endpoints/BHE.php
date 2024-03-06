<?php

namespace Jsanbae\SIIAPI\Endpoints;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\Contracts\Endpoint;
use Jsanbae\SIIAPI\Constants\BHE as BHEConstants;
use Jsanbae\SIIAPI\Constants\Auth as AuthConstants;
use Jsanbae\SIIAPI\Exceptions\AuthenticationFailedException;
use Jsanbae\SIIAPI\Exceptions\UnauthorizedResourceException;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Cookie\CookieJar;
use Psr\Http\Message\ResponseInterface;

class BHE implements Endpoint
{
    private $credential;
    private $auth_cookies_jar;
    private $csessionid;

    public function __construct(APICredential $_crendential, CookieJar $_auth_cookies_jar)
    {
        $this->credential = $_crendential;
        $this->auth_cookies_jar = $_auth_cookies_jar;
        $this->csessionid = $_auth_cookies_jar->getCookieByName(AuthConstants::CSESSIONID_COOKIE_NAME)->getValue();
    }

    public function InformeBoletasRecibidas(int $_periodo, int $_mes): ResponseInterface
    {
        $rut = $this->credential->getUser();
        $dv = strtoupper($this->credential->attributes()->getByName('dv'));

        $endpoint = "https://loa.sii.cl/cgi_IMT/TMBCOC_InformeMensualBheRec.cgi?rut_arrastre=$rut&dv_arrastre=$dv&cbanoinformemensual=$_periodo&cbmesinformemensual=$_mes&pagina_solicitada=0";
        $referer = "https://loa.sii.cl/cgi_IMT/TMBCOC_InformeAnualBheRec.cgi?rut_arrastre=$rut&dv_arrastre=dv&cbanoinformeanual=$_periodo";

        $client = new Client(['cookies' => true, 'verify' => false]);

        $response = $client->request('GET', $endpoint, [
            RequestOptions::COOKIES => $this->auth_cookies_jar,
            RequestOptions::HEADERS => [
                'Content-Type' => 'text/html',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'es-CL,es;q=0.8,en-US;q=0.5,en;q=0.3',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
                'Referer' => $referer,
                // 'Referer' => BHEConstants::BHE_REFERER,
                // 'Origin' =>  BHEConstants::BHE_ORIGIN,
                'Connection' => 'keep-alive'
            ],
        ]);

        if ($response->getStatusCode() == 401) throw new UnauthorizedResourceException("No se pudo acceder al recurso por no estar autorizado");

        return $response;
    }

    public function BoletaRecibidaPDF(string $_codigo_barras, string $_cod39, string $_nombre_comuna): ResponseInterface 
    {
        $cookie_jar = new CookieJar();
        $client = new Client(['cookies' => $cookie_jar, 'verify' => false]);

        $response = $client->request('POST', 'https://loa.sii.cl/cgi_IMT/TMBCOT_ConsultaBoletaPdf.cgi', [
            RequestOptions::COOKIES => $this->auth_cookies_jar,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
                'Accept-Language' => 'es-CL,es;q=0.8,en-US;q=0.5,en;q=0.3',
                'Origin' => 'https://loa.sii.cl',
                'Referer' => 'https://loa.sii.cl/cgi_IMT/TMBCOC_InformeMensualBheRec.cgi?cbanoinformemensual=2023&cbmesinformemensual=07&dv_arrastre=8&pagina_solicitada=0&rut_arrastre=76050762',
            ],
            RequestOptions::FORM_PARAMS => [
                'origen' => 'RECIBIDOS',
                'txt_codigobarras' => $_codigo_barras,
                'txt_cod_39' => $_cod39,
                'txt_descr_comuna' => $_nombre_comuna,
                'veroriginal' => 'si',
                'nro_boleta' => 0,
                'CantidadFilas' => 13
            ],
        ]);
    
        if ($response->getStatusCode() != 200) throw new AuthenticationFailedException("Error al autenticar (" . $response->getStatusCode() . "), favor revise sus credenciales.");
    
        $this->auth_cookies_jar = $cookie_jar;
    
        return $response;
    }

    public function  InformeBTEEmitidas(int $_periodo, int $_mes, int $_pagina = 1): ResponseInterface
    {
        $client = new Client(['cookies' => true, 'verify' => false]);
        
        if ($_mes < 10) $_mes = '0' . $_mes;
        
        $endpoint = "https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons2?DIA=1&MESM=$_mes&ANOM=$_periodo&TIPO=mensual&AUTEN=RUTCLAVE&CNTR=1&PAGINA=$_pagina";
        $referer = "https://zeus.sii.cl/cvc_cgi/bte/bte_indiv_cons2";

        $response = $client->request('GET', $endpoint, [
            RequestOptions::COOKIES => $this->auth_cookies_jar,
            RequestOptions::HEADERS => [
                'Content-Type' => 'text/html',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'es-CL,es;q=0.8,en-US;q=0.5,en;q=0.3',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
                'Referer' => $referer,
                'Connection' => 'keep-alive'
            ],
        ]);

        if ($response->getStatusCode() == 401) throw new UnauthorizedResourceException("No se pudo acceder al recurso por no estar autorizado");

        return $response;     
    }
}

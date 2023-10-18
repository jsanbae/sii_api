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
        $dv = $this->credential->attributes()->getByName('dv');

        $endpoint = "https://loa.sii.cl/cgi_IMT/TMBCOC_InformeMensualBheRec.cgi?rut_arrastre=$rut&dv_arrastre=$dv&cbanoinformemensual=$_periodo&cbmesinformemensual=$_mes&pagina_solicitada=0";
        $referer = "https://loa.sii.cl/cgi_IMT/TMBCOC_InformeAnualBheRec.cgi?rut_arrastre=$rut&dv_arrastre=dv&cbanoinformeanual=$_periodo";

        $client = new Client(['cookies' => true]);

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
        $client = new Client(['cookies' => $cookie_jar]);
        
//         curl "https://loa.sii.cl/cgi_IMT/TMBCOT_ConsultaBoletaPdf.cgi" 
// -X POST 
// -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0" 
// -H "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8" 
// -H "Accept-Language: es-CL,es;q=0.8,en-US;q=0.5,en;q=0.3" 
// -H "Accept-Encoding: gzip, deflate, br" 
// -H "Content-Type: application/x-www-form-urlencoded" 
// -H "Origin: https://loa.sii.cl" 
// -H "Connection: keep-alive" 
// -H "Referer: https://loa.sii.cl/cgi_IMT/TMBCOC_InformeMensualBheRec.cgi?cbanoinformemensual=2023&cbmesinformemensual=05&dv_arrastre=1&pagina_solicitada=0&rut_arrastre=96750760" 
// -H "Cookie: dtCookie=v_4_srv_42_sn_4F8EFC7F9628D94F93B78378D18BBF4F_perc_100000_ol_0_mul_1_app-3Aea7c4b59f27d43eb_0_app-3A0089562635ebe3da_0; s_cc=true; s_sq=siiprd^%^3D^%^2526c.^%^2526a.^%^2526activitymap.^%^2526page^%^253Dhttps^%^25253A^%^25252F^%^25252Fwww.sii.cl^%^25252Fservicios_online^%^25252F1040-1287.html^%^2526link^%^253DConsultar^%^252520boletas^%^252520recibidas^%^2526region^%^253DcollapseTwo^%^2526.activitymap^%^2526.a^%^2526.c^%^2526pid^%^253Dhttps^%^25253A^%^25252F^%^25252Fwww.sii.cl^%^25252Fservicios_online^%^25252F1040-1287.html^%^2526oid^%^253Djavascript^%^25253AlinkVisita^%^252528^%^252527https^%^25253A^%^25252F^%^25252Floa.sii.cl^%^25252Fcgi_IMT^%^25252FTMBCOC_MenuConsultasContribRec.cgi^%^25253Fdummy^%^25253D146194324^%^2526ot^%^253DA; TOKEN=TOGBTWSRH8ZCO; NETSCAPE_LIVEWIRE.rcmp=76949548; NETSCAPE_LIVEWIRE.dcmp=7; NETSCAPE_LIVEWIRE.rut=96750760; NETSCAPE_LIVEWIRE.rutm=96750760; NETSCAPE_LIVEWIRE.dv=1; NETSCAPE_LIVEWIRE.dvm=1; NETSCAPE_LIVEWIRE.clave=SIMZWMRapOsh2SIQi.wYYkZhGk; NETSCAPE_LIVEWIRE.mac=1m8nus0kft07pogd6vvjkgssvv; NETSCAPE_LIVEWIRE.exp=20231017143520; NETSCAPE_LIVEWIRE.sec=0000; NETSCAPE_LIVEWIRE.lms=120; CSESSIONID=TOGBTWSRH8ZCO; RUT_NS=96750760; DV_NS=1; NETSCAPE_LIVEWIRE.locexp=Tue^%^2C^%^2017^%^20Oct^%^202023^%^2017^%^3A35^%^3A19^%^20GMT; NETSCAPE_LIVEWIRE.ult=Tue Oct 17 2023 12:52:50 GMT-0300 (hora de verano de Chile)" 
// -H "Upgrade-Insecure-Requests: 1" 
// -H "Sec-Fetch-Dest: document" 
// -H "Sec-Fetch-Mode: navigate" 
// -H "Sec-Fetch-Site: same-origin" 
// -H "Sec-Fetch-User: ?1" 
// -H "Pragma: no-cache" 
// -H "Cache-Control: no-cache" 
// --data-raw "origen=RECIBIDOS&txt_codigobarras=0854360000059D8B6488&veroriginal=si&txt_cod_39=1000101110111010101000111011101011101000101110101110100011101010101000111010111011101110001010101011100011101010101000111011101010100011101110101010001110111010101000111011101010100011101110101110100011101010101110001011101010101110001011101110100010111010101110100010111010111000111010101010001110101110111010001011101011101000101110101000101110111010&txt_descr_comuna=PENAFLOR&nro_boleta=0&CantidadFilas=2"


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
}

<?php

namespace Jsanbae\SIIAPI;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\Endpoints\BHE;
use Jsanbae\SIIAPI\Endpoints\Auth;
use Jsanbae\SIIAPI\Services\Service;
use Jsanbae\SIIAPI\Services\BHEService;
use Jsanbae\SIIAPI\Endpoints\RCV\Ventas;
use Jsanbae\SIIAPI\Endpoints\RCV\Compras;
use Jsanbae\SIIAPI\Services\VentaService;
use Jsanbae\SIIAPI\Services\CompraService;

class SIIAPI
{
    private $credential;
    private $token_captcha;
    private $auth_cookies_jar;

    public function __construct(APICredential $_credential, ?string $_token_captcha =  null)
    {
        $this->credential = $_credential;
        $this->token_captcha = $_token_captcha;
        $this->auth_cookies_jar = (new Auth($this->credential))->getAuthCookiesJar();
    }

    /**
     * Servicio de RCV de compras
     * 
     * @return Service
     */
    public function Compras():Service
    {
        $this->checkTokenCaptcha();
        $endpoint = new Compras($this->credential, $this->auth_cookies_jar, $this->token_captcha);
        
        return new CompraService($endpoint);
    }

    /**
     * Servicio de RCV de ventas
     * 
     * @return Service
     */
    public function Ventas():Service
    {
        $this->checkTokenCaptcha();
        $endpoint = new Ventas($this->credential, $this->auth_cookies_jar, $this->token_captcha);
        
        return new VentaService($endpoint);
    }

    /**
     * Servicio de Boletas Honorarios Electrónicas
     * 
     * @return Service
     */
    public function BHE():Service
    {
        $endpoint = new BHE($this->credential, $this->auth_cookies_jar);
        
        return new BHEService($endpoint);
    }

    /**
     * Verifica si el token de captcha está configurado
     * 
     * @throws \Exception
     */
    private function checkTokenCaptcha(): void
    {
        if (is_null($this->token_captcha)) {
            throw new \Exception('Token de captcha no configurado.');
        }
    }
}

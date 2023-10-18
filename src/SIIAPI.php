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
    private $auth_cookies_jar;

    public function __construct(APICredential $_credential)
    {
        $this->credential = $_credential;
        $this->auth_cookies_jar = (new Auth($this->credential))->getAuthCookiesJar();
    }

    public function Compras():Service
    {
        $endpoint = new Compras($this->credential, $this->auth_cookies_jar);
        
        return new CompraService($endpoint);
    }

    public function Ventas():Service
    {
        $endpoint = new Ventas($this->credential, $this->auth_cookies_jar);
        
        return new VentaService($endpoint);
    }

    public function BHE():Service
    {
        $endpoint = new BHE($this->credential, $this->auth_cookies_jar);
        
        return new BHEService($endpoint);
    }
}

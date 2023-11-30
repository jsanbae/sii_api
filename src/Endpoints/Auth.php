<?php

namespace Jsanbae\SIIAPI\Endpoints;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\Contracts\Endpoint;
use Jsanbae\SIIAPI\Constants\Auth as AuthConstants;
use Jsanbae\SIIAPI\Exceptions\AuthenticationFailedException;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Cookie\CookieJar;
use Psr\Http\Message\ResponseInterface;

class Auth implements Endpoint
{
    private $crendential;
    private $auth_cookies_jar;
    public function __construct(APICredential $_credential)
    {
        // $this->crendential = ['rut' => 96750760, 'dv' => 1, 'clave' => 'LRV96750'];
        $this->crendential = $_credential;
    }

    public function getAuthCookiesJar():CookieJar
    {
        if (isset($this->auth_cookies_jar)) return $this->auth_cookies_jar;

        $this->Login();

        return $this->auth_cookies_jar;
    }

    public function Login(): ResponseInterface 
    {
        $cookie_jar = new CookieJar();
        $client = new Client(['cookies' => $cookie_jar, 'verify' => false]);
        
        $response = $client->request('POST', AuthConstants::LOGIN_ENDPOINT, [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
                'Accept-Language' => 'es-CL,es;q=0.8,en-US;q=0.5,en;q=0.3',
                'Referer' => AuthConstants::LOGIN_ENDPOINT,
            ],
            RequestOptions::FORM_PARAMS => [
                'rut' => $this->crendential->getUser(),
                'dv' => strtoupper($this->crendential->attributes()->getByName('dv')),
                'clave' => $this->crendential->getPassword(),
                'referencia' => AuthConstants::LOGIN_REFERENCE,
                'code' => AuthConstants::LOGIN_CODE,
            ],
        ]);

        if ($response->getStatusCode() != 200) throw new AuthenticationFailedException("Error al autenticar (" . $response->getStatusCode() . "), favor revise sus credenciales.");

        $this->auth_cookies_jar = $cookie_jar;

        return $response;
    }

    public function Logout()
    {

    }
}

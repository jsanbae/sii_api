<?php

namespace Jsanbae\SIIAPI\Endpoints;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\Contracts\Endpoint;
use Jsanbae\SIIAPI\Constants\Auth as AuthConstants;
use Jsanbae\SIIAPI\Exceptions\AuthenticationFailedException;
use Jsanbae\SIIAPI\Exceptions\ConnectionErrorException;

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
        $this->crendential = $_credential;
    }

    /**
     * Construye los parámetros de login con soporte para claves vacías
     * 
     * @param array $additionalParams Parámetros adicionales opcionales
     * @return array
     */
    private function buildLoginParams(array $additionalParams = []): array
    {
        $params = [
            'rut' => $this->crendential->getUsername(),
            'dv' => strtoupper($this->crendential->attributes()->getByName('dv')),
            'clave' => $this->crendential->getPassword(),
            'referencia' => AuthConstants::LOGIN_REFERENCE,
            // 'code' => AuthConstants::LOGIN_CODE,
        ];

        // Agregar parámetros adicionales (incluyendo claves con valores vacíos)
        foreach ($additionalParams as $key => $value) {
            $params[$key] = $value;
        }

        return $params;
    }

    public function getAuthCookiesJar(): CookieJar
    {
        if (isset($this->auth_cookies_jar)) return $this->auth_cookies_jar;

        $this->Login();

        return $this->auth_cookies_jar;
    }

    public function Login(): ResponseInterface 
    {
        $cookie_jar = new CookieJar();
        $client = new Client(['cookies' => $cookie_jar, 'verify' => false]);
        
        try {
            $response = $client->request('POST', AuthConstants::LOGIN_ENDPOINT, [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                    'Accept-Encoding' => 'gzip, deflate, br, zstd',
                    'Accept-Language' => 'es-US,es-419;q=0.9,es;q=0.8,en;q=0.7',
                    'Cache-Control' => 'max-age=0',
                    'Origin' => 'https://zeusr.sii.cl',
                    'Referer' => 'https://zeusr.sii.cl//AUT2000/InicioAutenticacion/IngresoRutClave.html?https://misiir.sii.cl/cgi_misii/siihome.cgi',
                    'Sec-Fetch-Dest' => 'document',
                    'Sec-Fetch-Mode' => 'navigate',
                    'Sec-Fetch-Site' => 'same-origin',
                    'Sec-Fetch-User' => '?1',
                    'Upgrade-Insecure-Requests' => '1',
                    'sec-ch-ua' => '"Google Chrome";v="141", "Not?A_Brand";v="8", "Chromium";v="141"',
                    'sec-ch-ua-mobile' => '?0',
                    'sec-ch-ua-platform' => '"Windows"'
                ],
                RequestOptions::FORM_PARAMS => $this->buildLoginParams([
                    AuthConstants::LOGIN_CODE => '', // Clave con valor vacío
                    'rutcntr' => $this->crendential->getUsername() . '-' . strtoupper($this->crendential->attributes()->getByName('dv')),
                ]),
            ]);
        } catch (\Throwable $t) {
            throw new ConnectionErrorException("No se pudo conectar al recurso " . $t->getMessage());
        }

        $content = $response->getBody()->getContents();

        if ($response->getStatusCode() != 200) {
            throw new AuthenticationFailedException("Error al autenticar (" . $response->getStatusCode() . "), favor revise sus credenciales.");
        } 

        if (strpos($content, 'Por el momento no se puede responder a sus requerimientos. Por favor, inténtelo más tarde.') !== false)  {
            throw new AuthenticationFailedException("Error al autenticar (503), Por el momento no se puede responder a sus requerimientos. Por favor, inténtelo más tarde.");
        }

        if (strpos($content, 'La Clave Tributaria ingresada no es correcta') !== false) {
            throw new AuthenticationFailedException(sprintf("Error al autenticar (401), favor revise sus credenciales. %s-%s %s", $this->crendential->getUsername(), strtoupper($this->crendential->attributes()->getByName('dv')), $this->crendential->getPassword()));
        }

        $this->auth_cookies_jar = $cookie_jar;
        return $response;
    }

    public function Logout()
    {

    }
}

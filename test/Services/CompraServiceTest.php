<?php

use Dotenv\Dotenv;
use Jsanbae\SIIAPI\SIIAPI;
use PHPUnit\Framework\TestCase;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\APICredentialAttributes;

class CompraServiceTest extends TestCase
{
    private $api_client;
    private $periodo;
    private $mes;

    public function setUp():void
    {
        parent::setUp();
        
        $dir = dirname(__DIR__) . '..\\..\\';

        $dotenv = Dotenv::createImmutable($dir);
        $dotenv->load();

        $api_rut = $_ENV['SII_API_RUT'];
        $api_dv = $_ENV['SII_API_DV'];
        $api_password = $_ENV['SII_API_PASSWORD'];

        $this->periodo = $_ENV['TEST_PERIODO'];
        $this->mes = $_ENV['TEST_MES'];

        $credential = new APICredential($api_rut, $api_password, new APICredentialAttributes(['dv' => $api_dv]));
        $token_captcha = $_ENV['SII_TOKEN_CAPTCHA'];
        $this->api_client = new SIIAPI($credential, $token_captcha);
    }

    public function test_libro()
    {
        $libro_compra = $this->api_client->Compras()->Libro($this->periodo, $this->mes);

        $libro_data = $libro_compra->toArray();
        fwrite(STDERR, print_r($libro_data, TRUE));

        $this->assertTrue(!$libro_compra->isEmpty());
    }	
}
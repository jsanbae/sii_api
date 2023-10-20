<?php

use Dotenv\Dotenv;
use Jsanbae\SIIAPI\SIIAPI;
use PHPUnit\Framework\TestCase;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\APICredentialAttributes;

class VentaServiceTest extends TestCase
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
        $this->api_client = new SIIAPI($credential);
    }

    public function test_libro()
    {
        $libro = $this->api_client->Ventas()->Libro($this->periodo, $this->mes);

        $libro_data = $libro->toArray();

        $this->assertFalse($libro->isEmpty());
    }	
}
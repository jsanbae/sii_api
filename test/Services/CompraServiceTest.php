<?php

use Dotenv\Dotenv;
use Jsanbae\SIIAPI\SIIAPI;
use PHPUnit\Framework\TestCase;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\APICredentialAttributes;

class CompraServiceTest extends TestCase
{
    private $api_client;

    public function setUp():void
    {
        parent::setUp();
        
        $dir = dirname(__DIR__) . '..\\..\\';

        $dotenv = Dotenv::createImmutable($dir);
        $dotenv->load();

        $api_rut = $_ENV['SII_API_RUT'];
        $api_dv = $_ENV['SII_API_DV'];
        $api_password = $_ENV['SII_API_PASSWORD'];

        $credential = new APICredential($api_rut, $api_password, new APICredentialAttributes(['dv' => $api_dv]));
        $this->api_client = new SIIAPI($credential);
    }

    public function test_libro()
    {
        $libro_compra = $this->api_client->Compras()->Libro(2023, 10);

        $libro_data = $libro_compra->toArray();

        $this->assertTrue(!$libro_compra->isEmpty());
    }	
}
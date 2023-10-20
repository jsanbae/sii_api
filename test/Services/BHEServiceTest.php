<?php

use Dotenv\Dotenv;
use Jsanbae\SIIAPI\SIIAPI;
use PHPUnit\Framework\TestCase;
use Jsanbae\SIIAPI\APICredential;

use Jsanbae\SIIAPI\Concerns\Comunas;
use Jsanbae\SIIAPI\APICredentialAttributes;

class BHEServiceTest extends TestCase
{
    private $api_client;
    private $periodo;
    private $mes;

    use Comunas;

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

    public function test_libro_honorarios()
    {
        $libro = $this->api_client->BHE()->Libro($this->periodo, $this->mes);

        $this->assertFalse($libro->isEmpty());
    }

    public function test_boletas_recibida_pdf()
    {
        $cod_comuna = "14504";
        $codigo_barra = "0854360000059D8B6488";
        // $cod_comuna = "2201";
        // $codigo_barra = '0945805501407639A19F';
        $ciudad = $this->getComunaByCode((int) $cod_comuna);
        $boleta_recibida_pdf_binary = $this->api_client->BHE()->BoletaRecibidaPDFBinary($codigo_barra, $ciudad);

        $file_name = 'test/outputs/boleta.pdf';
        file_put_contents($file_name, $boleta_recibida_pdf_binary);

        $this->assertTrue(file_exists($file_name));
    }
}
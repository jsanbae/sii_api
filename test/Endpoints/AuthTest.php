<?php

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\Endpoints\Auth;

use Jsanbae\SIIAPI\APICredentialAttributes;
use Jsanbae\SIIAPI\Constants\Auth as AuthConstants;

class AuthTest extends TestCase
{
    private $credential;
    public function setUp():void
    {
        parent::setUp();
        
        $dir = dirname(__DIR__) . '..\\..\\';

        $dotenv = Dotenv::createImmutable($dir);
        $dotenv->load();

        $api_rut = $_ENV['SII_API_RUT'];
        $api_dv = $_ENV['SII_API_DV'];
        $api_password = $_ENV['SII_API_PASSWORD'];

        $this->credential = new APICredential($api_rut, $api_password, new APICredentialAttributes(['dv' => $api_dv]));
    }

    public function test_login()
    {
        $response = (new Auth($this->credential))->Login();
        $headers = $response->getHeaders();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($headers);
    }

    public function test_get_cookies_jar()
    {
        $cookies_jar = (new Auth($this->credential))->getAuthCookiesJar();
        $csession_id = $cookies_jar->getCookieByName(AuthConstants::CSESSIONID_COOKIE_NAME)->getValue();
        
        $this->assertNotEmpty($csession_id);
    }

}
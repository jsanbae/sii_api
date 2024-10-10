<?php

namespace Jsanbae\SIIAPI\Endpoints\RCV;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\Constants\RCVType;
use Jsanbae\SIIAPI\Endpoints\RCV\RCV;

USE GuzzleHttp\Cookie\CookieJar;

class Ventas extends RCV
{

    public function __construct(APICredential $_credential, CookieJar $_auth_cookies_jar, $_token_captcha)
    {
        parent::__construct($_credential, $_auth_cookies_jar, RCVType::VENTA, $_token_captcha);
    }
}

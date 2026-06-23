<?php

namespace Jsanbae\SIIAPI\Endpoints\RCV;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\Constants\RCVType;
use Jsanbae\SIIAPI\Endpoints\RCV\RCV;

use GuzzleHttp\Cookie\CookieJar;

class Ventas extends RCV
{

    public function __construct(APICredential $_credential, CookieJar $_auth_cookies_jar, string $_token_captcha)
    {
        parent::__construct($_credential, $_auth_cookies_jar, RCVType::VENTA, $_token_captcha);
    }
}

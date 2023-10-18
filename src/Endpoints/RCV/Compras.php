<?php

namespace Jsanbae\SIIAPI\Endpoints\RCV;

use Jsanbae\SIIAPI\APICredential;
use Jsanbae\SIIAPI\Constants\RCVType;
use Jsanbae\SIIAPI\Endpoints\RCV\RCV;

use GuzzleHttp\Cookie\CookieJar;

class Compras extends RCV
{

    public function __construct(APICredential $_credential, CookieJar $_auth_cookies_jar)
    {
        parent::__construct($_credential, $_auth_cookies_jar, RCVType::COMPRA);
    }
}

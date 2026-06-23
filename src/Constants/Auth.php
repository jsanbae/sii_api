<?php

namespace Jsanbae\SIIAPI\Constants;

class Auth
{
    const SII_AUTH_COOKIES = 'SII_AUTH_COOKIES';
    const CSESSIONID_COOKIE_NAME = 'CSESSIONID';
    const LOGIN_ENDPOINT = "https://zeusr.sii.cl/cgi_AUT2000/CAutInicio.cgi";
    const LOGOUT_ENDPOINT = "https://zeusr.sii.cl/cgi_AUT2000/autTermino.cgi";
    const LOGOUT_REFERER = "https://www4.sii.cl/consdcvinternetui/";
    const LOGIN_CODE = 411;
    const LOGIN_REFERENCE = 'https://misiir.sii.cl/cgi_misii/siihome.cgi';
}

<?php

namespace Jsanbae\SIIAPI\Services;

use Jsanbae\SIIAPI\COntracts\Libro;

interface RCVService {
    public function Libro(int $_periodo, int $_mes): Libro;
}
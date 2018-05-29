<?php

namespace App;

class VersionScanner
{
    public function parseToken($versionToken)
    {
        return str_replace('-', '.', $versionToken);
    }
}

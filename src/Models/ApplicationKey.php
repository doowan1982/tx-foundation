<?php
namespace Tesoon\Foundation\Models;

class ApplicationKey extends Data
{
    /**
     * 中台KEY
     */
    const APPLICATION_TYPE = 1;

    /**
     * 通行证KEY
     */
    const PASSPORT_TYPE = 2;

    public $key;

    public $type = self::APPLICATION_TYPE;

}
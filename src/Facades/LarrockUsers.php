<?php

namespace Larrock\ComponentUsers\Facades;

use Illuminate\Support\Facades\Facade;

class LarrockUsers extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'larrockusers';
    }

}
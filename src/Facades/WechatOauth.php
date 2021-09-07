<?php

namespace Gxyshs\WechatOauth\Facades;

use Illuminate\Support\Facades\Facade;

class WechatOauth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wechatoauth';
    }
}

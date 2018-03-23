<?php
/**
 * Author: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2018/3/21
 * Time: 下午4:42
 */

namespace Firstphp\Jiayumsg\Facades;

use Illuminate\Support\Facades\Facade;

class JiayumsgFactory extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'JiayumsgService';
    }

}

